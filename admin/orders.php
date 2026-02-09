<?php
include "includes/header.php";
include "includes/navigation.php";
include "../includes/view_order.php";
?>

<div id="page-wrapper">
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Orders Management
                    <small>View all orders</small>
                </h1>
            </div>
            <div class="col-xs-12">
                <?php
                if (isset($_GET['source'])) {
                    $source = $_GET['source'];
                } else {
                    $source = "";
                }
                switch ($source) {
                    case 'view_order':
                        include "./includes/view_order_detail.php";
                        break;
                    default:
                        include "./includes/view_all_orders.php";
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php" ?>
