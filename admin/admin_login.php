<?php
session_start();

$default_username = "admin";
$default_password = "admin123";

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $default_username && $password === $default_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Admin Login</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: url('https://www.transparenttextures.com/patterns/green-cup.png'); /* Set the texture background */
            background-size: cover;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        /* Navbar */
        nav {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px 40px; /* Increased padding for more width */
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            top: 0;
            left: 0;
        }

        .navbar-logo {
            font-size: 30px; /* Increased font size */
            color: #fff;
            display: flex;
            align-items: center;
        }

        .navbar-logo i {
            font-size: 40px; /* Increased icon size */
            margin-right: 10px;
            color: #fff;
        }

        /* Login Box */
        .login-box {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            text-align: center;
            margin-top: 80px; /* Push down after navbar */
        }

        .login-box h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #fff;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
            color: #fff;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .login-box .fa-shopping-basket {
            font-size: 50px;
            color: #28a745;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .login-box {
                width: 80%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="navbar-logo">
        <i class="fa fa-shopping-basket"></i>
        <span>UrbanFood</span>
    </div>
</nav>

<!-- Login Box -->
<div class="login-box">
    <h2>Admin Login</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="" autocomplete="off">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required autocomplete="off" />

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required autocomplete="new-password" />

        <input type="submit" value="Login" />
    </form>
</div>

</body>
</html>
