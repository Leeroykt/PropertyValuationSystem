<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id']; // Get the property ID

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

// Fetch the valuation record (assuming image column is named 'image_paths')
$stmt = $conn->prepare("SELECT image_paths FROM valuations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$valuation = $result->fetch_assoc();

// Check if image_paths is available
$image_paths = isset($valuation['image_paths']) ? explode(',', $valuation['image_paths']) : [];

$stmt->close();
$conn->close();

// Return images as JSON
echo json_encode(['images' => $image_paths]);
?>
