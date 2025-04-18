<?php
include '../admin/connection.php';

// Get order ID from the query string
if (!isset($_GET['order_id'])) {
    echo "<p>Error: Order ID is missing.</p>";
    exit;
}
$order_id = $_GET['order_id'];

// Fetch the order details
$sql = "SELECT * FROM orders WHERE order_id = :order_id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':order_id', $order_id);
oci_execute($stmt);
$order_details = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

// If no order found
if (!$order_details) {
    echo "<p>Error: Invalid Order ID.</p>";
    exit;
}

// Fetch order items
$item_sql = "SELECT product_name, quantity, price, subtotal FROM order_items WHERE order_id = :order_id";
$item_stmt = oci_parse($conn, $item_sql);
oci_bind_by_name($item_stmt, ':order_id', $order_id);
oci_execute($item_stmt);
$order_items = [];
while ($row = oci_fetch_assoc($item_stmt)) {
    $order_items[] = $row;
}
oci_free_statement($item_stmt);

// Fetch total amount
$total_sql = "SELECT SUM(subtotal) AS total FROM order_items WHERE order_id = :order_id";
$total_stmt = oci_parse($conn, $total_sql);
oci_bind_by_name($total_stmt, ':order_id', $order_id);
oci_execute($total_stmt);
$total_row = oci_fetch_assoc($total_stmt);
$total_amount = $total_row['TOTAL'];
oci_free_statement($total_stmt);

oci_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
     <!--font awesome cdn link-->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - UrbanFood</title>
        <style>
        :root {
        --orange:#ff7800;
        --green: #27ae60;
        --light-green: #dff5e3;
        --light-orange:#e2b083;
        --dark: #222;
        --white: #fff;
        --gray: #777;
        --border: 0.1rem solid rgba(0, 0, 0, 0.1);
    }
    
    * {
        font-family: 'Poppins', sans-serif;
        margin: 0; padding: 0;
        box-sizing: border-box;
        outline: none; border: none;
        text-decoration: none;
        transition: 0.2s ease;
    }
    
    html {
        font-size: 62.5%;
        scroll-behavior: smooth;
        scroll-padding-top: 7rem;
    }
    
    body {
        background: #f9f9f9;
        padding-top: 10rem;
    }
    
    /* Header */
    .header {
        position: fixed;
        top: 0; left: 0; right: 0;
        background: var(--white);
        padding: 1.5rem 9%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 1000;
        box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    
    .header .logo {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--black);
    }
    
    .header .logo i {
        margin-right: 0.5rem;
        color:var(--orange)
    }
    
    .header .navbar a {
        font-size: 1.6rem;
        margin: 0 1.2rem;
        color: var(--dark);
    }
    
    .header .navbar a:hover {
        color: var(--orange);
    }
    
    .header .icons div {
        font-size: 2rem;
        color: var(--dark);
        margin-left: 1.5rem;
        cursor: pointer;
    }

        .order-confirmation {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .order-confirmation h2 {
            color: #2b9348;
            text-align: center;
            margin-bottom: 20px;
        }

        .order-confirmation p {
            font-size: 16px;
            margin: 8px 0;
        }

        .order-confirmation table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-confirmation th, .order-confirmation td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        .order-confirmation th {
            background-color: #e6f4ea;
        }

        .total-row {
            font-weight: bold;
        }

        @media screen and (max-width: 600px) {
            .order-confirmation {
                padding: 20px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
<header class="header">
  <a href="#" class="logo"><i class="fas fa-shopping-basket"></i> UrbanFood</a>

  <nav class="navbar">
  <a href="home.html">Home</a>
  </nav>

  <div class="icons">
    <a href="../Register/customer_logout.php"><div class="fa-solid fa-right-from-bracket" id="cart-btn"></div></a>
  </div>
</header>

<!-- Order Confirmation -->
<section class="order-confirmation">
    <h2>Thank You for Your Order!</h2>
    <p><strong>Order ID:</strong> <?= htmlspecialchars($order_details['ORDER_ID']) ?></p>
    <p><strong>Customer Name:</strong> <?= htmlspecialchars($order_details['CUSTOMER_NAME']) ?></p>
    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order_details['DELIVERY_ADDRESS']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($order_details['PHONE_NUMBER']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order_details['EMAIL']) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order_details['PAYMENT_METHOD']) ?></p>
    <p><strong>Delivery Method:</strong> <?= htmlspecialchars($order_details['DELIVERY_METHOD']) ?></p>

    <h3>Order Summary</h3>
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
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['PRODUCT_NAME']) ?></td>
                    <td><?= number_format($item['PRICE'], 2) ?></td>
                    <td><?= htmlspecialchars($item['QUANTITY']) ?></td>
                    <td><?= number_format($item['SUBTOTAL'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">Total Amount</td>
                <td>Rs. <?= number_format($total_amount, 2) ?></td>
            </tr>
        </tbody>
    </table>

</section>

</body>
</html>
