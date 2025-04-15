<?php
// Oracle connection setup
putenv("PATH=C:\\app\\user\\product\\21c\\dbhomeXE\\bin;" . getenv("PATH"));
$username = 'system';  // Oracle DB username
$password = 'system';  // Oracle DB password
$connection_string = 'localhost/XEPDB1';  // Oracle 21c XE default connection

$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("❌ Connection failed: " . $e['message']);
}

// Get form input
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate that inputs are not empty
if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
    echo "<script>alert('Please fill in all fields.'); window.location.href = 'register.html';</script>";
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format!'); window.location.href = 'register.html';</script>";
    exit();
}

// Validate passwords match
if ($password !== $confirm_password) {
    echo "<script>alert('Passwords do not match!'); window.location.href = 'register.html';</script>";
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Check if the email already exists
$sql_check = "SELECT 1 FROM USERS WHERE email = :email";
$stid_check = oci_parse($conn, $sql_check);
oci_bind_by_name($stid_check, ":email", $email);
oci_execute($stid_check);

$email_exists = oci_fetch($stid_check);  // Use oci_fetch to check if a row exists

if ($email_exists) {
    echo "<script>alert('Email already registered!'); window.location.href = 'register.html';</script>";
    oci_free_statement($stid_check);
    oci_close($conn);
    exit();
}
oci_free_statement($stid_check); // Free check statement

// Insert new user
$sql_insert = "INSERT INTO USERS (fullname, email, password) VALUES (:fullname, :email, :password)";
$stid_insert = oci_parse($conn, $sql_insert);
oci_bind_by_name($stid_insert, ":fullname", $fullname);
oci_bind_by_name($stid_insert, ":email", $email);
oci_bind_by_name($stid_insert, ":password", $hashed_password);

if (oci_execute($stid_insert)) {
    echo "<script>alert('Registration successful!'); window.location.href = 'login.html';</script>";
} else {
    $e = oci_error($stid_insert);
    echo "<script>alert('Error during registration: " . $e['message'] . "'); window.location.href = 'register.html';</script>";
}

oci_free_statement($stid_insert);
oci_close($conn);
?>