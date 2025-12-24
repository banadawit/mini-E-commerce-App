<?php
// 1. Logic Block (Must be before any HTML)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/User.php';

$user = new User();
$error = "";
// Initialize variables to keep form data if error occurs (Sticky Form)
$name = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and collect inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- VALIDATION START ---

    // 1. Check for empty fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    }
    // 2. Validate Name (Letters and spaces only, min 3 chars)
    elseif (!preg_match("/^[a-zA-Z\s]{3,}$/", $name)) {
        $error = "Name must be at least 3 characters and contain only letters.";
    }
    // 3. Validate Email Format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    // 4. Validate Password Length (Min 6 chars)
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    }
    // 5. Confirm Password Match
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    // 6. Check if email already exists in Database
    elseif ($user->emailExists($email)) {
        $error = "This email is already registered! Try logging in.";
    }
    // --- VALIDATION END ---

    // Attempt Registration if no errors
    else {
        if ($user->register($name, $email, $password)) {
            // Redirect with success message
            header("Location: login.php?success=Account created successfully! Please login.");
            exit();
        } else {
            $error = "Something went wrong during registration. Please try again.";
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center py-3">
                <h4 class="mb-0"><i class="bi bi-person-plus-fill"></i> Create Account</h4>
            </div>

            <div class="card-body p-4">

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-octagon-fill"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Bana Dawit" value="<?php echo htmlspecialchars($name); ?>" required minlength="3">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="name@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Min 8 characters" required minlength="8">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                placeholder="Re-enter password" required minlength="8">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Register</button>
                    </div>
                </form>
            </div>

            <div class="card-footer text-center py-3">
                <small class="text-muted">Already have an account?</small><br>
                <a href="login.php" class="fw-bold text-success">Login here</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>