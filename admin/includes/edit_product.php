<h1 class="page-header">
    Update Product Details
</h1>


<?php

if (isset($_POST['update_product'], $_GET['product_id'])) {

    $the_product_id = $_GET['product_id'];

    $product_name        = $_POST['name'];
    $product_description = $_POST['description'];
    $product_price       = $_POST['price'];
    $product_stock       = $_POST['stock_quantity'];
    $product_status      = $_POST['status'];

    // Update Product
    $query  = "UPDATE products SET ";
    $query .= "name='{$product_name}', ";
    $query .= "description='{$product_description}', ";
    $query .= "price='{$product_price}', ";
    $query .= "stock_quantity='{$product_stock}', ";
    $query .= "status='{$product_status}' ";
    $query .= "WHERE product_id={$the_product_id}";

    $update_product_query = mysqli_query($connection, $query);

    if (!$update_product_query) {
        die("Query Failed: " . mysqli_error($connection));
    }

    echo "<p class='alert alert-success'>Product updated successfully.</p>";
}
?>


<?php
if (isset($_GET['product_id'])) {

    $the_product_id = $_GET['product_id'];

    $query = "SELECT * FROM products WHERE product_id={$the_product_id}";
    $fetch_data = mysqli_query($connection, $query);

    while ($Row = mysqli_fetch_assoc($fetch_data)) {

        $product_name        = $Row['name'];
        $product_description = $Row['description'];
        $product_price       = $Row['price'];
        $product_stock       = $Row['stock_quantity'];
        $product_status      = $Row['status'];
        ?>

        <form action="" method="post">

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo $product_name; ?>">
            </div>

            <div class="form-group">
                <label for="description">Product Description</label>
                <textarea class="form-control" name="description" rows="5"><?php echo $product_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $product_price; ?>">
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity</label>
                <input type="number" class="form-control" name="stock_quantity" value="<?php echo $product_stock; ?>">
            </div>

            <div class="form-group">
                <label for="status">Product Status</label>
                <select class="form-control" name="status">
                    <option value="<?php echo $product_status; ?>"><?php echo $product_status; ?></option>
                    <?php if ($product_status === "active") { ?>
                        <option value="inactive">inactive</option>
                    <?php } else { ?>
                        <option value="active">active</option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="update_product" value="Update Product">
            </div>

        </form>

<?php
    }
}
?>
