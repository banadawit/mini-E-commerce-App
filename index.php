
<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';

// 1. Setup Pagination & Search Variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : "";
$limit = 8; // Increased to 8 to match the grid look of admin page
$offset = ($page - 1) * $limit;

// 2. Fetch Data
$productObj = new Product();
$products = $productObj->getProducts($search, $limit, $offset);
$totalProducts = $productObj->countProducts($search);
$totalPages = ceil($totalProducts / $limit);
?>

<div class="card bg-dark text-white shadow mb-4 border-0 rounded-3">
    <div class="card-body p-5 text-center">
        <h1 class="fw-bold text-warning display-5"><i class="bi bi-shop"></i> Welcome to My Shop</h1>
        <p class="lead opacity-75">Browse our collection of <?php echo $totalProducts; ?> unique products.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 offset-md-3">
        <form action="index.php" method="GET" class="input-group shadow-sm">
            <input type="text" name="search" class="form-control border-0 p-3" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-warning px-4 fw-bold"><i class="bi bi-search"></i> Search</button>
            <?php if (!empty($search)): ?>
                <a href="index.php" class="btn btn-light px-3 border-start"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="row g-4">
    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
            <div class="col-md-3">

                <div class="card h-100 shadow-sm border-0 position-relative" style="transition: transform 0.2s;">

                    <div class="position-relative">
                        <?php
                        $img = !empty($p['image']) ? "assets/images/products/" . $p['image'] : "https://via.placeholder.com/300x200?text=No+Image";
                        ?>
                        <img src="<?php echo $img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($p['name']); ?>"
                            style="height: 200px; object-fit: cover; filter: brightness(0.98);">

                        <span class="position-absolute top-0 start-0 bg-warning text-dark px-2 py-1 small fw-bold rounded-end mt-2 shadow-sm">
                            <?php echo htmlspecialchars($p['category_name'] ?? 'General'); ?>
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <h6 class="card-title fw-bold text-truncate text-dark" title="<?php echo htmlspecialchars($p['name']); ?>">
                            <?php echo htmlspecialchars($p['name']); ?>
                        </h6>

                        <p class="card-text small text-muted text-truncate mb-3"><?php echo htmlspecialchars($p['description']); ?></p>

                        <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                            <span class="fs-5 fw-bold text-success">ETB <?php echo number_format($p['price'], 2); ?></span>

                            <?php if ($p['stock'] > 0): ?>
                                <span class="badge bg-light text-success border border-success">
                                    <i class="bi bi-check-circle-fill"></i> In Stock
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-danger border border-danger">
                                    <i class="bi bi-x-circle-fill"></i> Sold Out
                                </span>
                            <?php endif; ?>
                        </div>


<div class="d-grid">
                            <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-dark btn-sm">
                                View Details <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5 text-muted bg-light rounded-3">
            <i class="bi bi-emoji-frown fs-1 d-block mb-3 opacity-50"></i>
            <h5>No products found</h5>
            <p>We couldn't find what you were looking for.</p>
            <a href="index.php" class="btn btn-primary btn-sm mt-2">View All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link text-dark" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link <?php echo ($page == $i) ? 'bg-dark border-dark' : 'text-dark'; ?>"
                        href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link text-dark" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>