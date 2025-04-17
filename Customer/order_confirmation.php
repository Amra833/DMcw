<?php
$connection = oci_connect("system", "system", "//localhost/XE");
if (!$connection) {
    die("Connection failed: " . oci_error()['message']);
}

$order_id = $_GET['order_id'];

$sql = "SELECT * FROM orders WHERE ORDER_ID = :order_id";
$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ':order_id', $order_id);
oci_execute($stmt);
$order = oci_fetch_assoc($stmt);

oci_free_statement($stmt);
oci_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Confirmation - UrbanFood</title>
  <link rel="stylesheet" href="order_confirmation.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo">UrbanFood</div>
    <ul class="nav-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="products.html">Products</a></li>
      <li><a href="cart.html">Cart</a></li>
      <li><a href="profile.php">My Account</a></li>
    </ul>
  </nav>

  <!-- Header -->
  <header>
    <h1>Order Confirmation</h1>
  </header>

  <!-- Order Details -->
  <section class="order-details">
    <h2>Thank you for your order!</h2>
    <p>Your order has been successfully placed.</p>
    <p><strong>Order ID:</strong> <?php echo $order['ORDER_ID']; ?></p>
    <p><strong>Name:</strong> <?php echo $order['CUSTOMER_NAME']; ?></p>
    <p><strong>Delivery Address:</strong> <?php echo $order['DELIVERY_ADDRESS']; ?></p>
    <p><strong>Payment Method:</strong> <?php echo $order['PAYMENT_METHOD']; ?></p>
    <p><strong>Delivery Method:</strong> <?php echo $order['DELIVERY_METHOD']; ?></p>
    <p><strong>Status:</strong> <?php echo $order['ORDER_STATUS']; ?></p>
  </section>
</body>
</html>
