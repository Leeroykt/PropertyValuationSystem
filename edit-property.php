<?php
$host = 'localhost';
$db = 'epgglobal';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$title = $_POST['title'];
$location = $_POST['location'];
$price = $_POST['price'];
$status = $_POST['status'];

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageName = uniqid() . "_" . basename($_FILES["image"]["name"]);
    $targetDir = "uploads/";
    $imagePath = $targetDir . $imageName;
    move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);

    $stmt = $conn->prepare("UPDATE properties SET title=?, location=?, price=?, status=?, image=? WHERE id=?");
    $stmt->bind_param("ssdssi", $title, $location, $price, $status, $imagePath, $id);
} else {
    $stmt = $conn->prepare("UPDATE properties SET title=?, location=?, price=?, status=? WHERE id=?");
    $stmt->bind_param("ssdsi", $title, $location, $price, $status, $id);
}

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: properties.php");
exit;
