<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // DB config
    $host = "localhost";
    $db = "epgglobal";
    $user = "root";
    $pass = "";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("DB Connection failed: " . $conn->connect_error);
    }

    // Collect and sanitize form inputs
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $interest = $_POST['property_interest'];

    // Simple validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password, property_interest) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $email, $phone, $hashed_password, $interest);

        if ($stmt->execute()) {
            $success = "Registration successful!";
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="card p-4 shadow" style="width: 400px;">
    <h3 class="text-center mb-4">Create an Account</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
      </div>
      <div class="mb-3">
        <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
      </div>
      <div class="mb-3">
        <select name="property_interest" class="form-select" required>
          <option value="" disabled selected>What are you interested in?</option>
          <option value="buyer">I am a Buyer</option>
          <option value="seller">I am a Seller</option>
          <option value="appraiser">I am an Appraiser</option>
          <option value="agent">I am a Real Estate Agent</option>
        </select>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-success">Register</button>
        <div class="text-center mt-3">
  <p>Already have an account? <a href="login.php">Log in</a></p>
</div>

      </div>
    </form>
  </div>

</body>
</html>
