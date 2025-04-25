<?php
session_start();

// Handle login form submission
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
    $username_or_email = trim($_POST['username']);
    $password = $_POST['password'];

    // Query the database for user credentials
    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];

            // Redirect to valuation.html
            header("Location: valuation.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid Credentials.";
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
  <title>Login - EPG Global</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="card shadow-sm p-4" style="min-width: 350px; max-width: 400px;">
    <h3 class="text-center mb-3">Login to EPG Global</h3>

    <!-- Display error message if login fails -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Enter username or email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-success">Login</button>
      </div>
      <div class="text-center mt-3">
        <a href="#">Forgot password?</a>
      </div>
      <div class="text-center mt-2">
        <p>Don't have an account? <a href="register.php">Register</a></p>
      </div>
    </form>
  </div>

  <!-- JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>