<?php
session_start();
require_once '../classes/Product.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$productObj = new Product();

// Handle Deletion
if (isset($_GET['delete'])) {
    if ($productObj->delete($_GET['delete'])) {
        header("Location: manage_products.php?msg=Deleted");
    }
}

$products = $productObj->getAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <h2>Manage Products</h2>
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="add_product.php">Add New Product</a>
        </nav>
        <br>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img src="../assets/images/products/<?php echo $p['image']; ?>" width="50"></td>
                    <td><?php echo $p['name']; ?></td>
                    <td>$<?php echo $p['price']; ?></td>
                    <td><?php echo $p['stock']; ?></td>
                    <td><?php echo $p['category_name']; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $p['id']; ?>">Edit</a> |
                        <a href="manage_products.php?delete=<?php echo $p['id']; ?>" style="color:red;" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>