<?php
include '../admin/connection.php';

// Check if order_id is passed in the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch the order details using the order_id
    $order_sql = "SELECT * FROM orders WHERE order_id = :order_id";
    $order_stmt = oci_parse($conn, $order_sql);
    oci_bind_by_name($order_stmt, ':order_id', $order_id);
    oci_execute($order_stmt);

    $order = oci_fetch_assoc($order_stmt);

    // Fetch the order items associated with the order_id
    $items_sql = "SELECT * FROM order_items WHERE order_id = :order_id";
    $items_stmt = oci_parse($conn, $items_sql);
    oci_bind_by_name($items_stmt, ':order_id', $order_id);
    oci_execute($items_stmt);

    // Close the statements
    oci_free_statement($order_stmt);
    oci_free_statement($items_stmt);
} else {
    echo "<p>Order ID is missing.</p>";
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment & Delivery - UrbanFood</title>
    <link rel="stylesheet" href="payment_delivery.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<!-- Header -->
<header class="header">
  <a href="#" class="logo"><i class="fa fa-shopping-basket"></i> UrbanFood</a>
  <nav class="navbar">
    <a href="home.html">Home</a>
    <a href="products.php">Products</a>
    <a href="suppliers.html">Suppliers</a>
    <a href="feedbacks.html">Feedbacks</a>
  </nav>
  <div class="icons">
    <a href="cart.html"><div class="fas fa-shopping-cart" id="cart-btn"></div></a>
  </div>
</header>

<!-- Order Summary -->
<section class="order-summary">
  <h2>Order Summary</h2>
  <p><strong>Order ID:</strong> <?php echo $order['ORDER_ID']; ?></p>
  <p><strong>Customer Name:</strong> <?php echo $order['CUSTOMER_NAME']; ?></p>
  <p><strong>Address:</strong> <?php echo $order['DELIVERY_ADDRESS']; ?></p>
  <p><strong>Phone:</strong> <?php echo $order['PHONE_NUMBER']; ?></p>
  <p><strong>Email:</strong> <?php echo $order['EMAIL']; ?></p>

  <h3>Items Ordered:</h3>
  <table>
    <thead>
      <tr>
        <th>Product Name</th>
        <th>Price (Rs.)</th>
        <th>Quantity</th>
        <th>Subtotal (Rs.)</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($item = oci_fetch_assoc($items_stmt)) { ?>
        <tr>
          <td><?php echo $item['PRODUCT_NAME']; ?></td>
          <td><?php echo $item['PRICE']; ?></td>
          <td><?php echo $item['QUANTITY']; ?></td>
          <td><?php echo $item['SUBTOTAL']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</section>

<!-- Payment & Delivery Form -->
<section class="payment-form">
  <h2>Choose Payment & Delivery Method</h2>
  <form action="process_payment.php" method="POST">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

    <!-- Payment Methods -->
    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
      <option value="credit_card">Credit Card</option>
      <option value="debit_card">Debit Card</option>
      <option value="cash_on_delivery">Cash on Delivery</option>
    </select>

    <!-- Delivery Methods -->
    <label for="delivery_method">Delivery Method:</label>
    <select name="delivery_method" id="delivery_method" required>
      <option value="standard_delivery">Standard Delivery</option>
      <option value="express_delivery">Express Delivery</option>
    </select>

    <button type="submit">Proceed to Payment</button>
  </form>
</section>

<!-- Footer -->
<footer>
  <p>&copy; 2025 UrbanFood. All rights reserved.</p>
</footer>

</body>
</html>
