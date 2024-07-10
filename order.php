<?php
session_start();
include "connect/connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['remove'])) {
        $remove_index = $_POST['remove'];
        if (isset($_SESSION['cart'][$remove_index])) {
            unset($_SESSION['cart'][$remove_index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
        }
    } elseif (isset($_POST['place_order'])) {
        // Insert order details into database
        $table_number = $_POST['table_number'];
        $cart = $_SESSION['cart'];
        
        foreach ($cart as $item) {
            $stmt = $conn->prepare("INSERT INTO orders (table_number, item_name, item_price) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $table_number, $item['name'], $item['price']);
            $stmt->execute();
            $stmt->close();
        }

        // Clear cart after order is placed
        unset($_SESSION['cart']);
        $_SESSION['table_number'] = $table_number;
        $conn->close();

        // Redirect to order.php with a success message
        echo "<script>alert('Terimakasih Telah Memesan, Silahkan Melakukan Pembayaran ke kasir.');</script>";
        echo "<script>window.location.replace('order.php');</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="icon" href="aset/logowarung.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .order-details {
            max-width: 1000px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .centered-title h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .table thead th {
            text-align: center;
        }
        .table tbody td {
            text-align: center;
        }
        .table .total-row {
            font-weight: bold;
        }

        .form-label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
            color: #333;
        }

        .form-control {
            width: 25%;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <span class="restaurant-name">Waroeng Gurih</span>
        </div>
        <div class="navbar-right">
            <div class="toolbar">
                <a href="Makanan.php">Daftar Makanan</a>
                <a href="Minuman.php">Daftar Minuman</a>
                <a href="order.php"><i class="fas fa-shopping-cart"></i> <span id="cart-count"><?php echo count($_SESSION['cart'] ?? []); ?></span></a>
            </div>
        </div>
    </nav>

    <div class="centered-title">
        <h1>ORDER DETAILS</h1>
    </div>

    <div class="order-details">
        <form method="post" action="">
            <div class="mb-3">
                <label for="table_number" class="form-label">Nomor Meja</label>
                <input type="text" class="form-control" id="table_number" name="table_number">
            </div>
            <?php
            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>Nama</th><th>Harga</th><th>Aksi</th></tr></thead>';
                echo '<tbody>';

                $total = 0;
                foreach ($_SESSION['cart'] as $index => $item) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($item['name']) . '</td>';
                    echo '<td>Rp ' . number_format($item['price']) . '</td>';
                    echo '<td>';
                    echo '<button type="submit" name="remove" value="' . $index . '" class="btn btn-danger">Hapus</button>';
                    echo '</td>';
                    echo '</tr>';
                    $total += $item['price'];
                }

                echo '<tr class="total-row">';
                echo '<td>Total</td>';
                echo '<td>Rp ' . number_format($total) . '</td>';
                echo '<td></td>';
                echo '</tr>';

                echo '</tbody>';
                echo '</table>';
                echo '<button type="submit" name="place_order" class="btn btn-primary">Pesan</button>';
            } else {
                echo '<p>No items in cart.</p>';
            }
            ?>
        </form>
    </div>
</body>
</html>