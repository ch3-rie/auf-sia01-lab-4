<?php include "includes/header.php"; ?>
<?php include "includes/navigation.php"; ?>

<div id="page-wrapper">
    <div class="container-fluid">

        <?php
        if (isset($_GET['source'])) {
            $source = $_GET['source'];
        } else {
            $source = '';
        }

        switch ($source) {
            case 'add_product':
                include "includes/add_product.php";
                break;

            case 'edit_product':
                include "includes/edit_product.php";
                break;

            default:
                include "includes/view_all_products.php";
                break;
        }
        ?>

    </div>
</div>

<?php include "includes/footer.php"; ?>
