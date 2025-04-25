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

// Fetch all pending valuations
$search = isset($_GET['search']) ? $_GET['search'] : '';
$pending_sql = "SELECT * FROM valuations WHERE address LIKE ? OR fullname LIKE ?";
$pending_stmt = $conn->prepare($pending_sql);
$search_param = '%' . $search . '%';
$pending_stmt->bind_param("ss", $search_param, $search_param);
$pending_stmt->execute();
$pending_result = $pending_stmt->get_result();
$pending_valuations = [];

while ($row = $pending_result->fetch_assoc()) {
    $pending_valuations[] = $row;
}

// Fetch all completed valuations
$completed_sql = "SELECT * FROM valuated_property";
$completed_result = $conn->query($completed_sql);
$completed_valuations = [];

while ($row = $completed_result->fetch_assoc()) {
    $completed_valuations[] = $row;
}

$pending_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Valuations - Estate Admin Dashboard</title>
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
  </style>
</head>
<body>

  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <h4><i class="bi bi-house-door"></i> Estate Admin</h4>
      <a href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="properties.php"><i class="bi bi-buildings"></i> Properties</a>
      <a href="adminValuate.php"><i class="bi bi-clipboard-check"></i> Valuations</a>
      <a href="users.php"><i class="bi bi-people"></i> Users</a>
      <!--a href="#"><i class="bi bi-envelope"></i> Messages</a-->
      <a href="content.php"><i class="bi bi-pencil-square"></i> Website Content</a>
      <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
    </div>

    <!-- Main Section -->
    <div class="main-section">
      <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
          <span class="navbar-brand mb-0 h1">Valuations</span>
        </div>
      </nav>

      <div class="content">
        <div class="container-fluid">
          <input type="text" class="form-control mb-3" id="search" placeholder="Search by address or owner" value="<?php echo htmlspecialchars($search); ?>" onkeyup="searchFunction()">
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#pending">Pending Valuations</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#completed">Completed Valuations</a>
            </li>
          </ul>

          <div class="tab-content">
            <div id="pending" class="tab-pane fade show active">
              <h3 class="mt-3">Pending Valuations</h3>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Address</th>
                    <th>Owner</th>
                    <th>Requested On</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($pending_valuations as $index => $valuation): ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><?php echo htmlspecialchars($valuation['address']); ?></td>
                      <td><?php echo htmlspecialchars($valuation['fullname']); ?></td>
                      <td><?php echo date('F j, Y', strtotime($valuation['requested_on'])); ?></td>
                      <td>
                        <a href="property_valuation.php?id=<?php echo $valuation['id']; ?>" class="btn btn-sm btn-success">Valuate</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div id="completed" class="tab-pane fade">
              <h3 class="mt-3">Completed Valuations</h3>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Address</th>
                    <th>Owner</th>
                    <th>Requested On</th>
                    <th>Estimated Value</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($completed_valuations as $index => $valuation): ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><?php echo htmlspecialchars($valuation['address']); ?></td>
                      <td><?php echo htmlspecialchars($valuation['fullname']); ?></td>
                      <td><?php echo date('F j, Y', strtotime($valuation['requested_on'])); ?></td>
                      <td><?php echo htmlspecialchars($valuation['estimated_value']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const searchInput = document.getElementById('search');
    
    function searchFunction() {
      const filter = searchInput.value.toLowerCase();
      const pendingTable = document.querySelector('#pending tbody');
      const completedTable = document.querySelector('#completed tbody');

      filterTable(pendingTable, filter);
      filterTable(completedTable, filter);
    }

    function filterTable(table, filter) {
      const rows = table.getElementsByTagName('tr');
      for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let match = false;
        for (let j = 0; j < cells.length; j++) {
          if (cells[j]) {
            if (cells[j].textContent.toLowerCase().includes(filter)) {
              match = true;
              break;
            }
          }
        }
        rows[i].style.display = match ? '' : 'none';
      }
    }
  </script>
</body>
</html>