<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

include 'connection.php'; // Oracle DB connection

// Fetch the total number of suppliers
$query_count_suppliers = "SELECT COUNT(*) AS total_suppliers FROM SUPPLIERS";
$result_count_suppliers = oci_parse($conn, $query_count_suppliers);
oci_execute($result_count_suppliers);
$row_count_suppliers = oci_fetch_assoc($result_count_suppliers);
$total_suppliers = $row_count_suppliers['TOTAL_SUPPLIERS'] ?? 0;

// Fetch the total number of orders
$query_count_orders = "SELECT COUNT(*) AS total_orders FROM ORDERS";
$result_count_orders = oci_parse($conn, $query_count_orders);
oci_execute($result_count_orders);
$row_count_orders = oci_fetch_assoc($result_count_orders);
$total_orders = $row_count_orders['TOTAL_ORDERS'] ?? 0;

// Fetch the total income for this month using order_items and orders
$query_income = "
    SELECT SUM(oi.subtotal) AS total_income
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    WHERE EXTRACT(MONTH FROM o.order_date) = EXTRACT(MONTH FROM SYSDATE)
    AND EXTRACT(YEAR FROM o.order_date) = EXTRACT(YEAR FROM SYSDATE)
";
$result_income = oci_parse($conn, $query_income);
oci_execute($result_income);
$row_income = oci_fetch_assoc($result_income);
$total_income = $row_income['TOTAL_INCOME'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
      * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", sans-serif;
   }

    body {
      background:rgb(255, 255, 255);
      color: #333;
      line-height: 1.6;
    }

    /* Header */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background:rgb(142, 206, 187);
      padding: 15px 30px;
      color: rgb(0, 0, 0);
      position: sticky;
      top: 0;
      z-index: 1000;
      flex-wrap: wrap;
    }

    .header .logo {
      font-size: 1.5rem;
      font-weight: bold;
      text-decoration: none;
      color: rgb(13, 30, 68);
      
    }
    .header .logo i{
      color: #ff8800;
    }

    .header .navbar {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .header .navbar a {
      text-decoration: none;
      color: rgb(0, 0, 0);
      font-weight: 500;
      transition: color 0.3s;
    }

    .header .navbar a:hover {
      color: #ff8800;
    }

    .header .icons div{
    height: 3rem;
    width: 3rem;
    line-height: 3rem;
    border-radius: .5rem;
    background: #eee;
    color: rgb(17, 17, 65);
    font-size: 1.5rem;
    cursor: pointer;
    margin-right: .3rem;
    text-align: center;
  }

  .header .icons div:hover{
    background: rgb(230, 69, 10);
    color: #fff;
  }


    
    .main {
      max-width: 1200px;
      margin: 30px auto;
      padding: 0 15px;
    }
    h1 {
      text-align: center;
      font-size: 36px;
      margin-bottom: 40px;
      color: #333;
    }
    .cards {
      display: flex;
      justify-content: center;
      gap: 20px;
      overflow-x: auto;
      padding: -50px 0;
      flex-wrap: nowrap;
    }
    .card {
      background-color: rgb(108, 158, 207);
      padding: 20px;
      border-radius: 12px;
      width: 200px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      flex-shrink: 0;
      transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    }
    .card:hover {
      background-color: #e2e6ea;
      transform: scale(1.05);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .icon-box {
      font-size: 30px;
      margin-bottom: 10px;
      color: rgb(0, 0, 0);
    }
    .card-content div:first-child {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .card8 {
      text-align: center;
      margin-top: 40px;
      align-items:center;
    }
    .card8 img {
      width: 400px;
      height: 500px;
      margin-top: 20px;
      display:flex;
      margin-left: 230px;
    
    }
    #welcome-text {
      font-size: 30px;
      font-weight: bold;
      margin-top: -300px;
      margin-left: 500px;
    }
    @media screen and (max-width: 768px) {
      .cards {
        flex-wrap: wrap;
        justify-content: center;
      }
    }
  </style>
</head>

<body>
<header class="header">
  <a href="#" class="logo"><i class="fa fa-shopping-basket"></i>UrbanFood</a>

  <nav class="navbar">
      <a href="admin_products.php">Products</a>
      <a href="admin_suppliers.php">Supplier Profiles</a>
      <a href="admin_view_feedbacks.php">Customer Feedbacks</a>
      <a href="admin_orders.php">Order Details</a>
  </nav>
  <div class="icons">
  <a href="logout.php"><div class="fa-solid fa-right-from-bracket"></div></a>
  </div>
</header>


  <!-- Main Content -->
  <div class="main">
    <h1>Admin Panel</h1>

    <div class="cards">
      <div class="card">
        <div class="icon-box"><i class="fas fa-calendar-alt"></i></div>
        <div class="card-content">
          <div id="CurrentDate">--</div>
          <div>Date</div>
        </div>
      </div>

      <div class="card">
        <div class="icon-box"><i class="fas fa-clock"></i></div>
        <div class="card-content">
          <div id="CurrentTime">--</div>
          <div>Time</div>
        </div>
      </div>

      <div class="card">
        <div class="icon-box"><i class="fas fa-user"></i></div>
        <div class="card-content">
          <div><?= $total_suppliers ?></div>
          <div>Suppliers</div>
        </div>
      </div>

      <div class="card">
        <div class="icon-box"><i class="fas fa-box"></i></div>
        <div class="card-content">
          <div><?= $total_orders ?></div>
          <div>Orders</div>
        </div>
      </div>

      <div class="card">
        <div class="icon-box"><i class="fas fa-money-bill-wave"></i></div>
        <div class="card-content">
          <div><?= "LKR " . number_format($total_income, 2) ?></div>
          <div>Total Income in This Month</div>
        </div>
      </div>
    </div>

    <div class="card8">
      <img src='../Images/Dashboard.png' class="img5" />
      <div class="content">
        <h4 id="welcome-text">Have a great day, Admin!</h4>
      </div>
    </div>
  </div>

  <script>
    function updateDate() {
      const today = new Date();
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      document.getElementById('CurrentDate').textContent = today.toLocaleDateString(undefined, options);
    }

    function updateTime() {
      const now = new Date();
      const timeStr = now.toLocaleTimeString(undefined, {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('CurrentTime').textContent = timeStr;
    }

    updateDate();
    setInterval(updateTime, 1000);
    updateTime();
  </script>
</body>
</html>
