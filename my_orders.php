<?php
require_once 'includes/header.php';
require_once 'classes/Order.php';

// Only logged-in customers can view their orders
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: auth/login.php?error=Please login as a customer to view your orders");
    exit();
}

$orderObj = new Order();
$orders = $orderObj->getOrdersByUser($_SESSION['user_id']);
?>

<h1>My Orders</h1>

<?php if (empty($orders)): ?>
    <p>You have no orders yet. <a href="index.php">Start shopping</a></p>
<?php else: ?>
    <table>
        <tr>
            <th>Products</th>
            <th>Total Price</th>
            <th>Items</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['product_names']); ?></td>
            <td>$<?php echo number_format($order['total_price'], 2); ?></td>
            <td><?php echo $order['items_count']; ?></td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td><?php echo $order['created_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>


