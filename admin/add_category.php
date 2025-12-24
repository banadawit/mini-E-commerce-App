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

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        $categoryObj = new Category();
        if ($categoryObj->create($name)) {
            $message = "Category '$name' added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding category.";
            $messageType = "danger";
        }
    } else {
        $message = "Category name cannot be empty.";
        $messageType = "danger";
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-tag-fill"></i> Add Category</h4>
                    <a href="manage_categories.php" class="btn btn-sm btn-light text-success fw-bold">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body p-4">

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="e.g. Smart Watches" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Save Category</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>