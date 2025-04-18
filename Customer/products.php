<?php
include '../admin/connection.php';

$categories = [
  'Fruits',
  'Vegetables',
  'Dairy Products',
  'Baked Goods',
  'Handmade Crafts'
];

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UrbanFood Products</title>
  <link rel="stylesheet" href="products.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- Header -->
<header class="header">
  <a href="#" class="logo"><i class="fa fa-shopping-basket"></i>UrbanFood</a>

  <nav class="navbar">
    <a href="home.html">Home</a>
    <a href="products.php">Products</a>
    <a href="suppliers.php">Suppliers</a>
    <a href="view_feedbacks.php">Feedbacks</a>
  </nav>

  <!-- SEARCH FORM placed below the navbar -->
  <div class="search-bar">
    <form action="products.php" method="GET">
      <input type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($searchQuery) ?>" required>
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <div class="icons">
    <div class="fas fa-bars" id="menu-btn"></div>
    <a href="cart.html"><div class="fas fa-shopping-cart" id="cart-btn"></div></a>
  </div>
</header>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay">
    <h1>Discover Fresh & Local Products</h1>
  </div>
</section>

<!-- Product Section -->
<section id="products">
<?php
if ($searchQuery !== '') {
  $sql = "SELECT * FROM PRODUCTS WHERE LOWER(product_name) LIKE :search ORDER BY product_id DESC";
  $stmt = oci_parse($conn, $sql);
  $searchTerm = '%' . strtolower($searchQuery) . '%';
  oci_bind_by_name($stmt, ":search", $searchTerm);
  oci_execute($stmt);

  echo '<div class="product-grid">';
  $hasResults = false;

  while ($row = oci_fetch_assoc($stmt)) {
    $hasResults = true;
    ?>
    <div class="product-card">
      <img src="../admin/uploads/<?= htmlspecialchars($row['PRODUCT_IMAGE']) ?>" 
           alt="<?= htmlspecialchars($row['PRODUCT_NAME']) ?>" 
           onerror="this.onerror=null;this.src='default.png';">
      <h3><?= htmlspecialchars($row['PRODUCT_NAME']) ?></h3>
      <p>Rs. <?= number_format($row['PRODUCT_PRICE'], 2) ?></p>
      <button onclick="addToCart('<?= htmlspecialchars($row['PRODUCT_NAME']) ?>', <?= $row['PRODUCT_PRICE'] ?>)">Add to Cart</button>
    </div>
    <?php
  }

  if (!$hasResults) {
    echo "<p>No products found for your search.</p>";
  }

  echo '</div>';
} else {
  foreach ($categories as $category) {
    $sql = "SELECT * FROM PRODUCTS WHERE category = :cat ORDER BY product_id DESC";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":cat", $category);
    oci_execute($stmt);

    if (oci_fetch($stmt)) {
      oci_execute($stmt);
      ?>
      <div class="category-section" id="<?= strtolower(str_replace(' ', '-', $category)) ?>">
        <h2><?= htmlspecialchars($category) ?></h2>
        <div class="product-grid">
          <?php while ($row = oci_fetch_assoc($stmt)): ?>
            <div class="product-card">
              <img src="../admin/uploads/<?= htmlspecialchars($row['PRODUCT_IMAGE']) ?>" 
                   alt="<?= htmlspecialchars($row['PRODUCT_NAME']) ?>" 
                   onerror="this.onerror=null;this.src='default.png';">
              <h3><?= htmlspecialchars($row['PRODUCT_NAME']) ?></h3>
              <p>Rs. <?= number_format($row['PRODUCT_PRICE'], 2) ?></p>
              <button onclick="addToCart('<?= htmlspecialchars($row['PRODUCT_NAME']) ?>', <?= $row['PRODUCT_PRICE'] ?>)">Add to Cart</button>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
      <?php
    }
  }
}
?>
</section>

<!-- Add To Cart Script -->
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

  // Responsive menu toggle
  const menuBtn = document.getElementById('menu-btn');
  const navbar = document.querySelector('.navbar');
  menuBtn.onclick = () => {
    navbar.classList.toggle('active');
  };
</script>

</body>
</html>

<?php
oci_close($conn);
?>
