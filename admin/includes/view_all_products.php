<h1 class="page-header">
    View All Products
</h1>


<?php
// Delete Product
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    $query = "DELETE FROM products WHERE product_id = $product_id";
    $delete_query = mysqli_query($connection, $query);

    if (!$delete_query) {
        die("Query Failed: " . mysqli_error($connection));
    }

    header("Location: products.php");
}
?>

<form action="" method="post">
    <table class="table table-bordered table-hover">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $query = "SELECT * FROM products";
            $fetch_products = mysqli_query($connection, $query);

            while ($row = mysqli_fetch_assoc($fetch_products)) {

                $product_id = $row['product_id'];

                echo "<tr>";
                echo "<td>{$row['product_id']}</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "<td>{$row['price']}</td>";
                echo "<td>{$row['stock_quantity']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['created_at']}</td>";
                echo "
                    <td>
                        <a href='products.php?source=edit_product&product_id=$product_id'>Edit</a>
                        |
                        <a onClick=\"javascript: return confirm('Are you sure you want to delete?');\"
                           href='products.php?delete=$product_id'>Delete</a>
                    </td>
                ";
                echo "</tr>";
            }
            ?>
        </tbody>

    </table>
</form>
