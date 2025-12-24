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

$id = $_GET['id'] ?? null;
$product = $productObj->getById($id);

if (!$product) {
    die("Product not found.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cat_id = $_POST['category_id'];

    $image_name = null;

    // Check if a new image is being uploaded
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../assets/images/products/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
    }

    if ($productObj->update($id, $name, $desc, $price, $stock, $cat_id, $image_name)) {
        header("Location: manage_products.php?msg=Updated");
    } else {
        $message = "Error updating product.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <h2>Edit Product</h2>
        <p><?php echo $message; ?></p>
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>

            <label>Description:</label>
            <textarea name="description"><?php echo $product['description']; ?></textarea>

            <label>Price:</label>
            <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>

            <label>Category:</label>
            <select name="category_id">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>>
                        <?php echo $cat['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Current Image:</label><br>
            <img src="../assets/images/products/<?php echo $product['image']; ?>" width="100"><br>

            <label>Change Image (Optional):</label>
            <input type="file" name="image">

            <button type="submit">Update Product</button>
        </form>
        <a href="manage_products.php">Cancel</a>
    </div>
</body>

</html>