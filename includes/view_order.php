<?php
/**
 * Utility function to fetch complete order details including items
 * 
 * @param int $order_id - Order ID to fetch
 * @param mysqli $connection - Database connection
 * @return array|null - Order data array or null if not found
 */
function getOrderDetails($order_id, $connection) {
    // Fetch order data
    $order_query = "SELECT * FROM orders WHERE order_id = " . intval($order_id);
    $order_result = mysqli_query($connection, $order_query);

    if (!$order_result || mysqli_num_rows($order_result) === 0) {
        return null;
    }

    $order_data = mysqli_fetch_assoc($order_result);

    // Fetch order items
    $items_query = "SELECT * FROM order_items WHERE order_id = " . intval($order_id) . " ORDER BY order_item_id ASC";
    $items_result = mysqli_query($connection, $items_query);

    $order_items = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $order_items[] = $item;
    }

    return [
        'order' => $order_data,
        'items' => $order_items
    ];
}

/**
 * Display order details in HTML format
 * 
 * @param array $order_details - Order details from getOrderDetails()
 * @param array $customer_data - Customer information array (name, email, phone, address)
 */
function displayOrderDetails($order_details, $customer_data = []) {
    if (!$order_details) {
        echo "<div class='alert alert-danger'>Order not found.</div>";
        return;
    }

    $order = $order_details['order'];
    $items = $order_details['items'];
    ?>

    <!-- Customer Information -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">👤 Customer Information</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_data['name'] ?? 'Not provided'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_data['email'] ?? 'Not provided'); ?></p>
                </div>
                <div class="col-md-6">
                    <?php if (!empty($customer_data['phone'])): ?>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer_data['phone']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($customer_data['address'])): ?>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($customer_data['address']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">📋 Order Information</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Order ID:</strong> #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('F d, Y H:i:s', strtotime($order['order_date'])); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <span class="label label-<?php echo $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'completed' ? 'success' : 'info'); ?>"><?php echo ucfirst(htmlspecialchars($order['status'])); ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">🛒 Order Items</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?></td>
                            <td class="text-right">₱<?php echo number_format($item['price'], 2); ?></td>
                            <td class="text-right"><?php echo intval($item['quantity']); ?></td>
                            <td class="text-right"><strong>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="active" style="font-weight: bold; font-size: 16px;">
                            <td colspan="3" style="text-align: right;">Total Amount:</td>
                            <td class="text-right">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No items in this order</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
}
?>
