<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';
require_once 'classes/Cart.php';

// Only customers should use the shopping cart
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$cartObj = new Cart();
$productObj = new Product();

// Handle Quantity Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $product_id => $qty) {
            $cartObj->update($product_id, $qty);
        }
    }
    // Refresh to reflect changes
    echo "<script>window.location.href='cart.php';</script>";
    exit();
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $cartObj->remove($_GET['remove']);
    echo "<script>window.location.href='cart.php';</script>";
    exit();
}

$items = $cartObj->getItems();
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-cart3"></i> Your Shopping Cart</h1>

    <?php if (empty($items)): ?>
        <div class="text-center py-5 border rounded bg-light">
            <h3 class="text-muted">Your cart is empty</h3>
            <p class="mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="index.php" class="btn btn-primary btn-lg">Start Shopping</a>
        </div>
    <?php else: ?>

        <form method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%;">Image</th>
                                    <th style="width: 40%;">Product</th>
                                    <th style="width: 15%;">Price</th>
                                    <th style="width: 20%;">Quantity</th>
                                    <th style="width: 15%;">Total</th>
                                    <th style="width: 5%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grand_total = 0;
                                foreach ($items as $id => $qty):
                                    $p = $productObj->getById($id);
                                    if (!$p) continue; // Skip if product deleted

                                    $subtotal = $p['price'] * $qty;
                                    $grand_total += $subtotal;

                                    $img = !empty($p['image']) ? "assets/images/products/" . $p['image'] : "https://via.placeholder.com/50";
                                ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $img; ?>" alt="Product" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <a href="product.php?id=<?php echo $id; ?>" class="text-decoration-none fw-bold text-dark">
                                                <?php echo htmlspecialchars($p['name']); ?>
                                            </a>
                                            <div class="small text-muted">Stock: <?php echo $p['stock']; ?></div>
                                        </td>
                                        <td>$<?php echo number_format($p['price'], 2); ?></td>
                                        <td>
                                            <input type="number" name="quantities[<?php echo $id; ?>]" value="<?php echo $qty; ?>" min="1" max="<?php echo $p['stock']; ?>" class="form-control form-control-sm">
                                        </td>
                                        <td class="fw-bold">$<?php echo number_format($subtotal, 2); ?></td>
                                        <td>
                                            <a href="cart.php?remove=<?php echo $id; ?>" class="text-danger" title="Remove Item" onclick="return confirm('Are you sure?');">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
                        <button type="submit" name="update_cart" class="btn btn-warning"><i class="bi bi-arrow-clockwise"></i> Update Cart</button>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold">Order Summary</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Items:</span>
                                <span><?php echo count($items); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fs-5 fw-bold">Total:</span>
                                <span class="fs-5 fw-bold text-success">$<?php echo number_format($grand_total, 2); ?></span>
                            </div>

                            <a href="checkout.php" class="btn btn-success w-100 py-2">
                                Proceed to Checkout <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>