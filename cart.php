<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/navigation.php";
?>

<div class="container">
<h1 class="page-header">Shopping Cart</h1>

<?php
if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
} else {
?>

<table class="table table-bordered">
<thead>
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Total</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php
$grand_total = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {

    $query = "SELECT name, price FROM products WHERE product_id = $product_id";
    $result = mysqli_query($connection, $query);
    $product = mysqli_fetch_assoc($result);

    $total = $product['price'] * $quantity;
    $grand_total += $total;
?>

<tr>
    <td><?php echo $product['name']; ?></td>
    <td>₱<?php echo number_format($product['price'], 2); ?></td>
    <td><?php echo $quantity; ?></td>
    <td>₱<?php echo number_format($total, 2); ?></td>
    <td>
        <a href="remove_from_cart.php?product_id=<?php echo $product_id; ?>"
           class="btn btn-danger btn-sm">
           Remove
        </a>
    </td>
</tr>

<?php } ?>

</tbody>
</table>

<h3>Total: ₱<?php echo number_format($grand_total, 2); ?></h3>

<a href="checkout.php" class="btn btn-primary">Checkout</a>

<?php } ?>

    <hr>
    <?php include "includes/footer.php"; ?>


