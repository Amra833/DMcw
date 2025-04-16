<?php
session_start();
include 'connection.php'; // your Oracle DB connection

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle product insert
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];
    $product_image = $_FILES['product_image']['name'];
    $temp_name = $_FILES['product_image']['tmp_name'];
    $product_id = time(); // unique ID

    if (move_uploaded_file($temp_name, $uploadDir . $product_image)) {
        $sql = "INSERT INTO PRODUCTS (product_id, product_name, product_price, product_image, category) 
                VALUES (:id, :name, :price, :image, :category)";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":id", $product_id);
        oci_bind_by_name($stmt, ":name", $product_name);
        oci_bind_by_name($stmt, ":price", $product_price);
        oci_bind_by_name($stmt, ":image", $product_image);
        oci_bind_by_name($stmt, ":category", $product_category);
        oci_execute($stmt);
    } else {
        echo "<p style='color:red;'>Image upload failed!</p>";
    }
}

// Handle product delete
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $sql = "DELETE FROM PRODUCTS WHERE product_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $product_id);
    oci_execute($stmt);
    header("Location: admin_products.php");
    exit();
}

// Handle update form
$edit_product = null;
if (isset($_GET['edit'])) {
    $product_id = $_GET['edit'];
    $sql = "SELECT * FROM PRODUCTS WHERE product_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $product_id);
    oci_execute($stmt);
    $edit_product = oci_fetch_assoc($stmt);
}

// Handle update submission
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];

    // Check if new image uploaded
    if ($_FILES['product_image']['name']) {
        $product_image = $_FILES['product_image']['name'];
        $temp_name = $_FILES['product_image']['tmp_name'];
        move_uploaded_file($temp_name, $uploadDir . $product_image);
        $sql = "UPDATE PRODUCTS SET product_name = :name, product_price = :price, category = :category, product_image = :image WHERE product_id = :id";
    } else {
        $product_image = $edit_product['PRODUCT_IMAGE'];
        $sql = "UPDATE PRODUCTS SET product_name = :name, product_price = :price, category = :category WHERE product_id = :id";
    }

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $product_id);
    oci_bind_by_name($stmt, ":name", $product_name);
    oci_bind_by_name($stmt, ":price", $product_price);
    oci_bind_by_name($stmt, ":category", $product_category);
    if ($_FILES['product_image']['name']) {
        oci_bind_by_name($stmt, ":image", $product_image);
    }
    oci_execute($stmt);
    header("Location: admin_products.php");
    exit();
}

// Fetch all products
$query = "SELECT * FROM PRODUCTS ORDER BY product_id DESC";
$result = oci_parse($conn, $query);
oci_execute($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Products</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
        }

        h3 {
            text-align: center;
            color: #34495e;
        }

        form {
            background: #fff;
            padding: 20px;
            margin-bottom: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
            max-width: 500px;
            margin: 0 auto 40px auto;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        img {
            width: 100px;
            height: auto;
            border-radius: 6px;
        }

        .btn {
            padding: 8px 15px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            margin: 3px;
            display: inline-block;
        }

        .btn-success { background-color: #28a745; }
        .btn-danger { background-color: #dc3545; }
        .btn-warning { background-color: #f39c12; }

        .table-actions a {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<h2>Admin - Product Management</h2>

<?php if ($edit_product): ?>
    <h3>Update Product</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= $edit_product['PRODUCT_ID'] ?>">

        <label>Product Name:</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($edit_product['PRODUCT_NAME']) ?>" required>

        <label>Product Price:</label>
        <input type="number" step="0.01" name="product_price" value="<?= $edit_product['PRODUCT_PRICE'] ?>" required>

        <label>Category:</label>
        <select name="product_category" required>
            <?php
            $categories = ['Fruits', 'Vegetables', 'Dairy Products', 'Baked Goods', 'Handmade Crafts'];
            foreach ($categories as $cat) {
                $selected = ($cat == $edit_product['CATEGORY']) ? 'selected' : '';
                echo "<option value=\"$cat\" $selected>$cat</option>";
            }
            ?>
        </select>

        <label>Update Image (optional):</label>
        <input type="file" name="product_image" accept="image/*">

        <input type="submit" name="update_product" value="Update Product" class="btn btn-warning">
    </form>
<?php else: ?>
    <h3>Add New Product</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="product_name" required>

        <label>Product Price:</label>
        <input type="number" name="product_price" step="0.01" required>

        <label>Category:</label>
        <select name="product_category" required>
            <option value="Fruits">Fruits</option>
            <option value="Vegetables">Vegetables</option>
            <option value="Dairy Products">Dairy Products</option>
            <option value="Baked Goods">Baked Goods</option>
            <option value="Handmade Crafts">Handmade Crafts</option>
        </select>

        <label>Product Image:</label>
        <input type="file" name="product_image" accept="image/*" required>

        <input type="submit" name="add_product" value="Add Product" class="btn btn-success">
    </form>
<?php endif; ?>

<h3>Product List</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Price (LKR)</th>
        <th>Category</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = oci_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['PRODUCT_ID'] ?></td>
            <td><img src="uploads/<?= $row['PRODUCT_IMAGE'] ?>" alt=""></td>
            <td><?= htmlspecialchars($row['PRODUCT_NAME']) ?></td>
            <td><?= number_format($row['PRODUCT_PRICE'], 2) ?></td>
            <td><?= htmlspecialchars($row['CATEGORY']) ?></td>
            <td class="table-actions">
                <a href="?edit=<?= $row['PRODUCT_ID'] ?>" class="btn btn-warning">Update</a>
                <a href="?delete=<?= $row['PRODUCT_ID'] ?>" class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
