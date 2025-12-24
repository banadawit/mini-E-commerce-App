<?php
session_start();
require_once '../classes/Order.php';
require_once '../classes/Admin.php';

// Restrict access to admins only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$orderObj = new Order();
$adminObj = new Admin();
$message = "";

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];
    $allowedStatuses = ['Pending', 'Shipped', 'Delivered'];

    if (in_array($status, $allowedStatuses, true)) {
        if ($adminObj->updateOrderStatus($order_id, $status)) {
            $message = "Order #{$order_id} status updated to {$status}.";
        } else {
            $message = "Failed to update order status.";
        }
    } else {
        $message = "Invalid status value.";
    }
}

$orders = $orderObj->getAllOrders();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Customer Orders</h2>
        <a href="dashboard.php" class="btn-link">&larr; Back to Dashboard</a>

        <?php if (!empty($message)): ?>
            <div class="alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php foreach($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                <td>$<?php echo number_format($o['total_price'], 2); ?></td>
                <td><?php echo $o['status']; ?></td>
                <td><?php echo $o['created_at']; ?></td>
                <td>
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status">
                            <option value="Pending" <?php if ($o['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Shipped" <?php if ($o['status'] === 'Shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="Delivered" <?php if ($o['status'] === 'Delivered') echo 'selected'; ?>>Delivered</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>