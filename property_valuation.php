<?php
session_start();

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

// Fetch valuation details based on ID
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM valuations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$valuation = $result->fetch_assoc();
$stmt->close();

if (!$valuation) {
    die("Valuation not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estimated_value = $_POST['propertyValue'];
    $report_details = $_POST['reportDetails'];

    $stmt = $conn->prepare("INSERT INTO valuated_property (fullname, address, deed_number, purpose, description, stand_size, contact_number, email, image_paths, estimated_value, report, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Valuated')");
    $stmt->bind_param("sssssssssss", $valuation['fullname'], $valuation['address'], $valuation['deed_number'], $valuation['purpose'], $valuation['description'], $valuation['stand_size'], $valuation['contact_number'], $valuation['email'], $valuation['image_paths'], $estimated_value, $report_details);

    if ($stmt->execute()) {
        $stmt = $conn->prepare("DELETE FROM valuations WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo "<script>alert('Property evaluated successfully!'); window.location.href='adminValuate.php';</script>";
    } else {
        echo "<script>alert('Error evaluating property: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Property Valuation - Estate Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
    }
    .wrapper {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 250px;
      background-color: #343a40;
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1030;
      overflow-y: auto;
    }
    .sidebar a {
      color: #adb5bd;
      text-decoration: none;
      padding: 10px 20px;
      display: block;
    }
    .sidebar a:hover {
      color: white;
      background-color: #495057;
    }
    .sidebar h4 {
      padding: 20px;
    }

    .main-section {
      margin-left: 250px;
      flex: 1;
      display: flex;
      flex-direction: column;
      padding-top: 70px;
    }
    .top-navbar {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 10px 20px;
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      z-index: 1040;
    }
    .content {
      padding: 20px;
      margin-top: 20px;
    }

    .valuation-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      align-items: stretch;
    }

    .image-column {
      flex: 1 1 300px;
      max-width: 400px;
    }

    .image-column .carousel-inner img {
      width: 100%;
      height: 500px;
      object-fit: cover;
      border-radius: 10px;
    }

    .details-column {
      flex: 2;
      min-width: 300px;  
      line-height: 0.6;
      margin-bottom: 6px;

    }

    @media (max-width: 768px) {
      .valuation-container {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <h4><i class="bi bi-house-door"></i> Estate Admin</h4>
    <a href="adminValuate.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="content.php"><i class="bi bi-pencil-square"></i> Website Content</a>
    <a href="properties.php"><i class="bi bi-buildings"></i> Properties</a>
    <a href="adminValuate.php"><i class="bi bi-clipboard-check"></i> Valuations</a>
    <a href="users.php"><i class="bi bi-people"></i> Users</a>
    <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
  </div>

  <!-- Main Section -->
  <div class="main-section">
    <nav class="navbar navbar-expand-lg top-navbar">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <button class="btn btn-outline-secondary me-2" id="toggleSidebar">
          <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h1">Valuate Property</span>
      </div>
    </nav>

    <div class="content">
      <div class="container-fluid">
        <div class="valuation-container">
          <!-- Image Column -->
          <div class="image-column">
            <?php if (!empty($valuation['image_paths'])): ?>
              <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <?php
                  $images = explode(',', $valuation['image_paths']);
                  foreach ($images as $index => $image): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                      <img src="<?php echo htmlspecialchars($image); ?>" class="d-block w-100" alt="Property Image">
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                  <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                  </button>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <p>No images available.</p>
            <?php endif; ?>
          </div>

          <!-- Details & Form -->
          <div class="details-column">
  <div class="card border-0 shadow-sm mb-4" style="background-color: #fdfdfd;">
    <div class="card-header bg-white border-bottom pb-2">
      <h5 class="mb-0 text-success">Valuation Details</h5>
    </div>
    <div class="card-body px-4 py-3">
      <div class="mb-2 row border-bottom py-2">
        <label class="col-5 fw-semibold text-muted">Address:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['address']); ?></div>
      </div>
      <div class="mb-3 row border-bottom py-2 bg-light">
        <label class="col-5 fw-semibold text-muted">Description:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['description']); ?></div>
      </div>
      <div class="mb-2 row border-bottom py-2">
        <label class="col-5 fw-semibold text-muted">Owner:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['fullname']); ?></div>
      </div>
      <div class="mb-2 row border-bottom py-2 bg-light">
        <label class="col-5 fw-semibold text-muted">Deed Number:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['deed_number']); ?></div>
      </div>
      <div class="mb-2 row border-bottom py-2">
        <label class="col-5 fw-semibold text-muted">Purpose:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['purpose']); ?></div>
      </div>
      <div class="mb-2 row border-bottom py-2 bg-light">
        <label class="col-5 fw-semibold text-muted">Stand Size:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['stand_size']); ?></div>
      </div>
      <!--div class="mb-2 row border-bottom py-2">
        <label class="col-5 fw-semibold text-muted">Contact Number:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['contact_number']); ?></div>
      </div-->
      <!--div class="mb-2 row border-bottom py-2 bg-light">
        <label class="col-5 fw-semibold text-muted">Email:</label>
        <div class="col-7"><?php echo htmlspecialchars($valuation['email']); ?></div>
      </div--->
      <div class="mb-2 row py-2">
        <label class="col-5 fw-semibold text-muted">Requested On:</label>
        <div class="col-7"><?php echo date('F j, Y', strtotime($valuation['requested_on'])); ?></div>
      </div>
    </div>
  </div>

  <!-- Form Section -->
  <form id="valuationForm" method="POST" class="mt-3">
    <div class="mb-3">
      <label for="propertyValue" class="form-label">Estimated Value</label>
      <input type="number" class="form-control" id="propertyValue" name="propertyValue" placeholder="Enter estimated value" required>
    </div>
    <div class="mb-3">
      <label for="reportDetails" class="form-label">Valuation Report</label>
      <textarea class="form-control" id="reportDetails" name="reportDetails" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Evaluate</button>
  </form>
</div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    sidebar.style.transform = sidebar.classList.contains('show') ? 'translateX(0)' : 'translateX(-100%)';
  });
</script>
</body>
</html>
