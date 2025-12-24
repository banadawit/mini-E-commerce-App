<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/Category.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$categoryObj = new Category();

// Handle Deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($categoryObj->delete($id)) {
        header("Location: manage_categories.php?msg=deleted");
        exit();
    }
}

$categories = $categoryObj->getAll();
?>

<?php require_once '../includes/header.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-tags"></i> Manage Categories</h1>
        <div>
            <a href="dashboard.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="add_category.php" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add New Category
            </a>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Category deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th style="width: 70%;">Category Name</th>
                            <th style="width: 20%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>#<?php echo $cat['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td>
                                    <a href="manage_categories.php?delete=<?php echo $cat['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure? Products in this category will become uncategorized.')">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>