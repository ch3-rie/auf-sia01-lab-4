<?php
// Validate order ID
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$order_details = getOrderDetails($order_id, $connection);

if (!$order_details) {
    echo "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Order not found.</div>";
    echo "<a href='orders.php' class='btn btn-default'><i class='fa fa-arrow-left'></i> Back to Orders</a>";
    exit;
}

// Fetch customer information if user is linked to order
$customer_data = [];
$order = $order_details['order'];

if ($order['user_id']) {
    $user_query = "SELECT user_firstname, user_lastname, user_email FROM users WHERE user_id = " . intval($order['user_id']);
    $user_result = mysqli_query($connection, $user_query);
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user = mysqli_fetch_assoc($user_result);
        $customer_data = [
            'name' => $user['user_firstname'] . ' ' . $user['user_lastname'],
            'email' => $user['user_email'],
            'phone' => 'N/A',
            'address' => 'N/A'
        ];
    }
}

// If customer data is not from user, show as guest
if (empty($customer_data)) {
    $customer_data = [
        'name' => 'Guest Customer',
        'email' => 'N/A',
        'phone' => 'N/A',
        'address' => 'N/A'
    ];
}
?>

<div class="row">
    <div class="col-lg-8">
        <!-- Customer Information -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-user"></i> Customer Information</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <p><strong>Name:</strong></p>
                        <p><?php echo htmlspecialchars($customer_data['name'] ?? 'Not provided'); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Email:</strong></p>
                        <p><?php echo htmlspecialchars($customer_data['email'] ?? 'Not provided'); ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p><strong>Phone:</strong></p>
                        <p><?php echo htmlspecialchars($customer_data['phone'] ?? 'Not provided'); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Address:</strong></p>
                        <p><?php echo htmlspecialchars($customer_data['address'] ?? 'Not provided'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Order Items</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr class="active">
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($order_details['items'] as $item) {
                                // Fetch product name
                                $product_query = "SELECT name FROM products WHERE product_id = " . intval($item['product_id']);
                                $product_result = mysqli_query($connection, $product_query);
                                $product_name = 'Unknown Product';
                                if ($product_result && mysqli_num_rows($product_result) > 0) {
                                    $product = mysqli_fetch_assoc($product_result);
                                    $product_name = $product['name'];
                                }
                                
                                $subtotal = $item['price'] * $item['quantity'];
                            ?>
                                <tr>
                                    <td><?php echo $item['product_id']; ?></td>
                                    <td><?php echo htmlspecialchars($product_name); ?></td>
                                    <td class="text-center">₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="text-center"><?php echo intval($item['quantity']); ?></td>
                                    <td class="text-right"><strong>₱<?php echo number_format($subtotal, 2); ?></strong></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="active">
                                <th colspan="4" class="text-right">Total:</th>
                                <th class="text-right"><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Panel -->
    <div class="col-lg-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle"></i> Order Information</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label><strong>Order ID:</strong></label>
                    <p>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></p>
                </div>

                <div class="form-group">
                    <label><strong>Order Date:</strong></label>
                    <p><?php echo date('F d, Y @ g:i A', strtotime($order['order_date'])); ?></p>
                </div>

                <div class="form-group">
                    <label><strong>Total Amount:</strong></label>
                    <p style="font-size: 18px; color: #27ae60;">₱<?php echo number_format($order['total_amount'], 2); ?></p>
                </div>

                <div class="form-group">
                    <label><strong>Status:</strong></label>
                    <p>
                        <?php
                        $status_badge = 'danger';
                        if ($order['status'] === 'completed') {
                            $status_badge = 'success';
                        } elseif ($order['status'] === 'processing') {
                            $status_badge = 'info';
                        } elseif ($order['status'] === 'pending') {
                            $status_badge = 'warning';
                        }
                        ?>
                        <span class="label label-<?php echo $status_badge; ?>" style="font-size: 12px; padding: 5px 10px;">
                            <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                        </span>
                    </p>
                </div>

                <div class="form-group">
                    <label><strong>Customer Type:</strong></label>
                    <p>
                        <?php
                        if ($order['user_id']) {
                            echo '<span class="label label-info">Registered User</span>';
                        } else {
                            echo '<span class="label label-default">Guest</span>';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div style="margin-top: 20px;">
            <a href="orders.php" class="btn btn-default btn-block">
                <i class="fa fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>
</div>
