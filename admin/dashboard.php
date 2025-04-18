<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="admin.css" />
</head>

<body>
  <header class="header">
    <a href="#" class="logo"><i class="fa fa-shopping-basket"></i>AdminPanel</a>
    <nav class="navbar">
      <a href="#home">Products</a>
      <a href="admin_suppliers.php">Supplier Profiles</a>
      <a href="../Customer/view_feedbacks.php">Customer Feedbacks</a>
      <a href="#">Order</a>
      <a href="#">Payments & Delivery</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <!-- Main Content -->
  <div class="main">
    <!-- Your dashboard content here -->
    <div class="cards">
      <!-- Example Cards -->
      <div class="card card1">
        <div class="icon-box"><i class="fas fa-calendar-alt"></i></div>
        <div class="card-content">
          <div id="CurrentDate"><span id="CurrentDate"></span></div>
          <div>Date</div>
        </div>
      </div>
    </div>

    <div class="card8">
      <h3>WELCOME</h3>
      <img src="assets/img/435895076_817037257111515_8910218523767888663_n-2 copy.jpg" class="img5" />
      <div class="content">
        <h4 id="card8Greeting"></h4>
      </div>
    </div>

    <canvas id="barChart" style="max-height: 400px; margin: 20px auto; width: 90%;"></canvas>
  </div>
</body>
</html>
