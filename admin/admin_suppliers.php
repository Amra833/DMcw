<?php
session_start();
include 'connection.php'; // Oracle DB connection

// Handle supplier delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "BEGIN :result := delete_supplier(:id); END;";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);
    oci_bind_by_name($stmt, ":result", $result, 4000);
    oci_execute($stmt);
    header("Location: admin_suppliers.php");
    exit();
}

// Handle supplier update
if (isset($_POST['update_supplier'])) {
    $id = $_POST['sup_id'];
    $name = $_POST['sup_name'];
    $location = $_POST['sup_location'];
    $product = $_POST['sup_product'];
    $photo_path = '';

    if (!empty($_FILES['sup_photo']['name'])) {
        $photo = $_FILES['sup_photo']['name'];
        $photo_tmp = $_FILES['sup_photo']['tmp_name'];
        $photo_path = "uploads/" . basename($photo);
        move_uploaded_file($photo_tmp, $photo_path);
    }

    $sql = "BEGIN :result := update_supplier(:id, :name, :location, :product, :photo); END;";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":location", $location);
    oci_bind_by_name($stmt, ":product", $product);
    oci_bind_by_name($stmt, ":photo", $photo_path);
    oci_bind_by_name($stmt, ":result", $result, 4000);
    oci_execute($stmt);
    header("Location: admin_suppliers.php");
    exit();
}

// Handle supplier insert
if (isset($_POST['add_supplier'])) {
    $name = $_POST['sup_name'];
    $location = $_POST['sup_location'];
    $product = $_POST['sup_product'];
    $photo = $_FILES['sup_photo']['name'];
    $photo_tmp = $_FILES['sup_photo']['tmp_name'];
    $photo_path = "uploads/" . basename($photo);
    move_uploaded_file($photo_tmp, $photo_path);

    $sql = "BEGIN :result := insert_supplier(:name, :location, :product, :photo); END;";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":location", $location);
    oci_bind_by_name($stmt, ":product", $product);
    oci_bind_by_name($stmt, ":photo", $photo_path);
    oci_bind_by_name($stmt, ":result", $result, 4000);
    oci_execute($stmt);
    header("Location: admin_suppliers.php");
    exit();
}

// Get all suppliers
$query = "SELECT * FROM SUPPLIERS ORDER BY supplier_id DESC";
$result = oci_parse($conn, $query);
oci_execute($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Suppliers</title>
    <link rel="stylesheet" href="admin_suppliers.css">
    <style>
        img {
            border-radius: 8px;
        }
    </style>
    <script>
        function toggleUpdateForm(id) {
            var form = document.getElementById('update_form_' + id);
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }
    </script>
</head>
<body>

<h2>Admin - Manage Suppliers</h2>

<!-- Add Supplier Form -->
<h3>Add New Supplier</h3>
<form method="POST" enctype="multipart/form-data">
    <label>Full Name:</label>
    <input type="text" name="sup_name" required><br>

    <label>Location:</label>
    <input type="text" name="sup_location" required><br>

    <label>Product:</label>
    <input type="text" name="sup_product" required><br>

    <label>Photo:</label>
    <input type="file" name="sup_photo" accept="image/*" required><br>

    <input type="submit" name="add_supplier" value="Add Supplier">
</form>

<hr>

<!-- Supplier List -->
<h3>Supplier List</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Photo</th>
        <th>Name</th>
        <th>Location</th>
        <th>Product</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = oci_fetch_assoc($result)): ?>
        <tr>
            <td>
                <?php if (!empty($row['SUP_PHOTO']) && file_exists($row['SUP_PHOTO'])): ?>
                    <img src="<?= htmlspecialchars($row['SUP_PHOTO']) ?>" width="100" height="100">
                <?php else: ?>
                    <span>No Image</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['SUP_FULLNAME']) ?></td>
            <td><?= htmlspecialchars($row['SUP_LOCATION']) ?></td>
            <td><?= htmlspecialchars($row['SUP_PRODUCT']) ?></td>
            <td>
                <button type="button" onclick="toggleUpdateForm(<?= $row['SUPPLIER_ID'] ?>)">Update</button>

                <!-- Delete -->
                <form method="GET" style="display:inline-block;">
                    <input type="hidden" name="delete" value="<?= $row['SUPPLIER_ID'] ?>">
                    <input type="submit" value="Delete" onclick="return confirm('Are you sure to delete this supplier?');">
                </form>

                <!-- Update Form -->
                <form id="update_form_<?= $row['SUPPLIER_ID'] ?>" method="POST" enctype="multipart/form-data" style="display:none;">
                    <input type="hidden" name="sup_id" value="<?= $row['SUPPLIER_ID'] ?>">
                    <input type="text" name="sup_name" value="<?= htmlspecialchars($row['SUP_FULLNAME']) ?>" required><br>
                    <input type="text" name="sup_location" value="<?= htmlspecialchars($row['SUP_LOCATION']) ?>" required><br>
                    <input type="text" name="sup_product" value="<?= htmlspecialchars($row['SUP_PRODUCT']) ?>" required><br>
                    <input type="file" name="sup_photo" accept="image/*"><br>
                    <input type="submit" name="update_supplier" value="Update Supplier">
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
