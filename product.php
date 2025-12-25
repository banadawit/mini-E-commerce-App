<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';
require_once 'classes/Cart.php';

$productObj = new Product();
$cartObj = new Cart();

// 1. Get Product ID safely
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productObj->getById($id);

// 2. Redirect if product doesn't exist
if (!$product) {
    echo "<div class='container my-5'><div class='alert alert-danger'>Product not found. <a href='index.php'>Go Home</a></div></div>";
    require_once 'includes/footer.php';
    exit;
}

// 3. Handle Add to Cart Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {

    // Check if user is logged in as Customer
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
        // Redirect to login with error message
        header("Location: auth/login.php?error=You must be logged in as a Customer to buy products.");
        exit;
    }

    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    // Server-side validation for stock
    if ($quantity > $product['stock']) {
        $error = "Only " . $product['stock'] . " items are available.";
    } elseif ($quantity < 1) {
        $error = "Quantity must be at least 1.";
    } else {
        // Success: Add to cart and redirect
        $cartObj->add($id, $quantity);
        header("Location: cart.php?success=Product added to cart successfully!");
        exit;
    }
}
?>

<div class="container my-5">
    <a href="index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Back to Products
    </a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6 mb-4">
            <?php
            $img = !empty($product['image']) ? "assets/images/products/" . $product['image'] : "https://via.placeholder.com/600x400?text=No+Image";
            ?>
            <img src="<?php echo $img; ?>" class="img-fluid rounded shadow-sm border" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="col-md-6">
            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="mb-3">
                <span class="badge bg-secondary"><?php echo htmlspecialchars($product['category_name'] ?? 'General'); ?></span>
                <?php if ($product['stock'] > 0): ?>
                    <span class="badge bg-success">In Stock: <?php echo $product['stock']; ?></span>
                <?php else: ?>
                    <span class="badge bg-danger">Out of Stock</span>
                <?php endif; ?>
            </div>

            <h3 class="text-success mb-4">ETB <?php echo number_format($product['price'], 2); ?></h3>

            <p class="lead text-muted"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <hr class="my-4">

            <?php if ($product['stock'] > 0): ?>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                    <form method="POST" class="d-flex align-items-end">
                        <div class="me-3">
                            <label for="qty" class="form-label fw-bold">Quantity</label>
                            <input type="number" id="qty" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width: 100px;">
                        </div>
                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> Administrators cannot place orders. Please log in as a customer.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-lock"></i> <a href="auth/login.php" class="alert-link">Log in</a> as a customer to purchase this item.
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <button class="btn btn-secondary btn-lg" disabled>Item Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>