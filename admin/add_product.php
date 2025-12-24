<?php
// 1. Logic Block
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/Product.php';
require_once '../classes/Category.php';

// Security: Ensure Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$productObj = new Product();
$categoryObj = new Category();
$categories = $categoryObj->getAll();
$message = "";
$messageType = ""; // For Bootstrap alert color (success/danger)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $cat_id = (int) $_POST['category_id'];

    // Validation
    if ($price <= 0 || $stock < 0) {
        $message = "Price must be greater than 0 and Stock cannot be negative.";
        $messageType = "danger";
    } elseif ($cat_id <= 0) {
        $message = "Please select a valid category.";
        $messageType = "danger";
    } elseif (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        $message = "Please upload a valid product image.";
        $messageType = "danger";
    } else {
        // File Upload Logic
        $target_dir = "../assets/images/products/";

        // Use time() to ensure unique filenames
        $fileExtension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
        $target_file = $target_dir . $newFileName;

        // Allowed types
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExtension, $allowedTypes)) {
            // Attempt to move the file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Save to Database
                if ($productObj->create($name, $desc, $price, $stock, $cat_id, $newFileName)) {
                    $message = "Product added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Database error: Could not save product.";
                    $messageType = "danger";
                }
            } else {
                $message = "Failed to upload image to server.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid file type. Allowed: JPG, PNG, GIF, WEBP.";
            $messageType = "danger";
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-plus-square"></i> Add New Product</h4>
                    <a href="dashboard.php" class="btn btn-sm btn-light text-primary fw-bold">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <div class="card-body p-4">

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Wireless Headphones" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" class="form-control" step="0.01" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" placeholder="e.g. 50" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <div class="form-text">Allowed: JPG, PNG, GIF</div>
                            </div>

                            <div class="col-12 mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Enter product details..."></textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Save Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>