<?php
include '../admin/connection.php';

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

if (!$order_details) {
    echo "<p>Error: Invalid Order ID.</p>";
    exit;
}

// Fetch order items
$order_items = getOrderItems($order_id);

oci_close($conn);

function getOrderItems($order_id) {
    global $conn;
    $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':order_id', $order_id);
    oci_execute($stmt);
    $items = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $items[] = $row;
    }
    oci_free_statement($stmt);
    return $items;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Order Details - Admin</title>
    <style>
     * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f0f2f5;
    }

    header {
        background-color:rgb(127, 146, 182);
        padding: 20px 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    header .logo {
        font-size: 24px;
        font-weight: bold;
        text-decoration: none;
        color:rgb(3, 25, 58);
    }

    .header .logo i{
    color: #ff8800;
    }

    .navbar {
        display: flex;
        gap: 20px;
        margin: 0 auto 10px;
    }

    .navbar a {
        color: black;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .navbar a:hover {
        text-decoration: none;
        color:rgb(223, 94, 8);
    }

    .header .icons div{
    height: 2.5rem;
    width: 3.5rem;
    line-height: 2.5rem;
    border-radius: .5rem;
    background: #eee;
    color: var(--black);
    font-size: 2rem;
    cursor: pointer;
    margin-right: .3rem;
    text-align: center;
    margin: 0 auto -30px 300px;
    }

    .header .icons div:hover{
    background:rgb(223, 94, 8);
    color: #fff;
    }

    /* Container to center content */
    .container {
        width: 80%;
        margin: 30px auto;
        overflow: hidden;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Header */
    h1 {
        text-align: center;
        color: #3498db;
        margin-bottom: 20px;
    }

    h2 {
        color: #2c3e50;
        font-size: 1.5em;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    p {
        font-size: 1.1em;
        margin-bottom: 10px;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }

    th {
        background-color: #f4f4f4;
        color: #34495e;
    }

    td {
        background-color: #fff;
        color: #7f8c8d;
    }

    /* Number formatting */
    td:nth-child(2), td:nth-child(4) {
        text-align: right;
    }

    td:nth-child(3) {
        text-align: center;
    }

    /* Button Styles */
    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-weight: bold;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.3s ease;
        cursor: pointer;
    }

    /* View Details Button */
    .view-details-btn {
        background-color: #3498db;
        color: white;
        border: 2px solid #2980b9;
        margin-top: 20px;
    }

    .view-details-btn:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }

    /* Delete Button */
    .delete-btn {
        background-color: #e74c3c;
        color: white;
        border: 2px solid #c0392b;
        margin-top: 20px;
        margin-left: 10px;
    }

    .delete-btn:hover {
        background-color: #c0392b;
        transform: scale(1.05);
    }

    /* Focus Effect for Buttons */
    .btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.5);
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .container {
            width: 95%;
        }

        table {
            font-size: 0.9em;
        }

        th, td {
            padding: 8px 10px;
        }
    }
</style>
<header class="header">
    <a href="#" class="logo"><i class="fa fa-shopping-basket"></i> UrbanFood</a>
    <nav class="navbar">
      <a href="dashboard.php">Dashboard</a>
      <a href="admin_products.php">Products</a>
      <a href="admin_suppliers.php">Supplier Profiles</a>
      <a href="admin_view_feedbacks.php">Customer Feedbacks</a>
      <a href="admin_orders.php">Order Details</a>

    <div class="icons">
           <a href="logout.php"><div class="fa-solid fa-right-from-bracket"></div></a>
    </div>
    </nav>
  </header>
</head>
<body>

<div class="container">
    <h1>Order Details - Order ID: <?= htmlspecialchars($order_details['ORDER_ID']) ?></h1>

    <h2>Customer Information</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($order_details['CUSTOMER_NAME']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($order_details['DELIVERY_ADDRESS']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order_details['EMAIL']) ?></p>

    <h2>Order Items</h2>
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
        </tbody>
    </table>

</div>

</body>
</html>
