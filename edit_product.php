<?php
session_start();
if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit();
}
include 'connect/connect.php';

$message = []; // Inisialisasi array untuk pesan

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Ambil data produk dari database
    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $select_product->bind_param("i", $product_id);
    $select_product->execute();
    $product = $select_product->get_result()->fetch_assoc();

    if (!$product) {
        $message[] = 'Product not found!';
    }
} else {
    header("Location: menu.php");
    exit();
}

if (isset($_POST['update_product'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';

    // Sanitasi input
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $price = filter_var($price, FILTER_SANITIZE_STRING);
    $category = filter_var($category, FILTER_SANITIZE_STRING);

    $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
    $image_tmp_name = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';
    $image_folder = 'uploaded_img/' . $image;

    // Update data produk di database
    if ($image) {
        if ($_FILES['image']['size'] > 2000000) {
            $message[] = 'Image size is too large';
        } else {
            if (move_uploaded_file($image_tmp_name, $image_folder)) {
                $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, price = ?, image = ? WHERE id = ?");
                $update_product->bind_param("ssssi", $name, $category, $price, $image, $product_id);
            } else {
                $message[] = 'Failed to upload image';
            }
        }
    } else {
        $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, price = ? WHERE id = ?");
        $update_product->bind_param("sssi", $name, $category, $price, $product_id);
    }

    if (isset($update_product)) {
        $update_product->execute();
        $message[] = 'Product updated successfully!';
    }

    // Tutup statements jika ada
    if (isset($update_product)) {
        $update_product->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
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
        /* Content */
        .content {
            flex: 1;
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #007bff;
        }

        .add-products {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            margin-top: 20px;
        }
        .add-products h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }
        .add-products .box {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .add-products .btn {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .add-products .btn:hover {
            background-color: #218838;
        }
        .add-products select.box {
            appearance: none;
            background: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><polygon points="0,0 10,10 20,0" fill="#333" /></svg>') no-repeat right 10px center;
            background-size: 10px;
        }

        .back-btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        
        .message {
            background-color: #90d9ac;
            color: #000;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 6px solid #00ad42;
            position: relative;
        }

        .message button {
            background: none;
            border: none;
            color: #d8000c;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .message button:hover {
            text-decoration: underline;
        }

        .label p {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
            color: #333;
        }

        .restaurant-name {
  font-size: 1.2em; /* Ukuran teks nama restoran */
  font-weight: bold; /* Ketebalan teks nama restoran */
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
        <div class="navbar-nav">
            <a href="dashboard.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="#">Orders</a>
            <a href="#">Laporan</a>
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

    <!-- Form update produk -->
    <section class="add-products">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Update Product</h3>
            <?php
            // Tampilkan pesan jika ada
            if (!empty($message)) {
                foreach ($message as $msg) {
                    echo '<p class="message">' . $msg . '</p>';
                }
            }
            ?>
            <div class="label"><p>Nama :</p></div>
            <input type="text" required placeholder="Enter product name" name="name" maxlength="100" class="box" value="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="label"><p>Harga :</p></div>
            <input type="number" min="0" max="9999999999" required placeholder="Enter product price" name="price" class="box" value="<?php echo htmlspecialchars($product['price']); ?>">
            <div class="label"><p>Category :</p></div>
            <select name="category" class="box" required>
                <option value="" disabled>Select category --</option>
                <option value="Makanan" <?php echo $product['category'] == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                <option value="Minuman" <?php echo $product['category'] == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
            </select>
            <div class="label"><p>Gambar :</p></div>
            <input type="file" name="image" accept="image/*" class="box">
            <input type="submit" value="Update Product" name="update_product" class="btn">
        </form>
        <a href="menu.php" class="back-btn">Kembali</a>
    </section>
</body>
</html>
