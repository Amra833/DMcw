<?php
// Database connection
$connection = oci_connect("system", "system", "//localhost/XE");
if (!$connection) {
    die("Connection failed: " . oci_error()['message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $sql = "INSERT INTO orders (CUSTOMER_NAME, DELIVERY_ADDRESS, PHONE_NUMBER, EMAIL) 
            VALUES (:name, :address, :phone, :email)";
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':address', $address);
    oci_bind_by_name($stmt, ':phone', $phone);
    oci_bind_by_name($stmt, ':email', $email);

    if (oci_execute($stmt)) {
        echo "<script>alert('Thank you! Your order has been placed.');</script>";
        echo "<script>localStorage.removeItem('cart'); window.location.href = 'payment_delivery.php';</script>";
    } else {
        $err = oci_error($stmt);
        echo "<p>Error processing order: " . htmlspecialchars($err['message']) . "</p>";
    }
    oci_free_statement($stmt);
}

oci_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order - UrbanFood</title>
  <link rel="stylesheet" href="order.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<!-- Header with navbar -->
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

<!-- Cart Summary -->
<section class="cart-summary">
  <h2>Your Cart Summary</h2>
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Price (Rs.)</th>
        <th>Quantity</th>
        <th>Subtotal (Rs.)</th>
      </tr>
    </thead>
    <tbody id="summary-body"></tbody>
  </table>
  <p><strong>Total: Rs. <span id="summary-total">0</span></strong></p>
</section>

<!-- Order Form -->
<section class="order-form">
  <h2>Complete Your Order</h2>
  <form method="POST">
    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="address">Delivery Address:</label>
    <textarea id="address" name="address" required></textarea>

    <label for="phone">Phone Number:</label>
    <input type="tel" id="phone" name="phone" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email">

    <button type="submit">Place Order</button>
  </form>
</section>

<script>
  function displayCartSummary() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const tbody = document.getElementById("summary-body");
    const totalDisplay = document.getElementById("summary-total");
    let total = 0;
    tbody.innerHTML = "";

    cart.forEach(item => {
      const subtotal = item.price * item.qty;
      total += subtotal;
      tbody.innerHTML += `
        <tr>
          <td>${item.name}</td>
          <td>${item.price}</td>
          <td>${item.qty}</td>
          <td>${subtotal}</td>
        </tr>
      `;
    });

    totalDisplay.textContent = total;
  }

  window.onload = displayCartSummary;
</script>

</body>
</html>
