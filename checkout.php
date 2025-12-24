<?php 
require_once 'includes/header.php'; 
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
require_once 'classes/Product.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php?error=Please login to checkout");
    exit();
}

// Only customers are allowed to go through checkout and place orders
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: index.php?error=Only customers can place orders");
    exit();
}

$cartObj = new Cart();
$orderObj = new Order();
$productObj = new Product();
$items = $cartObj->getItems();

if (empty($items)) {
    header("Location: index.php");
    exit();
}

$total = 0;
foreach ($items as $id => $qty) {
    $p = $productObj->getById($id);
    $total += $p['price'] * $qty;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($orderObj->createOrder($_SESSION['user_id'], $total, $items)) {
        $cartObj->clear();
        echo "<h1>Order Successful!</h1><p>Thank you for your purchase.</p><a href='index.php'>Home</a>";
        exit();
    } else {
        echo "Error processing order.";
    }
}
?>

<h1>Checkout</h1>
<p>Total Amount to Pay: <strong>$<?php echo number_format($total, 2); ?></strong></p>
<form method="POST">
    <p>Payment Method: <strong>Cash on Delivery (Demo)</strong></p>
    <button type="submit">Place Order</button>
</form>

<?php require_once 'includes/footer.php'; ?>