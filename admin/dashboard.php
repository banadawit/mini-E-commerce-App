<?php
// Note: We don't include header.php here because the admin panel often has a different layout.
// However, to keep it simple and consistent, we can reuse it, or just include the CSS.
// Let's reuse header.php but ensure we handle the path correctly.

require_once '../includes/header.php';
require_once '../classes/Admin.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect non-admins to login
    echo "<script>window.location.href='../auth/login.php';</script>";
    exit();
}

$adminObj = new Admin();
$stats = $adminObj->getDashboardStats();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
        <div>
            <a href="add_product.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Add Product</a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card text-white bg-primary h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase">Total Products</h6>
                        <h2 class="mb-0"><?php echo $stats['total_products']; ?></h2>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
                <a href="manage_products.php" class="card-footer text-white text-decoration-none text-center bg-primary border-0 small">
                    Manage Products <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase">Total Orders</h6>
                        <h2 class="mb-0"><?php echo $stats['total_orders']; ?></h2>
                    </div>
                    <i class="bi bi-cart-check fs-1 opacity-50"></i>
                </div>
                <a href="orders.php" class="card-footer text-white text-decoration-none text-center bg-success border-0 small">
                    View Orders <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-dark bg-warning h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase">Customers</h6>
                        <h2 class="mb-0"><?php echo $stats['total_customers']; ?></h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
                <div class="card-footer text-center bg-warning border-0 small">
                    Active Users
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase">Revenue</h6>
                        <h2 class="mb-0">ETB <?php echo number_format($stats['total_revenue'], 2); ?></h2>
                    </div>
                    <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                </div>
                <div class="card-footer text-center bg-danger border-0 small">
                    Lifetime Earnings
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-gear"></i> Quick Actions
                </div>
                <div class="list-group list-group-flush">
                    <a href="add_product.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-plus-circle text-success me-2"></i> Add New Product
                    </a>
                    <a href="manage_products.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-list-ul text-primary me-2"></i> Edit Existing Products
                    </a>
                    <a href="orders.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-clipboard-data text-info me-2"></i> Manage Customer Orders
                    </a>
                    <a href="../index.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-eye text-secondary me-2"></i> View Live Site
                    </a>
                    <a href="manage_categories.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-tags text-warning me-2"></i> Manage Categories
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-info-circle"></i> System Info
                </div>
                <div class="card-body">
                    <p><strong>Admin Logged in:</strong> <?php echo $_SESSION['user_name']; ?></p>
                    <p><strong>Server Date:</strong> <?php echo date('Y-m-d H:i'); ?></p>
                    <hr>
                    <div class="alert alert-info mb-0">
                        <small><i class="bi bi-lightbulb"></i> Tip: Always check stock levels after big sales.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>