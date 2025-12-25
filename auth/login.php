<?php
// 1. Start Session manually here because we need it for logic BEFORE including header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/User.php';

$user = new User();
$error = "";

// 2. Handle Login Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $userData = $user->login($email, $password);

    if ($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_name'] = $userData['name'];
        $_SESSION['user_role'] = $userData['role'];

        // Load the user's cart after successful login
        require_once '../classes/Cart.php';
        $cart = new Cart();
        $cart->loadUserCart();

        if ($userData['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit(); // Always exit after redirect
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0"><i class="bi bi-person-circle"></i> Login</h4>
            </div>

            <div class="card-body p-4">

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>
            </div>

            <div class="card-footer text-center py-3">
                <small class="text-muted">Don't have an account?</small><br>
                <a href="register.php" class="fw-bold">Register here</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>