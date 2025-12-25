<?php
// 1. Logic Block
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/Order.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$orderObj = new Order();
$message = "";
$messageType = "";

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];
    $allowedStatuses = ['Pending', 'Shipped', 'Delivered'];

    if (in_array($status, $allowedStatuses, true)) {
        // We use the updateStatus method we created in classes/Order.php
        if ($orderObj->updateStatus($order_id, $status)) {
            $message = "Order #{$order_id} updated to <strong>{$status}</strong>.";
            $messageType = "success";
        } else {
            $message = "Failed to update order status.";
            $messageType = "danger";
        }
    } else {
        $message = "Invalid status selected.";
        $messageType = "warning";
    }
}

// Fetch all orders
$orders = $orderObj->getAllOrders();
?>

<?php require_once '../includes/header.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-truck"></i> Manage Orders</h1>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Date Placed</th>
                            <th>Total Amount</th>
                            <th>Current Status</th>
                            <th style="min-width: 200px;">Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $o): ?>

                                <?php
                                $badgeClass = 'bg-secondary';
                                if ($o['status'] === 'Pending') $badgeClass = 'bg-warning text-dark';
                                elseif ($o['status'] === 'Shipped') $badgeClass = 'bg-primary';
                                elseif ($o['status'] === 'Delivered') $badgeClass = 'bg-success';
                                ?>

                                <tr>
                                    <td class="fw-bold">#<?php echo $o['id']; ?></td>
                                    <td>
                                        <i class="bi bi-person-circle text-muted"></i>
                                        <?php echo htmlspecialchars($o['customer_name']); ?>
                                    </td>
                                    <td><?php echo date("M d, Y", strtotime($o['created_at'])); ?></td>
                                    <td class="fw-bold text-success">ETB <?php echo number_format($o['total_price'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?> rounded-pill">
                                            <?php echo $o['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                            <select name="status" class="form-select form-select-sm border-secondary">
                                                <option value="Pending" <?php if ($o['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Shipped" <?php if ($o['status'] === 'Shipped') echo 'selected'; ?>>Shipped</option>
                                                <option value="Delivered" <?php if ($o['status'] === 'Delivered') echo 'selected'; ?>>Delivered</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-dark" title="Update">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No orders found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>