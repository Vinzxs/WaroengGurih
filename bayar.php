<?php
session_start();
include "connect/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table_number = $_POST['table_number'];

    // Update order status to 'Sudah Dibayar'
    $sqlUpdateStatus = "UPDATE orders SET status = 'Sudah Dibayar' WHERE table_number = ?";
    $stmt = $conn->prepare($sqlUpdateStatus);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
    $stmt->bind_param("s", $table_number);
    if (!$stmt->execute()) {
        echo "Error updating status: " . $stmt->error;
        exit;
    }
    $stmt->close();

    // Redirect back to cashier page or display confirmation
    header("Location: kasir.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar</title>
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
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            margin-bottom: 20px;
        }
        .card-body {
            padding-bottom: 10px;
        }
        .table-info {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 8px 0;
        }
        .table-info span {
            font-size: 16px;
        }
        .total-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            margin-top: 20px;
        }
        .total-label {
            font-weight: bold;
        }
        .total-value {
            font-weight: bold;
        }
        .button-container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .print-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .print-button:hover {
            background-color: #218838;
        }
        .back-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #c82333;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">Rincian Pesanan Meja <?= htmlspecialchars($table_number); ?></div>
        <div class="card-body">
            <?php foreach ($orders as $order): ?>
                <div class="table-info">
                    <span><?= htmlspecialchars($order['item_name']); ?></span>
                    <span>Rp <?= number_format($order['item_price']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="total-card">
            <div class="total-label">Total</div>
            <div class="total-value">Rp <?= number_format($total); ?></div>
        </div>
        <div class="button-container">
            <button class="print-button" onclick="window.print()">Cetak Struk</button>
            <a href="kasir.php" class="back-button">Kembali</a>
        </div>
    </div>
</div>
</body>
</html>
