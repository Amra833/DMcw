<?php
// Set the Oracle bin path (adjust if your path is different)
putenv("PATH=C:\\app\\user\\product\\21c\\dbhomeXE\\bin;" . getenv("PATH"));

// Oracle DB credentials
$username = 'system';
$password = 'system';  // Replace with actual password

// Correct connection string using SERVICE NAME (not SID)
$connection_string = '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=XEPDB1)))';

// Connect using oci_connect
$conn = oci_connect($username, $password, $connection_string);

// Error handling
if (!$conn) {
    $e = oci_error();
    die("❌ Connection failed: " . $e['message']);
}

echo "✅ Connected successfully!";
?>
