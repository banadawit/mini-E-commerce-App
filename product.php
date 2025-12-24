<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';
require_once 'classes/Cart.php';

$productObj = new Product();
$cartObj = new Cart();

$id = $_GET['id'] ?? null;
$product = $productObj->getById($id);

if (!$product) {
    echo "Product not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    // Only customers are allowed to add products to cart / place orders
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
        echo "<script>alert('Only customers can place orders. Please log in with a customer account.'); window.location.href='auth/login.php';</script>";
        exit;
    }

    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    if ($quantity < 1) {
        $quantity = 1;
    }

    $cartObj->add($id, $quantity);
    echo "<script>alert('Added to cart!'); window.location.href='cart.php';</script>";
}
?>

<div class="product-detail">
    <div style="display:flex; gap:40px;">
        <img src="assets/images/products/<?php echo $product['image']; ?>" style="width:400px;">
        <div>
            <h1><?php echo $product['name']; ?></h1>
            <p class="price">$<?php echo $product['price']; ?></p>
            <p><?php echo $product['description']; ?></p>
            <p><strong>Stock Available:</strong> <?php echo $product['stock']; ?></p>

            <?php if ($product['stock'] > 0): ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                    <form method="POST">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width:60px;">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <p style="color:gray;">Only customers can place orders. Log in with a customer account to buy.</p>
                <?php endif; ?>
            <?php else: ?>
                <p style="color:red;">Out of Stock</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>