<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';

$productObj = new Product();
$products = $productObj->getAll();
?>

<h1>Available Products</h1>

<div class="product-grid">
    <?php foreach ($products as $p): ?>
        <div class="product-card">
            <img src="assets/images/products/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>">
            <h3><?php echo $p['name']; ?></h3>
            <p class="price">$<?php echo $p['price']; ?></p>
            <p>Stock: <?php echo $p['stock']; ?></p>
            <a href="product.php?id=<?php echo $p['id']; ?>">
                <button>View Details</button>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>