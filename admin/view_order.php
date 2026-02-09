<?php
// Redirect to the modular orders page with the view_order source parameter
if (isset($_GET['order_id'])) {
    header("Location: orders.php?source=view_order&order_id=" . intval($_GET['order_id']));
    exit;
} else {
    header("Location: orders.php");
    exit;
}
?>
