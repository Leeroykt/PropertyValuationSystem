<?php
// Database connection
$host = 'localhost';
$db = 'epgglobal';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$properties = $conn->query("SELECT * FROM properties");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>EPG Global</title>
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    .property-card {
      position: relative;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .property-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .add-to-cart-icon {
      position: absolute;
      top: 10px;
      left: 10px;
      background: #fff;
      border-radius: 50%;
      padding: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      cursor: pointer;
    }
    .card-body {
      padding: 15px;
    }
  </style>
</head>

<body class="index-page">

<header id="header" class="header sticky-top">
  <div class="topbar d-flex align-items-center">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:info.epgglobal@epgglobal.co.zw">info.epgglobal@epgglobal.co.zw</a></i>
        <i class="bi bi-phone d-flex align-items-center ms-4"><span>+263 242 250170 | +263 242 701911</span></i>
      </div>
      <div class="social-links d-none d-md-flex align-items-center">
        <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </div>

  <div class="branding d-flex align-items-center">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">EPG Global</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html#hero" class="active">Home</a></li>
          <li><a href="index.html#about">About</a></li>
          <li><a href="index.html#services">Services</a></li>
          <li class="dropdown"><a href="#"><span>Property</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="buy.php">Property To Buy</a></li>
              <li><a href="#">Property to Rent</a></li>
            </ul>
          </li>
          <li><a href="index.html#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="cta-btn d-none d-sm-block" href="login.php">Valuation</a>
    </div>
  </div>
</header>

<main>
  <section id="properties" class="properties">
    <div class="container">
      <h2 class="text-center my-1 mb-5">Available Properties to Buy</h2>
      <div class="row">
        <?php if ($properties && $properties->num_rows > 0): ?>
          <?php while ($row = $properties->fetch_assoc()): ?>
            <div class="col-md-4">
              <div class="property-card">
                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Property Image">
                <div class="add-to-cart-icon" onclick="addToCart('<?php echo addslashes($row['title']); ?>', <?php echo $row['price']; ?>)">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                  <p class="card-text">Location: <?php echo htmlspecialchars($row['location']); ?></p>
                  <p class="card-text">Price: $<?php echo number_format($row['price'], 2); ?></p>
                  <button class="btn btn-secondary" onclick="viewProperty('<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['location']); ?>', <?php echo $row['price']; ?>)">View Property</button>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-center">No properties available at the moment.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<footer id="footer" class="footer light-background">
  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="index.html" class="logo d-flex align-items-center">
          <span class="sitename">EPG Global</span>
        </a>
        <div class="footer-contact pt-3">
          <p>4th Floor South Wing Runhare House</p>
          <p>4th street/Kwame Nkruma Ave, Harare, Zimbabwe</p>
          <p class="mt-3"><strong>Phone:</strong> <span>+263 242 250170 | +263 772 241 456</span></p>
          <p><strong>Email:</strong> <span>info.epgglobal@epgglobal.co.zw</span></p>
        </div>
      </div>
    </div>
  </div>
</footer>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  function addToCart(propertyName, price) {
    alert(propertyName + ' has been added to your cart for $' + price);
  }

  function viewProperty(title, location, price) {
    alert('Viewing ' + title + '\nLocation: ' + location + '\nPrice: $' + price);
  }
</script>
</body>
</html>
