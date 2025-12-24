<?php 
require_once 'includes/header.php'; 
require_once 'classes/Product.php';
require_once 'classes/Cart.php';

// Only customers should use the shopping cart
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: index.php");
    exit();
}

$cartObj = new Cart();
$productObj = new Product();

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $product_id => $qty) {
        $cartObj->update($product_id, $qty);
    }
    header("Location: cart.php");
    exit();
}

$items = $cartObj->getItems();

if (isset($_GET['remove'])) {
    $cartObj->remove($_GET['remove']);
    header("Location: cart.php");
    exit();
}
?>

<h1>Your Shopping Cart</h1>

<?php if (empty($items)): ?>
    <p>Your cart is empty. <a href="index.php">Go shopping!</a></p>
<?php else: ?>
    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
        <form method="POST">
        <?php 
        $total = 0;
        foreach ($items as $id => $qty): 
            $p = $productObj->getById($id);
            if (!$p) continue;
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td>$<?php echo number_format($p['price'], 2); ?></td>
            <td>
                <input type="number" name="quantities[<?php echo $id; ?>]" value="<?php echo $qty; ?>" min="1" style="width:60px;">
            </td>
            <td>$<?php echo number_format($subtotal, 2); ?></td>
            <td><a href="cart.php?remove=<?php echo $id; ?>" class="link-danger">Remove</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" align="right"><strong>Total:</strong></td>
            <td colspan="2"><strong>$<?php echo number_format($total, 2); ?></strong></td>
        </tr>
    </table>
    <div class="cart-actions">
        <button type="submit">Update Cart</button>
        <a href="checkout.php" class="btn-secondary">Proceed to Checkout</a>
    </div>
    </form>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>