<?php
// 1. Session Handling: Start only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Define Base URL to fix broken links if included in subfolders
// Adjust '/mini-ecommerce/' to match your actual folder name in htdocs
define('BASE_URL', '/mini-ecommerce/');

// 3. Cart Logic: Get item count for the badge
require_once __DIR__ . '/../classes/Cart.php';
$cart = new Cart();
$cartCount = $cart->getTotalQuantity();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini E-Commerce</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>index.php">
                <i class="bi bi-shop"></i> My Shop
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php">Home</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>my_orders.php">My Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="<?php echo BASE_URL; ?>cart.php">
                                    <i class="bi bi-cart-fill"></i> Cart
                                    <?php if ($cartCount > 0): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <?php echo $cartCount; ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link btn btn-sm btn-outline-light ms-2" href="<?php echo BASE_URL; ?>admin/dashboard.php">Admin Panel</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item ms-2">
                            <a class="nav-link text-warning" href="<?php echo BASE_URL; ?>logout.php" onclick="return confirm('Are you sure you want to logout?');">
                                Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)
                            </a>
                        </li>

                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white ms-2" href="<?php echo BASE_URL; ?>auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container flex-grow-1">

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>