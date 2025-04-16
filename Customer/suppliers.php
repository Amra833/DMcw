<?php
include '../admin/connection.php'; // Oracle DB connection

// Fetch all suppliers
$query = "SELECT * FROM SUPPLIERS ORDER BY supplier_id DESC";
$statement = oci_parse($conn, $query);
oci_execute($statement);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>UrbanFood | Our Suppliers</title>

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="suppliers.css">
</head>
<body>

<!-- Header -->
<header class="header">
  <a href="#" class="logo"><i class="fas fa-shopping-basket"></i> UrbanFood</a>

  <nav class="navbar">
    <a href="home.html">Home</a>
    <a href="products.html">Products</a>
    <a href="feedbacks.html">Feedbacks</a>
  </nav>

  <div class="icons">
    <a href="cart.html"><div class="fas fa-shopping-cart" id="cart-btn"></div></a>
  </div>
</header>

<!-- Supplier Section -->
<section class="products" id="suppliers">
  <div class="hero-image">
    <h1 class="heading"><span>Meet Our Trusted Suppliers</span></h1>
  </div>

  <div class="box-container">
    <?php while ($row = oci_fetch_assoc($statement)) { ?>
      <div class="box">
        <img src="../Images/<?php echo htmlspecialchars($row['SUP_PHOTO']); ?>" alt="<?php echo htmlspecialchars($row['SUP_FULLNAME']); ?>">
        <h3><?php echo htmlspecialchars($row['SUP_FULLNAME']); ?></h3>
        <p>Location: <?php echo htmlspecialchars($row['SUP_LOCATION']); ?></p>
        <p>Products: <?php echo htmlspecialchars($row['SUP_PRODUCT']); ?></p>
      </div>
    <?php } ?>
  </div>
</section>

<!-- Footer -->
<section class="footer">
  <div class="box-container">
    <div class="box">
      <h3>UrbanFood <i class="fas fa-shopping-basket"></i></h3>
      <p>Bridging the gap between local growers and urban consumers.</p>
      <div class="share">
        <a href="#" class="fab fa-facebook-f"></a>
        <a href="#" class="fab fa-twitter"></a>
        <a href="#" class="fab fa-instagram"></a>
        <a href="#" class="fab fa-linkedin"></a>
      </div>
    </div>

    <div class="box">
      <h3>Quick Links</h3>
      <a href="index.html">Home</a>
      <a href="#products">Products</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
    </div>

    <div class="box">
      <h3>Contact Info</h3>
      <a href="#">+94 76 123 4567</a>
      <a href="#">urbanfood@gmail.com</a>
      <a href="#">Colombo, Sri Lanka</a>
    </div>
  </div>

  <div class="credit">© 2025 UrbanFood | All rights reserved</div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="home.js"></script>

</body>
</html>

<?php
oci_free_statement($statement);
oci_close($conn);
?>
