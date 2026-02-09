<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/navigation.php";
include "includes/mailtrap_config.php";
include "includes/view_order.php";

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit;
}

// Initialize form data
$form_data = [
    'customer_name' => '',
    'customer_email' => '',
    'customer_phone' => '',
    'customer_address' => ''
];

$errors = [];
$order_created = false;
$order_id = null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Validate inputs
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');

    // Validation
    if (empty($customer_name)) {
        $errors[] = "Customer name is required.";
    }
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }

    // Store form data for re-display if there are errors
    $form_data['customer_name'] = htmlspecialchars($customer_name);
    $form_data['customer_email'] = htmlspecialchars($customer_email);
    $form_data['customer_phone'] = htmlspecialchars($customer_phone);
    $form_data['customer_address'] = htmlspecialchars($customer_address);

    if (empty($errors)) {
        // Calculate total amount
        $total_amount = 0;
        $cart_items = [];

        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $query = "SELECT product_id, name, price FROM products WHERE product_id = " . intval($product_id);
            $result = mysqli_query($connection, $query);
            $product = mysqli_fetch_assoc($result);

            if ($product) {
                $subtotal = $product['price'] * $quantity;
                $total_amount += $subtotal;
                $cart_items[] = [
                    'product_id' => $product['product_id'],
                    'product_name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            }
        }

        // Create order
        $user_id = $_SESSION['user_id'] ?? null;
        $status = "pending";

        // Validate user_id if present
        if ($user_id) {
            $user_check = mysqli_query($connection, "SELECT user_id FROM users WHERE user_id = " . intval($user_id));
            if (!$user_check || mysqli_num_rows($user_check) === 0) {
                $user_id = null; // Invalid user_id, treat as guest
            }
        }

        // Build dynamic INSERT query based on whether valid user_id exists
        if ($user_id) {
            $insert_order = mysqli_query($connection, "
                INSERT INTO orders (user_id, total_amount, order_date, status)
                VALUES (
                    " . intval($user_id) . ",
                    " . floatval($total_amount) . ",
                    NOW(),
                    '" . $status . "'
                )
            ");
        } else {
            $insert_order = mysqli_query($connection, "
                INSERT INTO orders (total_amount, order_date, status)
                VALUES (
                    " . floatval($total_amount) . ",
                    NOW(),
                    '" . $status . "'
                )
            ");
        }

        if ($insert_order) {
            $order_id = mysqli_insert_id($connection);

            // Insert order items
            foreach ($cart_items as $item) {
                $insert_item = mysqli_query($connection, "
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (
                        " . intval($order_id) . ",
                        " . intval($item['product_id']) . ",
                        " . intval($item['quantity']) . ",
                        " . floatval($item['price']) . "
                    )
                ");

                if (!$insert_item) {
                    error_log("Error inserting order item: " . mysqli_error($connection));
                }
            }

            // Prepare order data for email
            $order_data = [
                'order_date' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];

            // Send email confirmation
            $email_sent = sendOrderConfirmationEmail(
                $customer_email,
                $customer_name,
                $order_id,
                $order_data,
                $cart_items
            );

            // Store customer data in session for order success page
            $_SESSION['customer_name'] = $customer_name;
            $_SESSION['customer_email'] = $customer_email;
            $_SESSION['customer_phone'] = $customer_phone;
            $_SESSION['customer_address'] = $customer_address;

            // Clear cart
            unset($_SESSION['cart']);

            // Mark as created and redirect
            $order_created = true;
        } else {
            $errors[] = "Failed to create order. Please try again.";
            error_log("Error creating order: " . mysqli_error($connection));
        }
    }
}

// Calculate current cart total for display
$cart_total = 0;
$display_items = [];

if (!$order_created) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $query = "SELECT product_id, name, price FROM products WHERE product_id = " . intval($product_id);
        $result = mysqli_query($connection, $query);
        $product = mysqli_fetch_assoc($result);

        if ($product) {
            $subtotal = $product['price'] * $quantity;
            $cart_total += $subtotal;
            $display_items[] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}
?>

<div class="container">
    <h1 class="page-header">Checkout</h1>

    <?php if ($order_created): ?>
        <!-- Order Success -->
        <div class="alert alert-success">
            <h4>✓ Order Successfully Created!</h4>
            <p>Your order #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?> has been placed successfully.</p>
            <p>A confirmation email has been sent to <strong><?php echo htmlspecialchars($form_data['customer_email']); ?></strong></p>
        </div>

        <p><a href="order_success.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">View Order Details</a></p>

    <?php else: ?>
        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column: Customer Information Form -->
            <div class="col-md-7">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">👤 Customer Information</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="checkout.php">
                            <div class="form-group">
                                <label for="customer_name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" id="customer_name" name="customer_name" class="form-control" 
                                       value="<?php echo $form_data['customer_name']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="customer_email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="customer_email" name="customer_email" class="form-control" 
                                       value="<?php echo $form_data['customer_email']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="customer_phone">Phone Number</label>
                                <input type="tel" id="customer_phone" name="customer_phone" class="form-control" 
                                       value="<?php echo $form_data['customer_phone']; ?>">
                            </div>

                            <div class="form-group">
                                <label for="customer_address">Delivery Address</label>
                                <textarea id="customer_address" name="customer_address" class="form-control" rows="4"><?php echo $form_data['customer_address']; ?></textarea>
                            </div>

                            <button type="submit" name="place_order" class="btn btn-success btn-lg btn-block">
                                <i class="fa fa-check"></i> Place Order
                            </button>
                            <a href="cart.php" class="btn btn-default btn-block" style="margin-top: 10px;">
                                <i class="fa fa-arrow-left"></i> Back to Cart
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-md-5">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">🛒 Order Summary</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($display_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td class="text-right"><?php echo intval($item['quantity']); ?></td>
                                    <td class="text-right">₱<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="active" style="font-weight: bold; font-size: 16px;">
                                    <td colspan="2" style="text-align: right;">Total:</td>
                                    <td class="text-right">₱<?php echo number_format($cart_total, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="alert alert-info">
                    <h4>ℹ️ Important Information</h4>
                    <ul style="margin-bottom: 0; padding-left: 20px;">
                        <li>Please review your order carefully</li>
                        <li>A confirmation email will be sent to your email address</li>
                        <li>Your order status is "Pending" until we process it</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
<hr>



    <?php include "includes/footer.php"; ?>