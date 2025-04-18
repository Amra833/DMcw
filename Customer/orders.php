<?php
include '../admin/connection.php';

session_start(); // Start session to access login state

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo "<script>
        alert('Please login to place an order.');
        window.location.href = '../Register/login.html';
    </script>";
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];
    $delivery_method = $_POST['delivery_method'];

    // Insert into orders table
    $sql = "INSERT INTO orders (CUSTOMER_NAME, DELIVERY_ADDRESS, PHONE_NUMBER, EMAIL, PAYMENT_METHOD, DELIVERY_METHOD) 
            VALUES (:name, :address, :phone, :email, :payment_method, :delivery_method) RETURNING order_id INTO :order_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':address', $address);
    oci_bind_by_name($stmt, ':phone', $phone);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':payment_method', $payment_method);
    oci_bind_by_name($stmt, ':delivery_method', $delivery_method);

    $order_id = null;
    oci_bind_by_name($stmt, ':order_id', $order_id, -1, SQLT_INT);

    if (oci_execute($stmt)) {
        $cart = json_decode($_POST['cart']);

        foreach ($cart as $item) {
            $insert_item_sql = "INSERT INTO order_items (order_id, product_name, price, quantity, subtotal) 
                                VALUES (:order_id, :product_name, :price, :quantity, :subtotal)";
            $item_stmt = oci_parse($conn, $insert_item_sql);
            oci_bind_by_name($item_stmt, ':order_id', $order_id);
            oci_bind_by_name($item_stmt, ':product_name', $item->name);
            oci_bind_by_name($item_stmt, ':price', $item->price);
            oci_bind_by_name($item_stmt, ':quantity', $item->qty);
            $subtotal = $item->price * $item->qty;
            oci_bind_by_name($item_stmt, ':subtotal', $subtotal);

            oci_execute($item_stmt);
            oci_free_statement($item_stmt);
        }

        oci_free_statement($stmt);
        oci_close($conn);

        echo "<script>
            alert('Thank you! Your order has been placed.');
            localStorage.removeItem('cart');
            window.location.href = 'order_confirmation.php?order_id=' + " . $order_id . ";
        </script>";
        exit();
    } else {
        $err = oci_error($stmt);
        echo "<p>Error: " . htmlspecialchars($err['message']) . "</p>";
        oci_free_statement($stmt);
    }
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order - UrbanFood</title>
  <link rel="stylesheet" href="order.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<header class="header">
  <a href="#" class="logo"><i class="fa fa-shopping-basket"></i> UrbanFood</a>
  <nav class="navbar">
    <a href="home.html">Home</a>
    <a href="products.php">Products</a>
  </nav>
  <div class="icons">
    <a href="cart.html"><div class="fas fa-shopping-cart" id="cart-btn"></div></a>
    <a href="../Register/customer_logout.php"><div class="fa-solid fa-right-from-bracket" id="cart-btn"></div></a>
  </div>
</header>

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

<section class="order-form">
  <h2>Complete Your Order</h2>
  <form method="POST" onsubmit="return submitOrder(this)">
    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="address">Delivery Address:</label>
    <textarea id="address" name="address" required></textarea>

    <label for="phone">Phone Number:</label>
    <input type="tel" id="phone" name="phone" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email">

    <label for="payment_method">Payment Method:</label>
    <select id="payment_method" name="payment_method" required>
      <option value="Cash on Delivery">Cash on Delivery</option>
      <option value="Credit Card">Credit Card</option>
      <option value="Bank Transfer">Bank Transfer</option>
    </select>

    <label for="delivery_method">Delivery Method:</label>
    <select id="delivery_method" name="delivery_method" required>
      <option value="Standard Delivery">Standard Delivery</option>
      <option value="Express Delivery">Express Delivery</option>
    </select>

    <input type="hidden" name="cart" id="cart-data">
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

  function submitOrder(form) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (cart.length === 0) {
      alert("Your cart is empty.");
      return false;
    }
    document.getElementById("cart-data").value = JSON.stringify(cart);
    return true;
  }

  window.onload = displayCartSummary;
</script>

</body>
</html>
