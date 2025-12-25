<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';

// 1. SECURITY: Admin Only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

// 2. Setup Pagination & Search
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : "";
$limit = 8;
$offset = ($page - 1) * $limit;

// 3. Fetch Data
$productObj = new Product();
$products = $productObj->getProducts($search, $limit, $offset);
$totalProducts = $productObj->countProducts($search);
$totalPages = ceil($totalProducts / $limit);
?>

<div class="card bg-dark text-white shadow mb-4 border-0 rounded-3">
    <div class="card-body d-flex justify-content-between align-items-center p-4">
        <div>
            <h4 class="mb-1 fw-bold text-warning"><i class="bi bi-grid-fill"></i> Visual Inventory</h4>
            <div class="small opacity-75">Manage your catalog visually. What you see is what customers see.</div>
        </div>
        <div>
            <a href="admin/dashboard.php" class="btn btn-outline-light me-2">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="admin/add_product.php" class="btn btn-warning fw-bold text-dark">
                <i class="bi bi-plus-lg"></i> Add Product
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 offset-md-6">
        <form action="indexadmin.php" method="GET" class="input-group shadow-sm">
            <input type="text" name="search" class="form-control border-0 p-3" placeholder="Search by name or ID..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-dark px-4"><i class="bi bi-search"></i></button>
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
                            style="height: 200px; object-fit: cover; filter: brightness(0.95);">

                        <span class="position-absolute top-0 start-0 bg-dark text-white px-2 py-1 small fw-mono opacity-90 rounded-end mt-2">
                            ID: <strong><?php echo $p['id']; ?></strong>
                        </span>

                        <span class="position-absolute bottom-0 end-0 bg-warning text-dark px-2 py-1 small fw-bold rounded-start mb-2 shadow-sm">
                            <?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?>
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <h6 class="card-title fw-bold text-truncate text-dark" title="<?php echo htmlspecialchars($p['name']); ?>">
                            <?php echo htmlspecialchars($p['name']); ?>
                        </h6>

                        <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                            <span class="text-muted small">Price:</span>
                            <span class="fs-5 fw-bold text-success">ETB <?php echo number_format($p['price'], 2); ?></span>
                        </div>

                        <div class="mb-3">
                            <?php if ($p['stock'] == 0): ?>
                                <div class="badge bg-danger w-100 py-2"><i class="bi bi-x-circle"></i> OUT OF STOCK</div>
                            <?php elseif ($p['stock'] < 5): ?>
                                <div class="badge bg-danger bg-opacity-10 text-danger border border-danger w-100 py-2">
                                    <i class="bi bi-exclamation-triangle"></i> LOW STOCK (<?php echo $p['stock']; ?>)
                                </div>
                            <?php else: ?>
                                <div class="badge bg-light text-secondary border w-100 py-2">
                                    <i class="bi bi-box-seam"></i> <?php echo $p['stock']; ?> units
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid">
                            <a href="admin/edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-pencil-square"></i> Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5 text-muted bg-light rounded-3">
            <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-50"></i>
            <h5>No items found</h5>
            <p>Try adjusting your search terms.</p>
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