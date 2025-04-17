<?php
// Database connection
$connection = oci_connect("system", "system", "//localhost/XE");
if (!$connection) {
    $error = oci_error();
    die("❌ Connection failed: " . $error['message']);
}

// Handle product submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Handle image upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $upload_path = 'uploads/' . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
    }

    // Insert product into DB
    $sql = "INSERT INTO PRODUCTS (PRODUCT_NAME, PRICE, CATEGORY, IMAGE_NAME)
            VALUES (:name, :price, :category, :image)";
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':price', $price);
    oci_bind_by_name($stmt, ':category', $category);
    oci_bind_by_name($stmt, ':image', $image_name);

    if (oci_execute($stmt)) {
        echo "<script>alert('Product added successfully');</script>";
    } else {
        $error = oci_error($stmt);
        echo "❌ Error adding product: " . $error['message'];
    }

    oci_free_statement($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Products</title>
    <link rel="stylesheet" href="admin_products.css">
</head>
<body>

<header>
    <h1>Admin - Manage Products</h1>
</header>

<section>
    <h2>Add New Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="product_name" required>

        <label>Price:</label>
        <input type="number" name="price" step="0.01" required>

        <label>Category:</label>
        <select name="category" required>
            <option value="Fruits">Fruits</option>
            <option value="Vegetables">Vegetables</option>
            <option value="Dairy">Dairy</option>
            <option value="Baked Goods">Baked Goods</option>
            <option value="Crafts">Crafts</option>
        </select>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" name="add_product">Add Product</button>
    </form>
</section>

<section>
    <h2>Product List</h2>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM PRODUCTS";
        $stmt = oci_parse($connection, $sql);

        if (oci_execute($stmt)) {
            while ($row = oci_fetch_assoc($stmt)) {
                echo "<tr>";
                echo "<td><img src='uploads/" . htmlspecialchars($row['IMAGE_NAME']) . "' width='60'></td>";
                echo "<td>" . htmlspecialchars($row['PRODUCT_NAME']) . "</td>";
                echo "<td>Rs. " . htmlspecialchars($row['PRICE']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CATEGORY']) . "</td>";
                echo "</tr>";
            }
        } else {
            $error = oci_error($stmt);
            echo "<tr><td colspan='4'>❌ Query error: " . $error['message'] . "</td></tr>";
        }

        oci_free_statement($stmt);
        oci_close($connection);
        ?>
        </tbody>
    </table>
</section>

</body>
</html>
