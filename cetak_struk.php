<?php
session_start();
include "connect/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table_number = $_POST['table_number'];

    // Retrieve order details for the selected table number
    $sql = "SELECT item_name, item_price FROM orders WHERE table_number = ? AND status = 'Sudah Dibayar'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
    $stmt->bind_param("s", $table_number);
    if (!$stmt->execute()) {
        echo "Error executing statement: " . $stmt->error;
        exit;
    }
    $result = $stmt->get_result();

    // Fetch order details into an array
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk</title>
    <link rel="icon" href="aset/logowarung.png">
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
            background-color: #fff;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .table-number {
            font-size: 1.2em;
            font-weight: bold;
        }
        .items {
            margin-bottom: 20px;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        .items th, .items td {
            padding: 8px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8em;
            color: #777;
        }
        .back-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Struk Pembayaran</h2>
            <p class="table-number">Meja <?= htmlspecialchars($table_number); ?></p>
        </div>
        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']); ?></td>
                            <td>Rp <?= number_format($item['item_price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="total">
            <p>Total: Rp <?= number_format(array_sum(array_column($items, 'item_price'))); ?></p>
        </div>
        <div class="footer">
            <p>Terima kasih telah mengunjungi kami!</p>
        </div>
        <a href="kasir.php" class="back-button">Kembali ke Kasir</a>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
