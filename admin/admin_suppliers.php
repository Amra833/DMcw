<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Admin | Manage Suppliers</title>
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

    h2, h3 {
        text-align: center;
        color: #2c3e50;
    }

    /* Add Supplier Form */
    form {
        background-color: #ffffff;
        padding: 20px;
        margin: 20px auto;
        border-radius: 10px;
        max-width: 600px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    form label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
        color: #34495e;
    }

    form input[type="text"],
    form input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    form input[type="submit"] {
        margin-top: 15px;
        background-color:rgb(18, 57, 109);
        color: white;
        border: none;
        padding: 12px 20px;
        cursor: pointer;
        border-radius: 6px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    form input[type="submit"]:hover {
        background-color:rgb(21, 147, 151);
    }

    /* Supplier Table */
    table {
        margin: 30px auto;
        width: 90%;
        border-collapse: collapse;
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    table th, table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }

    table th {
        background-color:rgb(26, 96, 143);
        color: white;
    }

    table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    img {
        max-width: 100px;
        height: auto;
        border-radius: 6px;
    }

    /* Update Form */
    #update_form {
        background-color: #f9f9f9;
        padding: 15px;
        margin-top: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #update_form input[type="text"],
    #update_form input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    #update_form input[type="submit"] {
        margin-top: 10px;
        background-color: #f39c12;
        color: white;
        padding: 12px 20px;
        cursor: pointer;
        border-radius: 6px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    #update_form input[type="submit"]:hover {
        background-color: #e67e22;
    }

    /* Button Styling */
    button {
        background-color: #3498db;
        color: white;
        padding: 8px 15px;
        cursor: pointer;
        border-radius: 6px;
        border: none;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #2980b9;
    }

    input[type="submit"] {
        background-color: #e74c3c;
    }

    input[type="submit"]:hover {
        background-color: #c0392b;
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
