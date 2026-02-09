<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/navigation.php";
include "includes/view_order.php";

// Validate order ID
if (!isset($_GET['order_id'])) {
    header("Location: products.php");
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$order_details = getOrderDetails($order_id, $connection);

if (!$order_details) {
    header("Location: products.php");
    exit;
}

$order = $order_details['order'];
?>

<div class="container">
    <h1 class="page-header">Order Confirmation</h1>

    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-check"></i> Order Successfully Created!</h4>
        A confirmation email has been sent with your order details.
    </div>

    <!-- Display order details -->
    <?php 
    $customer_data = [
        'name' => $_SESSION['customer_name'] ?? 'Not provided',
        'email' => $_SESSION['customer_email'] ?? 'Not provided',
        'phone' => $_SESSION['customer_phone'] ?? '',
        'address' => $_SESSION['customer_address'] ?? ''
    ];
    displayOrderDetails($order_details, $customer_data); 
    ?>

    <div style="margin-top: 30px;">
        <h4>What's Next?</h4>
        <p>Your order has been successfully created with status <strong><?php echo ucfirst(htmlspecialchars($order['status'])); ?></strong>.</p>
        <p>We will process your order shortly and provide you with shipping information via email.</p>
        <p>If you have any questions, please contact our customer support.</p>
        <hr>
        <a href="products.php" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Continue Shopping</a>
        <a href="index.php" class="btn btn-default"><i class="fa fa-home"></i> Back to Home</a>
    </div>
    <?php include "includes/footer.php"; ?>
</div>


