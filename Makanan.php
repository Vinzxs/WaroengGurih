<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Makanan</title>
    <link rel="icon" href="aset/logowarung.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
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
        <h1>DAFTAR MAKANAN</h1>
    </div>

    <div class="menu">
        <div class="menu-category">
            <div class="menu-items">
                <?php
                // Connect to database
                include 'connect/connect.php';

                // Fetch menu items from database
                $sql = "SELECT * FROM products WHERE category = 'Makanan'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="menu-item">';
                        echo '<div class="menu-item-img">';
                        $gambar_path = 'uploaded_img/' . $row['image'];
                        if (file_exists($gambar_path)) {
                            echo '<img src="' . $gambar_path . '" alt="' . $row['name'] . '" />';
                        } else {
                            echo '<p>Gambar tidak ditemukan: ' . $row['image'] . '</p>';
                        }
                        echo '</div>';
                        echo '<div class="menu-item-info">';
                        echo '<span class="menu-item-name">' . $row['name'] . '</span>';
                        echo '<span class="menu-item-price">Rp ' . number_format($row['price']) . '</span>';
                        echo '<div class="menu-item-actions">';
                        echo '<button class="btn-add-to-cart" onclick="addToCart(\'' . $row['name'] . '\', ' . $row['price'] . ')">Add to Cart</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No menu items available.</p>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script>
        let cart = <?php echo json_encode($_SESSION['cart'] ?? []); ?>;

        function addToCart(name, price) {
            // Add item to cart locally
            cart.push({ name: name, price: price });

            // Update cart count on the navbar
            document.getElementById("cart-count").innerText = cart.length;

            // Save cart to session (using AJAX)
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_cart.php", true);
            xhr.setRequestHeader(
                "Content-Type",
                "application/x-www-form-urlencoded"
            );
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Optionally handle response here (if any)
                }
            };
            xhr.send("cart=" + JSON.stringify(cart));
        }
    </script>
</body>
</html>
