<?php
// 1. Session Handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Define Base URL
define('BASE_URL', '/mini-ecommerce/');

// 3. Get Current Page Name (for Active Menu Highlighting)
$currentPage = basename($_SERVER['PHP_SELF']);

// 4. Cart Logic
require_once __DIR__ . '/../classes/Cart.php';
$cart = new Cart();
$cartCount = $cart->getTotalQuantity();

$homeLink = BASE_URL . 'index.php'; // Default for Guests/Customers

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    // UPDATE: Admins now go to the Visual Inventory page
    $homeLink = BASE_URL . 'indexadmin.php';
}
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

    <style>
        /* Small override to make the logout hover look distinct */
        .nav-link.logout-link:hover {
            color: #dc3545 !important;
            /* Bootstrap Danger Red */
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-uppercase" href="<?php echo $homeLink; ?>">
                <i class="bi bi-shop text-warning fs-4 me-1"></i> My Shop
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">

                    <li class="nav-item">
                        <a class="nav-link px-3 <?php echo ($currentPage == 'indexadmin.php') ? 'active fw-bold text-white' : ''; ?>"
                            href="<?php echo BASE_URL; ?>indexadmin.php">Home</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                            <li class="nav-item">
                                <a class="nav-link px-3 <?php echo ($currentPage == 'my_orders.php') ? 'active fw-bold text-white' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>my_orders.php">My Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 position-relative <?php echo ($currentPage == 'cart.php') ? 'active fw-bold text-white' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>cart.php">
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
                                <a class="nav-link px-3 btn btn-sm btn-outline-secondary border-0 text-light ms-2"
                                    href="<?php echo BASE_URL; ?>admin/dashboard.php">
                                    <i class="bi bi-speedometer2"></i> Admin Panel
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a class="nav-link logout-link text-warning fw-semibold border border-warning rounded px-3 py-1"
                                href="#"
                                data-bs-toggle="modal"
                                data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="nav-item">
                            <a class="nav-link px-3 <?php echo ($currentPage == 'login.php') ? 'active fw-bold text-white' : ''; ?>"
                                href="<?php echo BASE_URL; ?>auth/login.php">Login</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-primary btn-sm px-4 rounded-pill fw-bold"
                                href="<?php echo BASE_URL; ?>auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container flex-grow-1">

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>