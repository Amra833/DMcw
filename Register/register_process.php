<?php
// Oracle connection setup
putenv("PATH=C:\\app\\user\\product\\21c\\dbhomeXE\\bin;" . getenv("PATH"));
$username = 'system';         // Oracle DB username
$password = 'system';         // Oracle DB password
$connection_string = 'localhost/XEPDB1';

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

// Check if the email already exists in the database
$sql_check = "SELECT * FROM USERS WHERE email = :email";
$stid_check = oci_parse($conn, $sql_check);
oci_bind_by_name($stid_check, ":email", $email);
oci_execute($stid_check);

if (oci_fetch_assoc($stid_check)) {
    echo "<script>alert('Email already registered!'); window.location.href = 'register.html';</script>";
    exit();
}

// Insert new user into the database
$sql = "INSERT INTO USERS (fullname, email, password) VALUES (:fullname, :email, :password)";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":fullname", $fullname);
oci_bind_by_name($stid, ":email", $email);
oci_bind_by_name($stid, ":password", $hashed_password);

if (oci_execute($stid)) {
    // ✅ Registration success
    echo "<script>alert('Registration successful!'); window.location.href = 'login.html';</script>";
} else {
    // ❌ Registration failed
    $e = oci_error($stid);
    echo "<script>alert('Error: " . $e['message'] . "'); window.location.href = 'register.html';</script>";
}

oci_free_statement($stid);
oci_free_statement($stid_check);
oci_close($conn);
?>
