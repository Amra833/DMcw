<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

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
        $sql = "BEGIN insert_product (:name, :price, :category, :image, :result); END;";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":name", $product_name);
        oci_bind_by_name($stmt, ":price", $product_price);
        oci_bind_by_name($stmt, ":category", $product_category);
        oci_bind_by_name($stmt, ":image", $product_image);
        oci_bind_by_name($stmt, ":result", $result, 100);
        oci_execute($stmt);
    } else {
        echo "<p style='color:red;'>Image upload failed!</p>";
    }
}


// Handle product delete
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    $sql = "BEGIN delete_product(:id, :result); END;";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $product_id);
    oci_bind_by_name($stmt, ":result", $result, 100);

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
    } else {
        $product_image = $edit_product['PRODUCT_IMAGE'];
    }
    
    // Now calling the procedure
    $sql = "BEGIN update_product(:id, :name, :price, :category, :image, :result); END;";
    $stmt = oci_parse($conn, $sql);
    
    oci_bind_by_name($stmt, ":id", $product_id);
    oci_bind_by_name($stmt, ":name", $product_name);
    oci_bind_by_name($stmt, ":price", $product_price);
    oci_bind_by_name($stmt, ":category", $product_category);
    oci_bind_by_name($stmt, ":image", $product_image);
    oci_bind_by_name($stmt, ":result", $result, 100);
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Admin - Manage Products</title>
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
    color: #333;
    text-align: center;
    margin-bottom: 25px;
}

form {
    background: #ffffff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
    margin: 0 auto 40px;
    max-width: 600px;
}

label {
    display: block;
    margin: 15px 0 5px;
    color: #333;
    font-weight: 500;
}

input[type="text"],
input[type="number"],
input[type="file"],
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    background-color: #f9f9f9;
}

input[type="submit"],
.btn {
    padding: 10px 20px;
    margin-top: 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    color: white;
    transition: background-color 0.3s ease;
}

.btn-success {
    background-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
}

.btn-warning {
    background-color: #f39c12;
}

.btn-warning:hover {
    background-color: #d68910;
}

.btn-danger {
    background-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
}

table {
    width: 70%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 8px rgba(0,0,0,0.05);
    margin: 0 auto 40px;
}

th, td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

th {
    background-color: #2c3e50;
    color: white;
}

img {
    width: 90px;
    border-radius: 8px;
}

.table-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
}

    </style>
</head>
<body>
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
