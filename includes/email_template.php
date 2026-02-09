<?php

?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .order-number {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #007bff;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table tr {
            border-bottom: 1px solid #eee;
        }
        .info-table td {
            padding: 8px 0;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 35%;
            color: #555;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table thead {
            background-color: #f8f9fa;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 16px;
            background-color: #f8f9fa;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .thank-you {
            text-align: center;
            color: #007bff;
            font-size: 18px;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Order Confirmation</h1>
        </div>

        <div class="content">
            <div class="order-number">
                Order #<?php echo str_pad((string)(int)$order_id, 6, '0', STR_PAD_LEFT); ?>
            </div>

            <div class="thank-you">
                Thank you for your order, <?php echo htmlspecialchars($customer_name); ?>!
            </div>

            <!-- Customer Information Section -->
            <div class="section">
                <div class="section-title">👤 Customer Information</div>
                <table class="info-table">
                    <tr>
                        <td>Name:</td>
                        <td><?php echo htmlspecialchars($customer_name); ?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><?php echo htmlspecialchars($customer_email); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Order Information Section -->
            <div class="section">
                <div class="section-title">📋 Order Information</div>
                <table class="info-table">
                    <tr>
                        <td>Order Date:</td>
                        <td><?php echo isset($order_data['order_date']) ? date('F d, Y H:i:s', strtotime($order_data['order_date'])) : date('F d, Y H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td><strong><?php echo ucfirst(htmlspecialchars($order_data['status'] ?? 'Pending')); ?></strong></td>
                    </tr>
                </table>
            </div>

            <!-- Order Items Section -->
            <div class="section">
                <div class="section-title">🛒 Order Items</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($order_items)): ?>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?></td>
                                <td class="text-right">₱<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-right"><?php echo intval($item['quantity']); ?></td>
                                <td class="text-right"><strong>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">No items in order</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="total-row">
                            <td colspan="3" class="text-right">Total Amount:</td>
                            <td class="text-right">₱<?php echo number_format(isset($total_amount) ? $total_amount : 0, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 30px; padding: 15px; background-color: #e7f3ff; border-left: 4px solid #007bff;">
                <p><strong>What's Next?</strong></p>
                <p>Your order has been successfully created with status <strong>Pending</strong>. We will process your order shortly and provide you with shipping information.</p>
                <p>If you have any questions, please reply to this email or contact our customer support.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> CMS Shop. All rights reserved.</p>
            <p>This is an automated email. Please do not reply directly to this address.</p>
        </div>
    </div>
</body>
</html>
