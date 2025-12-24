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

<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-box-seam"></i> My Order History</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="card text-center p-5 shadow-sm">
            <div class="card-body">
                <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                <h3 class="mt-3 text-muted">No orders found</h3>
                <p class="mb-4">You haven't placed any orders yet.</p>
                <a href="index.php" class="btn btn-primary">Start Shopping</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Order #</th>
                                <th>Date Placed</th>
                                <th>Products Summary</th>
                                <th>Items</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                // Determine Status Badge Color
                                $statusClass = 'bg-secondary';
                                switch ($order['status']) {
                                    case 'Pending':
                                        $statusClass = 'bg-warning text-dark';
                                        break;
                                    case 'Shipped':
                                        $statusClass = 'bg-primary';
                                        break;
                                    case 'Delivered':
                                        $statusClass = 'bg-success';
                                        break;
                                    case 'Cancelled':
                                        $statusClass = 'bg-danger';
                                        break;
                                }
                                ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $order['id']; ?></td>
                                    <td><?php echo date("M d, Y", strtotime($order['created_at'])); ?></td>
                                    <td class="text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($order['product_names']); ?>">
                                        <?php echo htmlspecialchars($order['product_names']); ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $order['items_count']; ?></span></td>
                                    <td class="fw-bold text-success">$<?php echo number_format($order['total_price'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>