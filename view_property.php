<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$db = "epgglobal";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: valuation.php");
    exit;
}

$valuation_id = $_GET['id'];

// Fetch estimated value and report
$stmt = $conn->prepare("SELECT address, estimated_value, report FROM valuated_property WHERE id = ?");
$stmt->bind_param("i", $valuation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $property = $result->fetch_assoc();
} else {
    $property = null;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Property Valuation Report</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
        }
        .valuation-box {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .back-link {
            margin-top: 20px;
        }
        .report-preview {
            background-color: #f1f3f5;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-success" href="#">EPG Global Valuation Portal</a>
        <div class="ms-auto">
            <span class="text-muted">Logged in as <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
        </div>
    </div>
</nav>

<div class="container my-5">
    <?php if ($property): ?>
        <div class="valuation-box">
            <h3 class="mb-4 text-success">Valuation Report</h3>
            <p><strong>Property Address:</strong> <?php echo htmlspecialchars($property['address']); ?></p>
            <p><strong>Estimated Value:</strong> <span class="text-primary">$<?php echo number_format($property['estimated_value'], 2); ?></span></p>

            <h5 class="mt-4">Report Details:</h5>
            <?php if (!empty($property['report'])): ?>
                <?php if (str_ends_with($property['report'], '.pdf')): ?>
                    <embed src="<?php echo htmlspecialchars($property['report']); ?>" type="application/pdf" width="100%" height="600px"/>
                <?php else: ?>
                    <div class="report-preview"><?php echo nl2br(htmlspecialchars($property['report'])); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning mt-3">No report available for this property yet.</div>
            <?php endif; ?>

            <a href="valuation.php" class="btn btn-outline-secondary back-link"><i class="bi bi-arrow-left"></i> Back to My Valuations</a>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center">Valuation not found or has been removed.</div>
    <?php endif; ?>
</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
