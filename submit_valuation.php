<?php
session_start();

// Handle valuation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database configuration
    $host = "localhost";
    $db = "epgglobal";
    $user = "root";
    $pass = "";

    // Connect to the database
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Collect and sanitize input
    $fullname = $_SESSION['fullname'];
    $address = trim($_POST['address']);
    $deed_number = trim($_POST['deed_number']);
    $purpose = trim($_POST['purpose']);
    $description = trim($_POST['description']);
    $stand_size = trim($_POST['stand_size']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);

    // Handle file uploads
    $image_paths = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['images']['name'][$key]);
            $target_path = "uploads/" . $file_name; // Ensure the uploads directory is writable
            if (move_uploaded_file($tmp_name, $target_path)) {
                $image_paths[] = $target_path;
            } else {
                error_log("Failed to upload file: " . $_FILES['images']['name'][$key]);
            }
        }
    }
    $image_paths_string = implode(',', $image_paths); // Store paths as a comma-separated string

    // Insert valuation request into the database
    $stmt = $conn->prepare("INSERT INTO valuations (fullname, address, deed_number, purpose, description, stand_size, contact_number, email, image_paths, requested_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssssss", $fullname, $address, $deed_number, $purpose, $description, $stand_size, $contact_number, $email, $image_paths_string);
    
    if ($stmt->execute()) {
        header("Location: valuation.php?success=1");
        exit;
    } else {
        error_log("Database error: " . $stmt->error); // Log the error
        header("Location: valuation.php?error=1");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>