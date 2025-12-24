<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';

// 1. Setup Pagination & Search Variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : "";
$limit = 6; // Products per page
$offset = ($page - 1) * $limit;

// 2. Fetch Data
$productObj = new Product();
$products = $productObj->getProducts($search, $limit, $offset);
$totalProducts = $productObj->countProducts($search);
$totalPages = ceil($totalProducts / $limit);
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1>Available Products</h1>
        <small class="text-muted">Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> results</small>
    </div>
    <div class="col-md-6">
        <form action="index.php" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
            <?php if (!empty($search)): ?>
                <a href="index.php" class="btn btn-outline-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="row">
    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php
                    $img = !empty($p['image']) ? "assets/images/products/" . $p['image'] : "https://via.placeholder.com/300x200?text=No+Image";
                    ?>
                    <img src="<?php echo $img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($p['name']); ?>" style="height: 200px; object-fit: cover;">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></p>
                        <p class="card-text text-truncate"><?php echo htmlspecialchars($p['description']); ?></p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-success">$<?php echo number_format($p['price'], 2); ?></span>

                            <?php if ($p['stock'] > 0): ?>
                                <span class="badge bg-success">In Stock</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0">
                        <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <h3 class="text-muted">No products found.</h3>
            <p>Try searching for something else.</p>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>