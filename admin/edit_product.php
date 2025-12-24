<?php
// 1. Logic Block
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/Product.php';
require_once '../classes/Category.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$productObj = new Product();
$categoryObj = new Category();
$categories = $categoryObj->getAll();

// Get Product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productObj->getById($id);

if (!$product) {
    echo "<script>alert('Product not found'); window.location.href='manage_products.php';</script>";
    exit();
}

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $cat_id = (int) $_POST['category_id'];

    $image_name = null; // Default to null (no change)

    // Check if a new image is being uploaded
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../assets/images/products/";
        $fileExtension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
        $target_file = $target_dir . $newFileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExtension, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_name = $newFileName; // Set new image name
            } else {
                $message = "Error uploading new image.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid file type. Allowed: JPG, PNG, GIF.";
            $messageType = "danger";
        }
    }

    // Only proceed if no image error occurred
    if (empty($message)) {
        if ($productObj->update($id, $name, $desc, $price, $stock, $cat_id, $image_name)) {
            header("Location: manage_products.php?msg=updated");
            exit();
        } else {
            $message = "Database error: Could not update product.";
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
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Product</h4>
                    <a href="manage_products.php" class="btn btn-sm btn-light text-primary fw-bold">
                        <i class="bi bi-x-lg"></i> Cancel
                    </a>
                </div>

                <div class="card-body p-4">

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $product['price']; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>

                            <hr class="my-3">

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Product Image</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-center">
                                        <div class="mb-1 text-muted small">Current</div>
                                        <img src="../assets/images/products/<?php echo $product['image']; ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-label small text-muted">Upload new image to replace current one (Optional)</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>