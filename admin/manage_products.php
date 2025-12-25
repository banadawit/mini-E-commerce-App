<?php
// 1. Logic Block
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/Product.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$productObj = new Product();

// Handle Deletion
$msg = "";
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($productObj->delete($id)) {
        header("Location: manage_products.php?msg=deleted");
        exit();
    } else {
        $msg = "Error deleting product.";
    }
}

// Fetch Products
$products = $productObj->getAll();
?>

<?php require_once '../includes/header.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-box-seam"></i> Manage Products</h1>
        <div>
            <a href="dashboard.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="add_product.php" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add New Product
            </a>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> Product deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 10%;">Image</th>
                            <th style="width: 25%;">Product Name</th>
                            <th style="width: 15%;">Category</th>
                            <th style="width: 10%;">Price</th>
                            <th style="width: 10%;">Stock</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td>#<?php echo $p['id']; ?></td>
                                    <td>
                                        <?php
                                        $img = !empty($p['image']) ? "../assets/images/products/" . $p['image'] : "https://via.placeholder.com/50";
                                        ?>
                                        <img src="<?php echo $img; ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['category_name'] ?? 'None'); ?></span></td>
                                    <td>ETB <?php echo number_format($p['price'], 2); ?></td>
                                    <td>
                                        <?php if ($p['stock'] < 5): ?>
                                            <span class="text-danger fw-bold"><?php echo $p['stock']; ?> (Low)</span>
                                        <?php else: ?>
                                            <span class="text-success"><?php echo $p['stock']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger delete-product" title="Delete" data-product-id="<?php echo $p['id']; ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No products found. Add one to get started!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-trash-fill"></i> Delete Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.delete-product');
    var confirmDeleteBtn = document.getElementById('confirmDelete');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.getAttribute('data-product-id');
            confirmDeleteBtn.href = 'manage_products.php?delete=' + productId;
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>