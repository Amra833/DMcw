<?php
include '../admin/connection.php';

// Fetch all orders for the admin
$sql = "SELECT * FROM orders";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
$orders = [];
while ($row = oci_fetch_assoc($stmt)) {
    $orders[] = $row;
}
oci_free_statement($stmt);

// Fetch order items for each order
function getOrderItems($order_id) {
    global $conn;
    $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':order_id', $order_id);
    oci_execute($stmt);
    $order_items = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $order_items[] = $row;
    }
    oci_free_statement($stmt);
    return $order_items;
}

// Delete order (if requested)
if (isset($_GET['delete_order_id'])) {
    $order_id_to_delete = $_GET['delete_order_id'];
    $delete_items_sql = "DELETE FROM order_items WHERE order_id = :order_id";
    $delete_order_sql = "DELETE FROM orders WHERE order_id = :order_id";
    
    // Begin transaction
    oci_execute(oci_parse($conn, "BEGIN"));
    
    $stmt = oci_parse($conn, $delete_items_sql);
    oci_bind_by_name($stmt, ':order_id', $order_id_to_delete);
    oci_execute($stmt);
    oci_free_statement($stmt);

    $stmt = oci_parse($conn, $delete_order_sql);
    oci_bind_by_name($stmt, ':order_id', $order_id_to_delete);
    oci_execute($stmt);
    oci_free_statement($stmt);

    // Commit transaction
    oci_execute(oci_parse($conn, "COMMIT"));
    
    header("Location: admin_orders.php"); // Refresh the page to reflect changes
    exit();
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Manage Orders - Admin</title>
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

    h1 {
        text-align: center;
        margin-top: 20px;
        font-size: 2em;
        color: #333;
    }

    .container {
        width: 80%;
        margin: auto;
        padding: 20px;
    }

    /* Table Styles */
    table {
        width: 120%;
        border-collapse: collapse;
        margin-top: 20px;
        margin-left: -100px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #3498db;
        color: white;
    }

    td {
        background-color: #fafafa;
    }

    tr:nth-child(even) td {
        background-color: #f1f1f1;
    }

    tr:hover td {
        background-color: #e0e0e0;
    }

    /* Button Styles */
    a.view-details-link {
        display: inline-block;
        padding: 10px 10px;
        background-color: #3498db;
        color: white;
        font-weight: bold;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    /* Hover effect for View Details link */
    a.view-details-link:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }

    /* Focus effect for accessibility */
    a.view-details-link:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.5);
    }
    .delete-btn {
        cursor: pointer;
        padding: 8px 15px;
        border-radius: 5px;
        background-color: #e74c3c;
        color: white;
        font-weight: bold;
        transition: background-color 0.3s ease;
        display: inline-block;
        
    }

    .delete-btn:hover {
        background-color: #c0392b;
    }

    /* Confirmation Pop-up Styles */
    .delete-confirmation {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px;
        text-align: center;
        z-index: 1000;
    }

    .delete-confirmation button {
        margin: 20px;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .delete-confirmation .confirm-btn {
        background-color: #e74c3c;
        color: white;
    }

    .delete-confirmation .cancel-btn {
        background-color: #3498db;
        color: white;
    }

    /* Background Overlay Styles */
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    /* Footer Styles */
    footer {
        text-align: center;
        background-color: #3498db;
        color: white;
        padding: 15px;
        margin-top: 20px;
    }

    footer a {
        color: white;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
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
    <h1>Manage Orders</h1>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Delivery Address</th>
                <th>Email</th>
                <th>Payment Method</th>
                <th>Delivery Method</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['ORDER_ID']) ?></td>
                    <td><?= htmlspecialchars($order['CUSTOMER_NAME']) ?></td>
                    <td><?= htmlspecialchars($order['DELIVERY_ADDRESS']) ?></td>
                    <td><?= htmlspecialchars($order['EMAIL']) ?></td>
                    <td><?= htmlspecialchars($order['PAYMENT_METHOD']) ?></td>
                    <td><?= htmlspecialchars($order['DELIVERY_METHOD']) ?></td>
                    <td>
                    <a href="order_details.php?order_id=<?= htmlspecialchars($order['ORDER_ID']) ?>" class="view-details-link">View Details</a>
                        <span class="delete-btn" onclick="confirmDelete(<?= htmlspecialchars($order['ORDER_ID']) ?>)">Delete</span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script>
function confirmDelete(order_id) {
    if (confirm("Are you sure you want to delete this order?")) {
        window.location.href = "admin_orders.php?delete_order_id=" + order_id;
    }
}
</script>

</body>
</html>
