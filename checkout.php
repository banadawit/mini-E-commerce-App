<?php
require_once 'includes/header.php';
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
require_once 'classes/Product.php';

// 1. Security Checks
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php?error=Please login to checkout");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: index.php?error=Only customers can place orders");
    exit();
}

$cartObj = new Cart();
$orderObj = new Order();
$productObj = new Product();
$items = $cartObj->getItems();

// 2. Redirect if cart is empty
if (empty($items)) {
    header("Location: index.php");
    exit();
}

// 3. Calculate Total
$total = 0;
$order_details = [];
foreach ($items as $id => $qty) {
    $p = $productObj->getById($id);
    if ($p) {
        $total += $p['price'] * $qty;
        $order_details[] = [
            'name' => $p['name'],
            'price' => $p['price'],
            'qty' => $qty,
            'subtotal' => $p['price'] * $qty
        ];
    }
}

// 4. Handle Post Request (Place Order)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Attempt to create order
    if ($orderObj->createOrder($_SESSION['user_id'], $total, $items)) {
        $cartObj->clear();
        // Redirect to My Orders page with success message
        header("Location: my_orders.php?success=Order placed successfully! Thank you for your purchase.");
        exit();
    } else {
        $error = "Failed to place order. Some items might be out of stock.";
    }
}
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-check-circle"></i> Checkout</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Review Your Items</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_details as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['qty']; ?></td>
                                    <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="fw-bold">Total Amount</td>
                                <td class="text-end fw-bold text-success fs-5">$<?php echo number_format($total, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <a href="cart.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Cart</a>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-credit-card"></i> Payment Details
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Payment Method</label>
                        <div class="d-flex align-items-center border p-2 rounded bg-light">
                            <i class="bi bi-cash-coin fs-4 text-success me-2"></i>
                            <div>
                                <strong>Cash on Delivery</strong>
                                <div class="small text-muted">Pay when you receive the items</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold">Total to Pay:</span>
                        <span class="fw-bold text-success fs-4">$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <form method="POST">
                        <button type="submit" class="btn btn-success w-100 py-2 fs-5">
                            Confirm Order <i class="bi bi-check-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>