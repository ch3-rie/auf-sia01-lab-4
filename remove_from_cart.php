<?php
session_start();

$product_id = (int) $_GET['product_id'];

unset($_SESSION['cart'][$product_id]);

header("Location: cart.php");
