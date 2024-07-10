<?php
session_start();
include "connect/connect.php";

// Retrieve all distinct table numbers from the database
$sqlTables = "SELECT DISTINCT table_number FROM orders";
$resultTables = $conn->query($sqlTables);

if (!$resultTables) {
    die("Error retrieving table numbers: " . $conn->error);
}

$tables = [];
while ($row = $resultTables->fetch_assoc()) {
    $tables[] = $row['table_number'];
}

$orders = [];
foreach ($tables as $table_number) {
    $sql = "SELECT item_name, item_price, status FROM orders WHERE table_number = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $table_number);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $orders[$table_number] = [];
    while ($row = $result->fetch_assoc()) {
        $orders[$table_number][] = $row;
    }
    $stmt->close();
}

$conn->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "connect/connect.php"; // Reconnect to database for POST request

    $table_number = $_POST['table_number'];

    // Hapus semua pesanan untuk nomor meja tertentu
    $sql = "DELETE FROM orders WHERE table_number = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $table_number);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Redirect kembali ke halaman kasir
    header("Location: kasir.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
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

        /* Card styling */
        .container {
            padding: 20px;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: calc(33.333% - 20px);
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: relative; /* Mengatur posisi relatif untuk tombol bayar */
        }

        .card-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative; /* Mengatur posisi relatif untuk tombol bayar */
        }

        .card-body table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .card-body th, .card-body td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .total-card {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: auto; /* Mengatur agar total selalu di bagian bawah */
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            font-weight: bold;
        }

        .no-items {
            text-align: center;
            font-size: 1.2em;
            color: #333;
            padding: 20px;
        }

        .button-container {
    margin-top: 10px;
    display: flex;
    justify-content: space-between; /* Mengatur tombol agar berjajar */
}

.button-container form {
    flex: 1; /* Mengatur form agar memiliki lebar yang sama */
    margin: 0 5px; /* Memberikan jarak antara tombol */
}

.button-container form:first-child {
    margin-left: 0; /* Menghapus margin kiri pada form pertama */
}

.button-container form:last-child {
    margin-right: 0; /* Menghapus margin kanan pada form terakhir */
}

.pay-button, .struk-button, .pesanan-selesai-button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 12px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%; /* Mengatur tombol agar memenuhi lebar form */
}

.pay-button:hover, .struk-button:hover, .pesanan-selesai-button:hover {
    background-color: #218838;
}


        .restaurant-name {
            font-size: 1.2em; /* Ukuran teks nama restoran */
            font-weight: bold; /* Ketebalan teks nama restoran */
        }

        @media (max-width: 768px) {
            .card {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .card {
                width: 100%;
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

<div class="container">
    <div class="grid">
    <?php foreach ($orders as $table_number => $cart): ?>
    <div class="card">
        <div class="card-header">Pesanan Meja <?= htmlspecialchars($table_number); ?></div>
        <div class="card-body">
            <?php if (!empty($cart)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Item Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($cart as $item): 
                            $total += $item['item_price'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_name']); ?></td>
                                <td>Rp <?= number_format($item['item_price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-card">
                    <div class="total-label">Total</div>
                    <div class="total-value">Rp <?= number_format($total); ?></div>
                </div>
                <div class="button-container">
                    <?php if ($cart[0]['status'] == 'Belum Dibayar'): ?>
                        <form action="bayar.php" method="post">
                            <input type="hidden" name="table_number" value="<?= htmlspecialchars($table_number); ?>">
                            <button type="submit" class="pay-button">Bayar</button>
                        </form>
                    <?php else: ?>
                        <form action="cetak_struk.php" method="post">
                            <input type="hidden" name="table_number" value="<?= htmlspecialchars($table_number); ?>">
                            <button type="submit" class="struk-button">Cetak Struk</button>
                        </form>
                        <form action="kasir.php" method="post">
                            <input type="hidden" name="table_number" value="<?= htmlspecialchars($table_number); ?>">
                            <button type="submit" class="pesanan-selesai-button">Pesanan Selesai</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="no-items">No items in cart for table <?= htmlspecialchars($table_number); ?>.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</div>

</body>
</html>

