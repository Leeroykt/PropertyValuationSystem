<?php
// Establish connection to the database
$host = 'localhost';
$db = 'epgglobal';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Handle form submission (adding new property)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $imagePath = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $targetDir = "uploads/";
        $targetFilePath = $targetDir . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath);
        $imagePath = $targetFilePath;
    }

    // Prepare and execute the query to insert the property into the database
    $stmt = $conn->prepare("INSERT INTO properties (title, location, price, status, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $title, $location, $price, $status, $imagePath);

    if ($stmt->execute()) {
        $successMessage = "Property added successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch properties from the database to display
$propertiesQuery = "SELECT * FROM properties";
$propertiesResult = $conn->query($propertiesQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Estate Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body,
        html {
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
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-top: 70px;
        }

        .top-navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .hamburger-btn {
            display: none;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .property-card {
            margin-bottom: 1rem;
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

            .main-section {
                margin-left: 0;
            }

            .top-navbar {
                margin-left: 0;
            }

            .hamburger-btn {
                display: inline-block;
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
            <!--a href="#"><i class="bi bi-envelope"></i> Messages</a-->
            <a href="content.php"><i class="bi bi-pencil-square"></i> Website Content</a>
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
                        <span class="navbar-brand mb-0 h1">Properties</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> Admin
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="content" id="mainContent">
                <div class="container-fluid">
                    <h2 class="mb-4">Manage Properties</h2>

                    <!-- Success/Error Message -->
                    <?php if (isset($successMessage)) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $successMessage; ?>
                        </div>
                    <?php } elseif (isset($errorMessage)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php } ?>

                    <!-- Add New Property Button -->
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" id="addPropertyBtn" data-bs-toggle="modal" data-bs-target="#addPropertyModal">+ Add New Property</button>
                    </div>

                    <!-- Property List -->
                    <div class="row">
                        <?php if ($propertiesResult->num_rows > 0) { ?>
                            <?php while ($property = $propertiesResult->fetch_assoc()) { ?>
                                <div class="col-md-4">
                                    <div class="card property-card">
                                        <img src="<?php echo $property['image']; ?>" class="card-img-top" alt="Property Image">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $property['title']; ?></h5>
                                            <p class="card-text"><strong>Location:</strong> <?php echo $property['location']; ?></p>
                                            <p class="card-text"><strong>Price:</strong> $<?php echo number_format($property['price'], 2); ?></p>
                                            <p class="card-text"><strong>Status:</strong> <?php echo $property['status']; ?></p>
                                            <button class="btn btn-primary edit-btn" data-id="<?= $property['id']; ?>"
                                                data-title="<?= htmlspecialchars($property['title'], ENT_QUOTES); ?>"
                                                data-location="<?= htmlspecialchars($property['location'], ENT_QUOTES); ?>"
                                                data-price="<?= $property['price']; ?>"
                                                data-status="<?= $property['status']; ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editPropertyModal">Edit</button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="col-12">
                                <p>No properties available.</p>
                            </div>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Add Property Modal -->
    <div class="modal fade" id="addPropertyModal" tabindex="-1" aria-labelledby="addPropertyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="properties.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPropertyModalLabel">Add New Property</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Property Title</label>
                        <input type="text" class="form-control" id="title" name="title" required />
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required />
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required />
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Available">Available</option>
                            <option value="Sold">Sold</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Property Image</label>
                        <input class="form-control" type="file" name="image" id="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Property</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Property Modal -->
    <div class="modal fade" id="editPropertyModal" tabindex="-1" aria-labelledby="editPropertyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="edit-property.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPropertyModalLabel">Edit Property</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id" />
                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Property Title</label>
                        <input type="text" class="form-control" id="edit-title" name="title" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit-location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="edit-location" name="location" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit-price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="edit-price" name="price" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status</label>
                        <select class="form-select" id="edit-status" name="status" required>
                            <option value="Available">Available</option>
                            <option value="Sold">Sold</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-image" class="form-label">Change Image (Optional)</label>
                        <input class="form-control" type="file" name="image" id="edit-image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update Property</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to populate the Edit Property Modal with the current property data
        const editBtns = document.querySelectorAll('.edit-btn');
        editBtns.forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const title = button.getAttribute('data-title');
                const location = button.getAttribute('data-location');
                const price = button.getAttribute('data-price');
                const status = button.getAttribute('data-status');

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-title').value = title;
                document.getElementById('edit-location').value = location;
                document.getElementById('edit-price').value = price;
                document.getElementById('edit-status').value = status;
            });
        });
    </script>
</body>

</html>
