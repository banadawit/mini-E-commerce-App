<?php
session_start();
require_once '../classes/Admin.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Instantiate Admin class to get stats
$adminObj = new Admin();
$stats = $adminObj->getDashboardStats();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Mini E-Commerce</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Additional styling specifically for the dashboard layout */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-top: 5px solid #27ae60;
        }

        .stat-card h3 {
            margin: 0;
            color: #555;
            font-size: 16px;
            text-transform: uppercase;
        }

        .stat-card p {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0 0 0;
        }

        .admin-nav {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .admin-nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #27ae60;
            font-weight: bold;
        }

        .admin-nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="admin-container">
            <h1>Admin Dashboard</h1>

            <div class="admin-nav">
                <a href="manage_products.php">📦 Manage Products</a>
                <a href="orders.php">🛒 View Orders</a>
                <!-- View Site shows the storefront, but admin still cannot order because of role checks -->
                <a href="../index.php" target="_blank">🌐 View Site</a>
                <a href="../logout.php" style="color: #e74c3c;">🚪 Logout</a>
            </div>

            <div class="welcome-msg">
                <p>Welcome back, <strong><?php echo $_SESSION['user_name']; ?></strong>! Here is your store overview:</p>
            </div>

            <!-- Dashboard Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p><?php echo $stats['total_products']; ?></p>
                </div>

                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p><?php echo $stats['total_orders']; ?></p>
                </div>

                <div class="stat-card" style="border-top-color: #f1c40f;">
                    <h3>Total Revenue</h3>
                    <p>$<?php echo number_format($stats['total_revenue'], 2); ?></p>
                </div>

                <div class="stat-card" style="border-top-color: #3498db;">
                    <h3>Customers</h3>
                    <p><?php echo $stats['total_customers']; ?></p>
                </div>
            </div>

        </div>
    </div>

</body>

</html>