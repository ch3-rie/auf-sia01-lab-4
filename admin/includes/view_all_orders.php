<?php
// Fetch all orders
$query = "SELECT * FROM orders ORDER BY order_date DESC";
$result = mysqli_query($connection, $query);

if (!$result) {
    echo "<div class='alert alert-danger'>Error fetching orders: " . mysqli_error($connection) . "</div>";
} elseif (mysqli_num_rows($result) === 0) {
    echo "<div class='alert alert-info'><i class='fa fa-info-circle'></i> No orders found.</div>";
} else {
?>
    <form action="" method="POST">
        <table class="table table-bordered table-hover" id="vieworders">
            <thead>
                <tr>
                    <th><input type='checkbox' id='selectAllBoxes' onclick="selectAll(this)"></th>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>User ID</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($order = mysqli_fetch_assoc($result)) {
                    $order_id = $order['order_id'];
                    $status_badge = 'danger';
                    if ($order['status'] === 'completed') {
                        $status_badge = 'success';
                    } elseif ($order['status'] === 'processing') {
                        $status_badge = 'info';
                    } elseif ($order['status'] === 'pending') {
                        $status_badge = 'warning';
                    }
                    
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='checkBoxArray[]' value='" . $order_id . "'></td>";
                    echo "<td>#" . str_pad($order_id, 6, '0', STR_PAD_LEFT) . "</td>";
                    echo "<td>" . date('M d, Y H:i', strtotime($order['order_date'])) . "</td>";
                    echo "<td><strong>₱" . number_format($order['total_amount'], 2) . "</strong></td>";
                    echo "<td>";
                    if ($order['user_id']) {
                        echo $order['user_id'];
                    } else {
                        echo "<em class='text-muted'>Guest</em>";
                    }
                    echo "</td>";
                    echo "<td><span class='label label-" . $status_badge . "'>" . ucfirst(htmlspecialchars($order['status'])) . "</span></td>";
                    echo "<td><a href='orders.php?source=view_order&order_id=" . $order_id . "' class='btn btn-primary btn-xs'><i class='fa fa-eye'></i> View</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </form>
<?php } ?>

<script>
function selectAll(source) {
    var checkboxes = document.querySelectorAll("input[name='checkBoxArray[]']");
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>
