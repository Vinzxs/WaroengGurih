<?php
session_start();
if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit();
}

include "connect/connect.php";

// Fetch the count of pending orders
$sqlPendingOrders = "SELECT COUNT(*) AS pending_count FROM orders WHERE status = 'Belum Dibayar'";
$resultPendingOrders = $conn->query($sqlPendingOrders);
$pendingOrders = $resultPendingOrders->fetch_assoc()['pending_count'];

// Fetch the count of total products
$sqlTotalProducts = "SELECT COUNT(*) AS name FROM products";
$resultTotalProducts = $conn->query($sqlTotalProducts);
$totalProducts = $resultTotalProducts->fetch_assoc()['name'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="aset/logowarung.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* Basic styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; /* Enable wrapping for responsive design */
        }
        .navbar-left {
            display: flex;
            align-items: center;
        }
        .navbar-logo {
            margin-right: 20px;
        }
        .navbar-logo img {
            height: 40px; /* Adjust as needed */
            width: auto;
        }
        .navbar-nav {
            text-align: center;
            flex: 1;
            display: flex;
            justify-content: center; /* Center the content */
            gap: 20px; /* Space between links */
        }
        .navbar-nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
        }
        .navbar-right {
            display: flex;
            align-items: center;
        }
        .profile-info {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #fff;
        }
        .profile-info i {
            margin-right: 10px;
        }
        .profile-name {
            margin-right: 10px;
        }
        /* Content */
        .content {
            flex: 1;
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Enable wrapping for responsive design */
        }
        .stat-item {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin: 10px;
            position: relative;
            min-width: 200px; /* Ensure minimum width */
            transition: transform 0.3s; /* Smooth hover effect */
        }
        .stat-item:hover {
            transform: translateY(-10px); /* Lift the card on hover */
        }
        .stat-item h3 {
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #333;
        }
        .stat-item p {
            font-size: 1.5em;
            color: #007bff;
        }
        .restaurant-name {
            font-size: 1.2em; /* Font size for restaurant name */
            font-weight: bold; /* Bold font for restaurant name */
        }
        /* Quick Access Buttons */
        .quick-access-buttons {
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            gap: 10px; 
        }
        .quick-access-buttons button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .quick-access-buttons button:hover {
            background-color: #0056b3;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar-nav {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
                display: none; /* Hide by default on mobile */
            }
            .navbar-nav a {
                padding: 10px;
                text-align: left;
                width: 100%;
            }
            .stats {
                flex-direction: column;
                align-items: stretch;
            }
            .stat-item {
                width: 100%;
                margin-bottom: 20px;
            }
            .navbar-toggle {
                display: block;
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 10px;
                border-radius: 5px;
                font-size: 1em;
                cursor: pointer;
                transition: background-color 0.3s;
                margin-bottom: 10px;
                margin-top: 10px;
            }
            .navbar-toggle:hover {
                background-color: #0056b3;
            }
        }
        /* Hide toggle button on larger screens */
        @media (min-width: 769px) {
            .navbar-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <span class="restaurant-name">Waroeng Gurih</span>
            </div>
        </div>
        <button class="navbar-toggle" onclick="toggleNavbar()">Menu</button>
        <div class="navbar-nav" id="navbarNav">
            <a href="dashboard.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="orderadmin.php">Orders</a>
            <a href="laporanadmin.php">Laporan</a>
        </div>
        <div class="navbar-right">
            <div class="profile-info">
                <i class="fas fa-user-circle fa-2x"></i>
                <div class="profile-name"><?php echo $_SESSION['operator']; ?></div>
            </div>
            <a href="login.php" style="color: #fff; margin-left: 20px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    <div class="content">
        <h2>Dashboard</h2>
        <p>Selamat datang, <?php echo $_SESSION['operator']; ?>!</p><br>
        <div class="stats">
            <div class="stat-item">
                <h3>Pesanan Belum Diproses</h3>
                <p><?= $pendingOrders; ?></p>
                <div class="quick-access-buttons">
                    <button onclick="window.location.href='orderadmin.php'">Lihat Pesanan</button>
                </div>
            </div>
            <div class="stat-item">
                <h3>Total Produk</h3>
                <p><?= $totalProducts; ?></p>
                <div class="quick-access-buttons">
                    <button onclick="window.location.href='menu.php'">Tambah Product</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleNavbar() {
            var navbarNav = document.getElementById('navbarNav');
            if (navbarNav.style.display === 'flex') {
                navbarNav.style.display = 'none';
            } else {
                navbarNav.style.display = 'flex';
                navbarNav.style.flexDirection = 'column';
            }
        }
    </script>
</body>
</html>
