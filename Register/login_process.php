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
$email = $_POST['email'] ?? '';
$input_password = $_POST['password'] ?? '';

// Prepare and execute query
$sql = "SELECT password FROM users WHERE email = :email";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":email", $email);
oci_execute($stid);

$row = oci_fetch_assoc($stid);
oci_close($conn);

if ($row && password_verify($input_password, $row['PASSWORD'])) {
    // ✅ Login success
    header("Location: dashboard.html");
    exit();
} else {
    // ❌ Login failed
    echo "<script>alert('Invalid email or password'); window.location.href = 'login.html';</script>";
}
?>
