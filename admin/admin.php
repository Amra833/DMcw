<?php
session_start();
include 'connection.php'; // Make sure this uses oci_connect()

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get logged in user (or fallback to 'Guest')
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Initialize counts
$suppliers_count = 0;
$orders_count = 0;
$total_income = 0.00;

// Query to count all suppliers
$supplier_query = "SELECT COUNT(*) AS total FROM suppliers";
$supplier_stmt = oci_parse($conn, $supplier_query);
oci_execute($supplier_stmt);
$row = oci_fetch_assoc($supplier_stmt);
$suppliers_count = $row['TOTAL'];

// Query to count all orders
$order_query = "SELECT COUNT(*) AS total FROM orders";
$order_stmt = oci_parse($conn, $order_query);
oci_execute($order_stmt);
$row = oci_fetch_assoc($order_stmt);
$orders_count = $row['TOTAL'];

// Get current month and year
$current_month = date('m');
$current_year = date('Y');

// Query to calculate total income for the current month
$income_query = "
    SELECT SUM(amount) AS total 
    FROM payments 
    WHERE EXTRACT(MONTH FROM payment_date) = :month AND EXTRACT(YEAR FROM payment_date) = :year
";
$income_stmt = oci_parse($conn, $income_query);
oci_bind_by_name($income_stmt, ":month", $current_month);
oci_bind_by_name($income_stmt, ":year", $current_year);
oci_execute($income_stmt);
$row = oci_fetch_assoc($income_stmt);
$total_income = $row['TOTAL'] ? (float)$row['TOTAL'] : 0.00;

// Prepare response as JSON
$response = [
    'supplierCount' => $suppliers_count,
    'orderCount' => $orders_count,
    'totalIncome' => number_format($total_income, 2),
    'date' => date('d.m.Y'),
    'username' => $username
];

// Set JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Free resources
oci_free_statement($supplier_stmt);
oci_free_statement($order_stmt);
oci_free_statement($income_stmt);
oci_close($conn);
?>
