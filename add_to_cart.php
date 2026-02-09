<?php

session_start();

if (!isset($_GET['product_id'])) {
    http_response_code(400);
    exit('Product ID required');
}

$product_id = (int) $_GET['product_id'];
$quantity = isset($_GET['quantity']) ? max(1, (int) $_GET['quantity']) : 1;

// Validate product exists
include "includes/db.php";
$product_check = mysqli_query($connection, "SELECT product_id FROM products WHERE product_id = $product_id AND status = 'active'");

if (!$product_check || mysqli_num_rows($product_check) === 0) {
    http_response_code(404);
    exit('Product not found');
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

// Return success response
http_response_code(200);
exit('Item added to cart');
