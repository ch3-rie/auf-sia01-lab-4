<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>
<?php include "includes/navigation.php"; ?>

<div class="container">

    <h1 class="page-header">Products</h1>

    <div class="row">
        <?php
        $query = "SELECT product_id, name, description, price 
                  FROM products 
                  WHERE status = 'active'";

        $select_products = mysqli_query($connection, $query);

        while ($row = mysqli_fetch_assoc($select_products)) {

            $product_id   = $row['product_id'];
            $name         = $row['name'];
            $description  = substr($row['description'], 0, 120);
            $price        = $row['price'];
        ?>
            <div class="col-md-4">
                <div class="thumbnail">
                    <div class="caption">
                        <h4><?php echo $name; ?></h4>

                        <p><?php echo $description; ?>...</p>

                        <p>
                            <strong>₱<?php echo number_format($price, 2); ?></strong>
                        </p>

                        <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 5px;">
                            <button class="qty-minus-btn btn btn-default btn-sm" data-product-id="<?php echo $product_id; ?>" type="button">
                                -<i class="fa fa-minus"></i>
                            </button>
                            <input type="text" id="qty-<?php echo $product_id; ?>" class="product-quantity" value="1" readonly style="width: 50px; padding: 5px; text-align: center; border: 1px solid #ccc;">
                            <button class="qty-plus-btn btn btn-default btn-sm" data-product-id="<?php echo $product_id; ?>" type="button">
                                +<i class="fa fa-plus"></i>
                            </button>
                        </div>

                        <button class="btn btn-success add-to-cart-btn" data-product-id="<?php echo $product_id; ?>" title="Add to Cart">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                        <span class="cart-feedback" style="margin-left: 10px; display: none; color: green;">✓ Added</span>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <hr>
<?php include "includes/footer.php"; ?>
</div>

<script>

document.querySelectorAll('.qty-minus-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        const quantityInput = document.getElementById('qty-' + productId);
        let qty = parseInt(quantityInput.value) || 1;
        
        if (qty > 1) {
            qty--;
            quantityInput.value = qty;
        }
    });
});


document.querySelectorAll('.qty-plus-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        const quantityInput = document.getElementById('qty-' + productId);
        let qty = parseInt(quantityInput.value) || 1;
        
        if (qty < 100) {
            qty++;
            quantityInput.value = qty;
        }
    });
});


document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const productId = this.getAttribute('data-product-id');
        const quantityInput = document.getElementById('qty-' + productId);
        const quantity = parseInt(quantityInput.value) || 1;
        const feedback = this.nextElementSibling;
        

        if (quantity < 1) {
            quantityInput.value = 1;
            return;
        }
        

        fetch('add_to_cart.php?product_id=' + productId + '&quantity=' + quantity)
            .then(response => {
                if (response.ok) {

                    quantityInput.value = 1;
                    
 
                    feedback.style.display = 'inline';
                    

                    updateCartCount();
                    

                    setTimeout(() => {
                        feedback.style.display = 'none';
                    }, 2000);
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>


