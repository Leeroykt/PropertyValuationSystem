<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$fullname = $_SESSION['fullname'];

$conn = new mysqli("localhost", "root", "", "epgglobal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT id, address, purpose, description, created_at FROM valuations WHERE fullname = ?");
$stmt->bind_param("s", $fullname);
$stmt->execute();
$pending_valuations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT id, address, estimated_value, image_paths, report FROM valuated_property WHERE fullname = ?");
$stmt->bind_param("s", $fullname);
$stmt->execute();
$evaluated_properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>EPG Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: 'Roboto', sans-serif;
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
      overflow: hidden;
    }

    .topbar {
      background: #fff;
      border-bottom: 1px solid #dee2e6;
      padding: 0.75rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .main-section {
      flex: 1;
      overflow-y: auto;
      padding: 1.5rem;
      display: none;
    }

    .main-section.active {
      display: block;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background: #f8f9fa;
    }

    .modal-img {
      max-width: 100px; /* Small image size */
      height: auto;
      float: right; /* Align to the right */
      margin-left: 15px;
    }

    .report-section {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center py-3">EPG Portal</h4>
    <a href="#" class="nav-link active" onclick="showSection('submit')"><i class="bi bi-plus-circle"></i> Submit Request</a>
    <a href="#" class="nav-link" onclick="showSection('pending')"><i class="bi bi-hourglass-split"></i> Pending</a>
    <a href="#" class="nav-link" onclick="showSection('evaluated')"><i class="bi bi-check2-circle"></i> Evaluated</a>
    <hr class="text-light">
    <a href="#" class="text-light"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($fullname); ?></a>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

  <!-- Content -->
  <div class="content">
    <!-- Topbar -->
    <div class="topbar">
      <span><strong>EPG Valuation Portal</strong></span>
      <span class="text-muted"><?php echo htmlspecialchars($fullname); ?></span>
    </div>

    <!-- Sections -->
    <div id="submit" class="main-section active">
      <h4>Submit a Property Valuation</h4>
      <form method="POST" action="submit_valuation.php" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" class="form-control" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" readonly required />
            <input type="text" class="form-control mt-3" name="address" placeholder="Address" required />
            <input type="text" class="form-control mt-3" name="deed_number" placeholder="Title Deed Number" required />
            <select class="form-select mt-3" name="purpose" required>
              <option selected disabled>Select Purpose</option>
              <option>Sale</option>
              <option>Mortgage</option>
              <option>Valuation</option>
              <option>Other</option>
            </select>
            <input type="file" class="form-control mt-3" name="images[]" multiple />
          </div>
          <div class="col-md-6">
            <textarea class="form-control" name="description" rows="3" placeholder="Property Description" required></textarea>
            <input type="text" class="form-control mt-3" name="stand_size" placeholder="Stand Size" required />
            <input type="text" class="form-control mt-3" name="contact_number" placeholder="Contact Number" required />
            <input type="email" class="form-control mt-3" name="email" placeholder="Email Address" required />
          </div>
        </div>
        <div class="text-end mt-4">
          <button type="submit" class="btn btn-success">Submit</button>
        </div>
      </form>
    </div>

    <div id="pending" class="main-section">
      <h4>Pending Requests</h4>
      <input type="text" class="form-control mb-3" id="searchPending" onkeyup="filterTable('pendingTable')" placeholder="Search address...">
      <table class="table table-bordered" id="pendingTable">
        <thead>
          <tr><th>#</th><th>Address</th><th>Purpose</th><th>Description</th><th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php if (empty($pending_valuations)): ?>
          <tr><td colspan="6" class="text-center">No pending requests.</td></tr>
        <?php else: ?>
          <?php foreach ($pending_valuations as $i => $row): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['purpose']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><a href="view_images.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">View</a></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div id="evaluated" class="main-section">
      <h4>Evaluated Properties</h4>
      <input type="text" class="form-control mb-3" id="searchEvaluated" onkeyup="filterTable('evaluatedTable')" placeholder="Search address...">
      <table class="table table-bordered" id="evaluatedTable">
        <thead>
          <tr><th>#</th><th>Address</th><th>Estimated Value</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php if (empty($evaluated_properties)): ?>
          <tr><td colspan="4" class="text-center">No evaluated properties.</td></tr>
        <?php else: ?>
          <?php foreach ($evaluated_properties as $i => $row): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['estimated_value']) ?></td>
            <td>
              <!-- Button to trigger modal -->
              <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#propertyModal" onclick="loadPropertyDetails(<?= $row['id'] ?>)">View</button>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <footer>
      <p class="mb-1 fw-bold">EPG Global Real Estate</p>
      <p class="mb-0">Harare, Zimbabwe | +263 242 250170 | info@epgglobal.co.zw</p>
    </footer>
  </div>
</div>

<!-- Modal for Property Details -->
<div class="modal fade" id="propertyModal" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="propertyModalLabel">Evaluation Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-between">
          <h5 id="modalAddress"></h5>
          <img id="modalImage" class="modal-img" alt="Property Image" />
        </div>
        <p><strong>Estimated Value: </strong>$<span id="modalValue"></span></p>
        <div id="modalDescription"></div>
        <div class="report-section">
          <p><strong>Report Details:</strong></p>
          <div id="modalReport"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function loadPropertyDetails(id) {
    const property = <?php echo json_encode($evaluated_properties); ?>.find(p => p.id == id);
    
    document.getElementById("modalAddress").textContent = property.address;
    document.getElementById("modalValue").textContent = property.estimated_value;

    const images = property.image_paths.split(',');
    document.getElementById("modalImage").src = images[0]; // Display the first image

    document.getElementById("modalDescription").textContent = property.description;
    document.getElementById("modalReport").textContent = property.report; // Display report details
  }

  function showSection(id) {
    document.querySelectorAll('.main-section').forEach(sec => sec.classList.remove('active'));
    document.getElementById(id).classList.add('active');

    document.querySelectorAll('.sidebar a.nav-link').forEach(link => link.classList.remove('active'));
    event.target.classList.add('active');
  }

  function filterTable(tableId) {
    const inputId = tableId === 'pendingTable' ? 'searchPending' : 'searchEvaluated';
    const filter = document.getElementById(inputId).value.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);

    rows.forEach(row => {
      const cell = row.getElementsByTagName("td")[1];
      row.style.display = cell && cell.textContent.toLowerCase().includes(filter) ? "" : "none";
    });
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>