<?php
$connection = oci_connect("system", "system", "//localhost/XE");
if (!$connection) {
    die("Connection failed: " . oci_error()['message']);
}

$order_id = $_GET['order_id'];

// Fetch the order details
$sql = "SELECT * FROM orders WHERE ORDER_ID = :order_id";
$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ':order_id', $order_id);
oci_execute($stmt);
$order = oci_fetch_assoc($stmt);

// Fetch order items
$sql_items = "SELECT oi.PRODUCT_ID, p.PRODUCT_NAME, oi.PRICE, oi.QUANTITY 
              FROM order_items oi 
              JOIN products p ON oi.PRODUCT_ID = p.PRODUCT_ID 
              WHERE oi.ORDER_ID = :order_id";
$stmt_items = oci_parse($connection, $sql_items);
oci_bind_by_name($stmt_items, ':order_id', $order_id);
oci_execute($stmt_items);

// Fetch order items into an array
$order_items = [];
while ($item = oci_fetch_assoc($stmt_items)) {
    $order_items[] = $item;
}

oci_free_statement($stmt);
oci_free_statement($stmt_items);
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

    <!-- Display Order Items -->
    <h3>Order Items</h3>
    <table>
      <tr>
        <th>Product Name</th>
        <th>Price (Rs.)</th>
        <th>Quantity</th>
        <th>Total (Rs.)</th>
      </tr>
      <?php foreach ($order_items as $item): ?>
        <tr>
          <td><?php echo $item['PRODUCT_NAME']; ?></td>
          <td><?php echo $item['PRICE']; ?></td>
          <td><?php echo $item['QUANTITY']; ?></td>
          <td><?php echo $item['PRICE'] * $item['QUANTITY']; ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>
</body>
</html>
