<?php
// Database connection to Oracle
$connection = oci_connect("system", "system", "//localhost/XE");
if (!$connection) {
    die("Connection failed: " . oci_error()['message']);
}

// Fetch all orders with their items
$sql = "
SELECT 
    o.ORDER_ID, 
    o.CUSTOMER_NAME, 
    o.DELIVERY_ADDRESS, 
    o.PHONE_NUMBER, 
    o.EMAIL, 
    o.ORDER_DATE, 
    o.ORDER_STATUS,   -- Added ORDER_STATUS to the SELECT statement
    oi.PRODUCT_NAME, 
    oi.PRICE, 
    oi.QUANTITY, 
    oi.SUBTOTAL
FROM 
    ORDERS o
JOIN 
    ORDER_ITEMS oi ON o.ORDER_ID = oi.ORDER_ID
ORDER BY 
    o.ORDER_DATE DESC
";

$stmt = oci_parse($connection, $sql);

// Check if the SQL query executed successfully
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("SQL Error: " . $e['message']);
}

// Handle Order Status Update
if (isset($_GET['update_status']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $new_status = htmlspecialchars($_GET['update_status']);  // Sanitize the input
    
    $update_sql = "UPDATE ORDERS SET ORDER_STATUS = :new_status WHERE ORDER_ID = :order_id";
    $update_stmt = oci_parse($connection, $update_sql);
    oci_bind_by_name($update_stmt, ':new_status', $new_status);
    oci_bind_by_name($update_stmt, ':order_id', $order_id);
    
    if (oci_execute($update_stmt)) {
        echo "<script>alert('Order status updated successfully.');</script>";
    } else {
        echo "<script>alert('Failed to update order status.');</script>";
    }
    oci_free_statement($update_stmt);
}

// Handle Order Deletion
if (isset($_GET['delete_order']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // First, delete items from the ORDER_ITEMS table to maintain referential integrity
    $delete_items_sql = "DELETE FROM ORDER_ITEMS WHERE ORDER_ID = :order_id";
    $delete_items_stmt = oci_parse($connection, $delete_items_sql);
    oci_bind_by_name($delete_items_stmt, ':order_id', $order_id);
    oci_execute($delete_items_stmt);
    oci_free_statement($delete_items_stmt);
    
    // Then, delete from the ORDERS table
    $delete_order_sql = "DELETE FROM ORDERS WHERE ORDER_ID = :order_id";
    $delete_order_stmt = oci_parse($connection, $delete_order_sql);
    oci_bind_by_name($delete_order_stmt, ':order_id', $order_id);
    
    if (oci_execute($delete_order_stmt)) {
        echo "<script>alert('Order deleted successfully.');</script>";
    } else {
        echo "<script>alert('Failed to delete order.');</script>";
    }
    oci_free_statement($delete_order_stmt);
}

// Close the database connection
oci_free_statement($stmt);
oci_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Orders - UrbanFood</title>
  <link rel="stylesheet" href="admin_orders.css">
</head>
<body>

  <!-- Admin Dashboard Header -->
  <header>
    <h1>Admin Panel - Order Management</h1>
    <nav>
      <a href="admin_home.php">Home</a>
      <a href="admin_orders.php">Orders</a>
      <a href="admin_products.php">Products</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <!-- Orders Table -->
  <section>
    <h2>All Orders</h2>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Name</th>
          <th>Address</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Order Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch and display each order in the table
        while ($row = oci_fetch_assoc($stmt)) {
            echo "
            <tr>
                <td>{$row['ORDER_ID']}</td>
                <td>{$row['CUSTOMER_NAME']}</td>
                <td>{$row['DELIVERY_ADDRESS']}</td>
                <td>{$row['PHONE_NUMBER']}</td>
                <td>{$row['EMAIL']}</td>
                <td>{$row['PRODUCT_NAME']}</td>
                <td>{$row['PRICE']}</td>
                <td>{$row['QUANTITY']}</td>
                <td>{$row['SUBTOTAL']}</td>
                <td>{$row['ORDER_DATE']}</td>
                <td>{$row['ORDER_STATUS']}</td>
                <td>
                    <a href='admin_orders.php?update_status=Processed&order_id={$row['ORDER_ID']}'>Mark as Processed</a>
                    <a href='admin_orders.php?update_status=Shipped&order_id={$row['ORDER_ID']}'>Mark as Shipped</a>
                    <a href='admin_orders.php?update_status=Cancelled&order_id={$row['ORDER_ID']}'>Cancel</a>
                    <a href='admin_orders.php?delete_order=true&order_id={$row['ORDER_ID']}'>Delete</a>
                </td>
            </tr>";
        }
        ?>
      </tbody>
    </table>
  </section>

</body>
</html>
