<?php
session_start();
if (!isset($_SESSION['operator'])) {
    header("Location: login.php");
    exit();
}
include 'connect/connect.php';

$message = []; // Inisialisasi array untuk pesan

if (isset($_POST['add_product'])) {
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

    // Validasi nama produk di database
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_products->bind_param("s", $name);
    $select_products->execute();
    $select_products->store_result(); // Simpan hasil query
    $num_rows = $select_products->num_rows;

    if ($num_rows > 0) {
        $message[] = 'Product name already exists!';
    } else {
        if ($_FILES['image']['size'] > 2000000) {
            $message[] = 'Image size is too large';
        } else {
            if (move_uploaded_file($image_tmp_name, $image_folder)) {
                // Insert produk baru ke dalam database
                $insert_product = $conn->prepare("INSERT INTO `products` (name, category, price, image) VALUES (?, ?, ?, ?)");
                $insert_product->bind_param("ssss", $name, $category, $price, $image);
                $insert_product->execute();
                $message[] = 'New product added!';
                $insert_product->close(); // Tutup statement insert
            } else {
                $message[] = 'Failed to upload image';
            }
        }
    }

    // Tutup statement select
    $select_products->close();
}

if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .add-products form {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            text-align: center;
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
            box-sizing: border-box; /* Menjamin bahwa padding tidak menambah ukuran keseluruhan */
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
            transition: background-color 0.3s ease; /* Transisi untuk hover */
        }
        .add-products .btn:hover {
            background-color: #218838;
        }
        .add-products select.box {
            appearance: none;
            background: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><polygon points="0,0 10,10 20,0" fill="#333" /></svg>') no-repeat right 10px center;
            background-size: 10px;
        }

        /* Styling Tabel */
        .products-table {
            width: 100%; /* Menjadikan tabel full width */
            max-width: 1200px; /* Atur max-width sesuai kebutuhan */
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .products-table h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        .products-table table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .products-table th, .products-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .products-table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }

        .products-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .products-table tr:hover {
            background-color: #f1f1f1;
        }

        .products-table a {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            margin: 0 5px;
        }

        .products-table .edit-btn {
            background-color: #007bff;
            color: #fff;
        }

        .products-table .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .products-table a:hover {
            opacity: 0.8;
        }

        /* Styling Form Pencarian */
        .products-table form {
            margin-bottom: 20px;
            text-align: center;
        }

        .products-table form input[type="text"] {
            padding: 10px;
            width: 80%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        .products-table form button {
            padding: 10px 20px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
        }

        .products-table form button:hover {
            background-color: #218838;
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
            margin-right: 280px;
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
 
    <!-- Form tambah produk -->
    <section class="add-products">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Add Product</h3>
            <?php
            // Tampilkan pesan jika ada
            if (!empty($message)) {
                foreach ($message as $msg) {
                    echo '<div class="message">';
                    echo '<p>' . $msg . '</p>';
                    echo '<button onclick="this.parentElement.remove();">X</button>';
                    echo '</div>';
                }
            }
            ?>
            <div class="label"><p>Nama :</p></div>
                <input type="text" required placeholder="Enter product name" name="name" maxlength="100" class="box">
            <div class="label"><p>Harga :</p></div>
                <input type="number" min="0" max="9999999999" required placeholder="Enter product price" name="price" class="box">
            <div class="label"><p>Category :</p></div>
                <select name="category" class="box" required>
                    <option value="" disabled selected>Select category --</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                </select>
            <div class="label"><p>Gambar :</p></div>
                <input type="file" name="image" class="box" accept="image/*" required>
                <input type="submit" value="Add Product" name="add_product" class="btn">
        </form>
    </section>

    <section class="products-table">
        <h3>Product List</h3>
        <!-- Form Pencarian -->
        <form action="" method="GET">
            <input type="text" name="search" placeholder="Search by product name">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Proses pencarian
                if (isset($_GET['search'])) {
                    $search = $_GET['search'];
                    // Query untuk mencari produk berdasarkan nama
                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ?");
                    $search_term = "%$search%";
                    $select_products->bind_param("s", $search_term);
                    $select_products->execute();
                    $result = $select_products->get_result();
                } else {
                    // Query untuk mengambil data produk
                    $result = $conn->query("SELECT * FROM `products` ORDER BY id DESC");
                }

                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $count . '</td>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . $row['category'] . '</td>';
                        echo '<td>' . $row['price'] . '</td>';
                        echo '<td>';
                        echo '<a href="edit_product.php?id=' . $row['id'] . '" class="edit-btn">Edit</a>';
                        echo '<a href="menu.php?delete_id=' . $row['id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a>';
                        echo '</td>';
                        echo '</tr>';
                        $count++;
                    }
                } else {
                    echo '<tr><td colspan="5">No products found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </section>
</body>
</html>
