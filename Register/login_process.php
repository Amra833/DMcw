<?php
session_start(); // Start the session

// Oracle DB connection setup
putenv("PATH=C:\\app\\user\\product\\21c\\dbhomeXE\\bin;" . getenv("PATH"));
$username = 'system';         // Oracle DB username
$password = 'system';         // Oracle DB password
$connection_string = 'localhost/XEPDB1';

// Establish Oracle connection
$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("❌ Connection failed: " . $e['message']);
}

// Get form input
$email = $_POST['email'] ?? '';
$input_password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($input_password)) {
    echo "<script>alert('Please enter both email and password.'); window.location.href = 'login.html';</script>";
    exit();
}

// Prepare and execute query
$sql = "SELECT password FROM USERS WHERE email = :email";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":email", $email);
oci_execute($stid);

// Fetch result
$row = oci_fetch_assoc($stid);

// Free resources and close connection
oci_free_statement($stid);
oci_close($conn);

// Check login credentials
if ($row && password_verify($input_password, $row['PASSWORD'])) {
    // ✅ Login success - set session and redirect
    $_SESSION['user_email'] = $email; // Store email in session
    header("Location: ../Customer/orders.php");
    exit();
} else {
    // ❌ Login failed
    echo "<script>alert('Invalid email or password'); window.location.href = 'login.html';</script>";
    exit();
}
?>