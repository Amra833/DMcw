<?php
// Database connection
$connection = oci_connect("system", "system", "//localhost/XE"); // Replace with your details

if (!$connection) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

// Categories to display
$categories = [
    'Fruits',
    'Vegetables',
    'Dairy Products',
    'Baked Goods',
    'Handmade Crafts'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UrbanFood Products</title>
  <link rel="stylesheet" href="products.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<header class="header">
  <a href="#" class="logo"><i class="fa fa-shopping-basket"></i> UrbanFood</a>
  <nav class="navbar">
    <a href="home.html">Home</a>
    <a href="#products">Products</a>
    <a href="orders.html">Orders</a>
    <a href="suppliers.html">Suppliers</a>
  </nav>
  <div class="icons">
    <div class="fas fa-bars" id="menu-btn"></div>
    <div class="fas fa-search" id="search-btn"></div>
    <a href="cart.html"><div class="fas fa-shopping-cart" id="cart-btn"></div></a>
    <div class="fas fa-user" id="login-btn"></div>
  </div>
</header>

<section class="hero">
  <div class="hero-overlay">
    <h1>Discover Fresh & Local Products</h1>
  </div>
</section>

<section id="products">
<?php foreach ($categories as $category): ?>
  <div class="category-section" id="<?= strtolower(str_replace(' ', '-', $category)) ?>">
    <h2><?= htmlspecialchars($category) ?></h2>
    <div class="product-grid">
      <?php
        $sql = "SELECT * FROM PRODUCTS WHERE category = :category";
        $stmt = oci_parse($connection, $sql);
        oci_bind_by_name($stmt, ":category", $category);
        if (oci_execute($stmt)) {
          while ($row = oci_fetch_assoc($stmt)) {
            echo '<div class="product-card">';
            echo '<img src="' . htmlspecialchars($row['PRODUCT_IMAGE']) . '" alt="' . htmlspecialchars($row['PRODUCT_NAME']) . '">';
            echo '<h3>' . htmlspecialchars($row['PRODUCT_NAME']) . '</h3>';
            echo '<p>Rs. ' . htmlspecialchars($row['PRODUCT_PRICE']) . '</p>';
            echo '<button onclick="addToCart(\'' . addslashes($row['PRODUCT_NAME']) . '\', ' . $row['PRODUCT_PRICE'] . ')">Add to Cart</button>';
            echo '</div>';
          }
        } else {
          $err = oci_error($stmt);
          echo "<p>Error loading products: " . htmlspecialchars($err['message']) . "</p>";
        }
        oci_free_statement($stmt);
      ?>
    </div>
  </div>
<?php endforeach; ?>
</section>

<script>
function addToCart(name, price) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const index = cart.findIndex(item => item.name === name);
  if (index !== -1) {
    cart[index].qty += 1;
  } else {
    cart.push({ name: name, price: price, qty: 1 });
  }
  localStorage.setItem("cart", JSON.stringify(cart));
  alert(name + " added to cart!");
}
</script>

</body>
</html>
<?php oci_close($connection); ?>
