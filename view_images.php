<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$fullname = $_SESSION['fullname'];
$id = $_GET['id'];

$conn = new mysqli("localhost", "root", "", "epgglobal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT image_paths FROM valuations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$valuation = $result->fetch_assoc();
$image_paths = isset($valuation['image_paths']) ? explode(',', $valuation['image_paths']) : [];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Property Images</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Roboto', sans-serif;
      background-color: #f8f9fa;
    }
    .wrapper {
      display: flex;
      height: 100vh;
      overflow: hidden;
    }
    .sidebar {
      width: 250px;
      background-color: #343a40;
      color: white;
      display: flex;
      flex-direction: column;
    }
    .sidebar a {
      color: #adb5bd;
      text-decoration: none;
      padding: 15px 20px;
      display: block;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #495057;
      color: white;
    }
    .content {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }
    .topbar {
      background: #fff;
      border-bottom: 1px solid #dee2e6;
      padding: 1rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .gallery-container {
      padding: 2rem;
    }
    .image-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1rem;
    }
    .image-grid img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ddd;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
    footer {
      text-align: center;
      padding: 1rem;
      background: #f1f1f1;
      margin-top: auto;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center py-3">EPG Portal</h4>
    <a href="valuation.php" class="nav-link"><i class="bi bi-plus-circle"></i> Submit Request</a>
    <a href="#" class="nav-link"><i class="bi bi-hourglass-split"></i> Pending</a>
    <a href="#" class="nav-link active"><i class="bi bi-check2-circle"></i> Evaluated</a>
    <hr class="text-light">
    <a href="#"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($fullname); ?></a>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="topbar">
      <h5 class="mb-0">Property Images</h5>
      <span class="text-muted"><?php echo htmlspecialchars($fullname); ?></span>
    </div>

    <div class="gallery-container">
      <?php if (empty($image_paths[0])): ?>
        <div class="alert alert-warning text-center">No images uploaded for this property.</div>
      <?php else: ?>
        <div class="image-grid">
          <?php foreach ($image_paths as $img): ?>
            <img src="<?php echo htmlspecialchars(trim($img)); ?>" alt="Property Image">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="text-center mt-4">
        <a href="valuation.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
      </div>
    </div>

    <footer>
      <p class="mb-0 fw-bold">EPG Global Real Estate</p>
      <small>Harare, Zimbabwe | +263 242 250170 | info@epgglobal.co.zw</small>
    </footer>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
