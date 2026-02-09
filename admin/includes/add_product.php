<h1 class="page-header">
    Add New Product
</h1>


<?php
if (isset($_POST['create_product'])) {

    $product_name        = $_POST['name'];
    $product_description = $_POST['description'];
    $product_price       = $_POST['price'];
    $product_stock       = $_POST['stock_quantity'];
    $product_status      = $_POST['status'];
    $product_created_at  = date('Y-m-d H:i:s');

    $query = "INSERT INTO products(
                name,
                description,
                price,
                stock_quantity,
                status,
                created_at
              ) VALUES (
                '{$product_name}',
                '{$product_description}',
                '{$product_price}',
                '{$product_stock}',
                '{$product_status}',
                '{$product_created_at}'
              )";

    $create_product_query = mysqli_query($connection, $query);

    if (!$create_product_query) {
        die("Query Failed: " . mysqli_error($connection));
    }

    echo "<p class='alert alert-success'>Product added successfully.</p>";
}
?>

<form action="" method="post">
    <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" class="form-control" name="name">
    </div>

    <div class="form-group">
        <label for="description">Product Description</label>
        <textarea class="form-control" name="description" rows="5"></textarea>
    </div>

    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" step="0.01" class="form-control" name="price">
    </div>

    <div class="form-group">
        <label for="stock_quantity">Stock Quantity</label>
        <input type="number" class="form-control" name="stock_quantity">
    </div>

    <div class="form-group">
        <label for="status">Product Status</label>
        <select class="form-control" name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="create_product" value="Add Product">
    </div>
</form>
