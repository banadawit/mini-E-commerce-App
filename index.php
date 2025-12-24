<?php
require_once 'includes/header.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

// 1. Setup Pagination, Search, and Sort Variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : "";
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$sort = isset($_GET['sort']) ? ($_GET['sort'] === 'popular' ? 'newest' : $_GET['sort']) : 'newest';
$limit = 8;
$offset = ($page - 1) * $limit;

// 2. Fetch Data
$productObj = new Product();
$categoryObj = new Category();
$products = $productObj->getProducts($search, $limit, $offset, $category_id, $sort);
$totalProducts = $productObj->countProducts($search, $category_id);
$totalPages = ceil($totalProducts / $limit);
$categories = $categoryObj->getAll();
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Discover Amazing Products</h1>
                <p class="lead mb-4">Shop from our collection of <?php echo $totalProducts; ?>+ high-quality products at the best prices.</p>
                <a href="#products" class="btn btn-light btn-lg px-4 me-2">Shop Now</a>
                <a href="#categories" class="btn btn-outline-light btn-lg px-4">Browse Categories</a>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="https://via.placeholder.com/600x400" alt="Hero Image" class="img-fluid rounded-3 shadow">
            </div>
        </div>
    </div>
</section>

<!-- Search and Filter Section -->
<section class="py-4 bg-light" id="categories">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-8">
                <form action="index.php" method="GET" class="search-form">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary px-4">Search</button>
                        <?php if (!empty($search) || !empty($category_id)): ?>
                            <a href="index.php" class="btn btn-outline-secondary">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <select class="form-select" onchange="if(this.value) window.location.href='?category='+this.value">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="py-5" id="products">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold mb-0">Featured Products</h2>
            <div class="d-flex align-items-center">
                <span class="text-muted me-2">Sort by:</span>
                <select id="sortSelect" class="form-select form-select-sm w-auto" onchange="handleSortChange(this.value)">
                    <option value="price_asc" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="newest" <?php echo ($sort === 'newest' || $sort === 'popular') ? 'selected' : ''; ?>>Newest First</option>
                </select>
            </div>
        </div>

        <?php if (count($products) > 0): ?>
            <div class="row g-4">
                <?php foreach ($products as $p): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card h-100 border-0 shadow-sm">
                            <div class="position-relative">
                                <?php
                                $img = !empty($p['image']) 
                                    ? "assets/images/products/" . $p['image'] 
                                    : "https://via.placeholder.com/300x300?text=No+Image";
                                ?>
                                <img src="<?php echo $img; ?>" 
                                     class="card-img-top product-image" 
                                     alt="<?php echo htmlspecialchars($p['name']); ?>">
                                
                                <div class="product-badges">
                                    <?php if ($p['stock'] > 0): ?>
                                        <span class="badge bg-success">In Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Sold Out</span>
                                    <?php endif; ?>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($p['category_name'] ?? 'General'); ?></span>
                                </div>
                                
                                <div class="product-actions">
                                    <button class="btn btn-sm btn-light rounded-circle me-1" title="Add to Wishlist">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light rounded-circle" title="Quick View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body p-3">
                                <h6 class="card-title mb-1 text-truncate" title="<?php echo htmlspecialchars($p['name']); ?>">
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </h6>
                                <p class="text-muted small mb-2 text-truncate-2" style="height: 2.5rem;">
                                    <?php echo htmlspecialchars($p['description']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary mb-0">$<?php echo number_format($p['price'], 2); ?></span>
                                    <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-5" aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>

                        <?php 
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $start + 4);
                        $start = max(1, $end - 4);
                        
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1&search='.urlencode($search).'&category='.$category_id.'&sort='.$sort.'">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" 
                                   href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php 
                        endfor; 
                        
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="?page='.$totalPages.'&search='.urlencode($search).'&category='.$category_id.'&sort='.$sort.'">'.$totalPages.'</a></li>';
                        }
                        ?>

                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-5 my-5">
                <div class="mb-4">
                    <i class="bi bi-search display-1 text-muted"></i>
                </div>
                <h4 class="mb-3">No products found</h4>
                <p class="text-muted mb-4">We couldn't find any products matching your search.</p>
                <a href="index.php" class="btn btn-primary px-4">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="p-4 bg-white rounded-3 h-100">
                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; line-height: 60px;">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                    <h5 class="h6 fw-bold mb-2">Free Shipping</h5>
                    <p class="small text-muted mb-0">On all orders over $50</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4 bg-white rounded-3 h-100">
                    <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; line-height: 60px;">
                        <i class="bi bi-arrow-repeat fs-4"></i>
                    </div>
                    <h5 class="h6 fw-bold mb-2">Easy Returns</h5>
                    <p class="small text-muted mb-0">30-day return policy</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4 bg-white rounded-3 h-100">
                    <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; line-height: 60px;">
                        <i class="bi bi-headset fs-4"></i>
                    </div>
                    <h5 class="h6 fw-bold mb-2">24/7 Support</h5>
                    <p class="small text-muted mb-0">Dedicated support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="h2 mb-3">Subscribe to Our Newsletter</h3>
                <p class="lead mb-4">Get the latest updates on new products and upcoming sales</p>
                <form class="row g-2 justify-content-center">
                    <div class="col-md-8">
                        <input type="email" class="form-control form-control-lg" placeholder="Enter your email">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-light btn-lg px-4">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    .hero-section {
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        padding: 4rem 0;
        margin-bottom: 3rem;
    }
    
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .product-image {
        height: 160px;
        width: 100%;
        object-fit: contain;
        padding: 10px;
        transition: transform 0.5s ease;
    }
    
    .product-card:hover .product-image {
        transform: scale(1.03);
    }
    
    .product-badges {
        position: absolute;
        top: 10px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        padding: 0 15px;
    }
    
    .product-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .product-card:hover .product-actions {
        opacity: 1;
    }
    
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .pagination .page-link {
        min-width: 40px;
        text-align: center;
        margin: 0 3px;
        border-radius: 4px !important;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #4e54c8;
        border-color: #4e54c8;
    }
    
    .search-form .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
    
    .search-form .input-group-text {
        background: transparent;
    }
    
    @media (max-width: 767.98px) {
        .hero-section {
            text-align: center;
            padding: 3rem 0;
        }
        
        .product-actions {
            opacity: 1;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>

<script>
    // Handle sort change
    function handleSortChange(sortValue) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortValue);
        // Reset to first page when changing sort
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    // Add to cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add loading animation for product cards
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    });
</script>