<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart = json_decode($_POST['cart'], true);
    $_SESSION['cart'] = $cart;
}
?>
