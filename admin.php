<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, redirect to login page
    header("Location: adminlogin.php");
    exit;
}

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

// Fetch counts for dashboard
$pending_count = 0;
$completed_count = 0;
$users_count = 0;
$total_visits = 0;

// Count pending valuations
$pending_sql = "SELECT COUNT(*) as count FROM valuations";
$pending_result = $conn->query($pending_sql);
if ($pending_result) {
    $pending_count = $pending_result->fetch_assoc()['count'];
}

// Count completed valuations
$completed_sql = "SELECT COUNT(*) as count FROM valuated_property";
$completed_result = $conn->query($completed_sql);
if ($completed_result) {
    $completed_count = $completed_result->fetch_assoc()['count'];
}

// Count users
$users_sql = "SELECT COUNT(*) as count FROM users";
$users_result = $conn->query($users_sql);
if ($users_result) {
    $users_count = $users_result->fetch_assoc()['count'];
}

// Fetch total visits
$visits_sql = "SELECT COUNT(*) as count FROM visits"; // Assuming you have a 'visits' table
$visits_result = $conn->query($visits_sql);
if ($visits_result) {
    $total_visits = $visits_result->fetch_assoc()['count'];
}

// Fetch pending valuations for the table
$pending_valuations_sql = "SELECT * FROM valuations";
$pending_valuations_result = $conn->query($pending_valuations_sql);
$pending_valuations = [];

while ($row = $pending_valuations_result->fetch_assoc()) {
    $pending_valuations[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Responsive Estate Admin Dashboard</title>
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
      transition: transform 0.3s ease;
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
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .top-navbar {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 10px 20px;
      z-index: 1030;
    }

    .content {
      flex-grow: 1;
      padding: 20px;
    }

    .hamburger-btn {
      display: none;
    }

    #overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      z-index: 1040;
    }

    /* Mobile styles */
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        transform: translateX(-100%);
        z-index: 1050;
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .top-navbar {
        margin-left: 0;
      }

      .hamburger-btn {
        display: inline-block;
      }

      .content {
        padding: 20px 10px;
      }
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
      <a href="content.php"><i class="bi bi-pencil-square"></i> Website Content</a>
      <!--a href="#"><i class="bi bi-envelope"></i> Messages</a-->
      <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
    </div>

    <!-- Main Section -->
    <div class="main-section">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <button class="btn btn-outline-secondary me-2 hamburger-btn" id="toggleSidebar">
                <i class="bi bi-list"></i>
              </button>
              <span class="navbar-brand mb-0 h1">Dashboard</span>
            </div>
            <div class="dropdown">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> Admin
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                <li><a class="dropdown-item" href="adminlogout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
              </ul>
            </div>
        </div>
      </nav>

      <!-- Content -->
      <div class="content" id="mainContent">
        <div class="container-fluid">
          <h2 class="mb-4">Dashboard Overview</h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <div class="col">
              <div class="card text-bg-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <h6>Total Visits</h6>
                    <h4><?php echo $total_visits; ?></h4>
                  </div>
                  <i class="bi bi-bar-chart-line-fill card-icon"></i>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="card text-bg-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <h6>Pending Valuations</h6>
                    <h4><?php echo $pending_count; ?></h4>
                  </div>
                  <i class="bi bi-clock-history card-icon"></i>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="card text-bg-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <h6>Completed Valuations</h6>
                    <h4><?php echo $completed_count; ?></h4>
                  </div>
                  <i class="bi bi-chat-left-dots-fill card-icon"></i>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="card text-bg-danger">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <h6>Users</h6>
                    <h4><?php echo $users_count; ?></h4>
                  </div>
                  <i class="bi bi-people-fill card-icon"></i>
                </div>
              </div>
            </div>
          </div>

          <hr class="my-5"/>

          <h4>Pending Valuations</h4>
          <div class="card mb-5">
            <div class="card-body">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Property</th>
                    <th>Owner</th>
                    <th>Requested On</th>
                    <th>Status</th>
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
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td>
                      <button class="btn btn-sm btn-success">View In Valuations</button>
                    </td>
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

  <!-- Overlay for mobile sidebar -->
  <div id="overlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
      overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('show');
      overlay.style.display = 'none';
    });
  </script>
</body>
</html>