<?php
session_start();
if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit();
}

include "connect/connect.php";

// Retrieve orders from the database grouped by table_number
$sql = "SELECT table_number, GROUP_CONCAT(item_name SEPARATOR ', ') AS items, SUM(item_price) AS total_price, status FROM orders GROUP BY table_number, status ORDER BY id DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$conn->close();

// Function to escape special characters for HTML output
function htmlEscape($value) {
    return htmlspecialchars($value, ENT_QUOTES);
}

// Function to format number as currency (Rupiah)
function formatCurrency($value) {
    return 'Rp ' . number_format($value);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pesanan</title>
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
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            justify-content: center; /* Mengatur konten di tengah */
            gap: 20px; /* Jarak antara link */
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

        /* Laporan styling */
        .container {
            padding: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        .table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table tbody tr:hover {
            background-color: #ddd;
        }
        .status-belum-dibayar {
            font-weight: bold;
        }
        .restaurant-name {
            font-size: 1.2em;
            font-weight: bold;
        }

        .report-heading {
            text-align: left;
            font-size: 1.5em;
            color: #007bff; /* Warna biru yang cerah */
            text-transform: uppercase; /* Mengubah teks menjadi huruf kapital */
            font-weight: bold; /* Mengatur teks menjadi tebal */
            letter-spacing: 1px; /* Spasi antar huruf */
        }

        .report-actions {
            margin-top: 20px;
            text-align: right;
        }

        .report-actions a {
            display: inline-block;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            margin-right: 10px;
        }

        .report-actions a:hover {
            background-color: #0056b3;
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
        <div class="navbar-right">
            <div class="profile-info">
                <i class="fas fa-user-circle fa-2x"></i>
                <div class="profile-name"><?php echo htmlEscape($_SESSION['operator']); ?></div>
            </div>
            <a href="login.php" style="color: #fff; margin-left: 20px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="container">
        <h2 class="report-heading">Laporan Pesanan</h2>
        
        <!-- Tombol untuk cetak PDF dan Excel -->
        <div class="report-actions">
            <a href="cetak_pdf.php" target="_blank"><i class="far fa-file-pdf"></i> Cetak PDF</a>
        </div>

        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>Nomor Meja</th>
                    <th>Items</th>
                    <th>Total Harga</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlEscape($order['table_number']); ?></td>
                            <td><?= htmlEscape($order['items']); ?></td>
                            <td><?= formatCurrency($order['total_price']); ?></td>
                            <td class="<?= $order['status'] == 'sudah dibayar' ? 'status-sudah-dibayar' : 'status-belum-dibayar'; ?>">
                                <?= htmlEscape($order['status']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Tidak ada pesanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
