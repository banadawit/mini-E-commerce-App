<?php
session_start();
require_once '../classes/Product.php';
require_once '../classes/Category.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$productObj = new Product();
$categoryObj = new Category();
$categories = $categoryObj->getAll();
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $cat_id = (int) $_POST['category_id'];

    if ($price <= 0 || $stock < 0) {
        $message = "Please enter a valid price and stock (price > 0, stock ≥ 0).";
    } elseif ($cat_id <= 0) {
        $message = "Please select a valid category.";
    } elseif (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        $message = "Please upload a valid product image.";
    } else {

        // File Upload Logic
        $target_dir = "../assets/images/products/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Simple validation (JPG, PNG, GIF)
        if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                if ($productObj->create($name, $desc, $price, $stock, $cat_id, $file_name)) {
                    $message = "Product added successfully!";
                } else {
                    $message = "Error saving product to database.";
                }
            } else {
                $message = "Error uploading image.";
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Add New Product</h2>
    <p><?php echo $message; ?></p>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required><br>
        <textarea name="description" placeholder="Description"></textarea><br>
        <input type="number" name="price" step="0.01" placeholder="Price" required><br>
        <input type="number" name="stock" placeholder="Stock Quantity" required><br>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
            <?php endforeach; ?>
        </select><br>
        <input type="file" name="image" required><br>
        <button type="submit">Add Product</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>