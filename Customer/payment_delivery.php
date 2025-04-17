<?php
// Database connection
$connection = oci_connect("system", "system", "//localhost/XE");
if (!$connection) {
    die("Connection failed: " . oci_error()['message']);
}

// Check if 'order_id' is passed in the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    echo "<p>Order ID: $order_id</p>"; // Debugging output
} else {
    die("Order ID is missing.");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get payment and delivery method details
    $payment_method = $_POST['payment_method'];
    $delivery_method = $_POST['delivery_method'];

    // Update the order with payment and delivery information
    $sql = "UPDATE orders 
            SET PAYMENT_METHOD = :payment_method, DELIVERY_METHOD = :delivery_method, ORDER_STATUS = 'Pending' 
            WHERE ORDER_ID = :order_id";
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ':payment_method', $payment_method);
    oci_bind_by_name($stmt, ':delivery_method', $delivery_method);
    oci_bind_by_name($stmt, ':order_id', $order_id);

    if (oci_execute($stmt)) {
        // Get the cart items (assuming cart is stored in cookies as a JSON string)
        if (isset($_COOKIE['cart'])) {
            $cart = json_decode($_COOKIE['cart'], true);  // Decode the cart JSON

            // Insert cart items into the order_items table
            foreach ($cart as $item) {
                $product_id = $item['id'];
                $price = $item['price'];
                $quantity = $item['qty'];

                $sql = "INSERT INTO order_items (ORDER_ID, PRODUCT_ID, PRICE, QUANTITY) 
                        VALUES (:order_id, :product_id, :price, :quantity)";
                $stmt = oci_parse($connection, $sql);
                oci_bind_by_name($stmt, ':order_id', $order_id);
                oci_bind_by_name($stmt, ':product_id', $product_id);
                oci_bind_by_name($stmt, ':price', $price);
                oci_bind_by_name($stmt, ':quantity', $quantity);
                oci_execute($stmt);
                oci_free_statement($stmt);
            }
        }

        // Redirect to order confirmation page after successful order placement
        echo "<script>alert('Order placed successfully.');</script>";
        echo "<script>window.location.href = 'order_confirmation.php?order_id=$order_id';</script>";
    } else {
        $err = oci_error($stmt);
        echo "<p>Error processing payment and delivery: " . htmlspecialchars($err['message']) . "</p>";
    }
    oci_free_statement($stmt);
}

// Close the database connection
oci_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment & Delivery - UrbanFood</title>
  <link rel="stylesheet" href="payment_delivery.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<!-- Header with Navbar -->
<header class="header">
  <a href="#" class="logo"><i class="fa fa-shopping-basket"></i> UrbanFood</a>
  <nav class="navbar">
    <a href="home.html">Home</a>
    <a href="products.html">Products</a>
    <a href="suppliers.html">Suppliers</a>
    <a href="feedbacks.html">Feedbacks</a>
  </nav>

  <div class="icons">
    <a href="cart.html"><div class="fas fa-shopping-cart" id="cart-btn"></div></a>
  </div>
</header>

<!-- Check if order_id is available and then display the form -->
<?php if (isset($order_id)): ?>
  <!-- Payment & Delivery Form -->
  <section class="payment-delivery-form">
    <form method="POST">
      <h2>Choose Payment Method</h2>
      <select name="payment_method" required>
        <option value="Credit Card">Credit Card</option>
        <option value="PayPal">PayPal</option>
        <option value="Cash on Delivery">Cash on Delivery</option>
      </select>

      <h2>Choose Delivery Method</h2>
      <select name="delivery_method" required>
        <option value="Standard">Standard Delivery</option>
        <option value="Express">Express Delivery</option>
      </select>

      <button type="submit">Confirm Order</button>
    </form>
  </section>
<?php else: ?>
  <p>Error: Order ID is missing.</p>
<?php endif; ?>

</body>
</html>
