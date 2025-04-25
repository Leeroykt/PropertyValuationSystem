<?php
session_start();

// Handle registration form submission
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
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new admin user into the database
    $stmt = $conn->prepare("INSERT INTO admin_user (fullname, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: epgglobal-admin.php");
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration - EPG Global</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="card shadow-sm p-4" style="min-width: 350px; max-width: 400px;">
    <h3 class="text-center mb-3">Admin Registration</h3>

    <!-- Display error message if registration fails -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form method="POST" action="">
      <div class="mb-3">
        <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-success">Register</button>
      </div>
      <div class="text-center mt-2">
        <p>Already have an account? <a href="admin_login.php">Login</a></p>
      </div>
    </form>
  </div>

  <!-- JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>