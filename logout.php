<?php
// 1. Initialize the session
session_start();

// 2. Clear cart from session before destroying session
if (isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 3. Unset all of the session variables
$_SESSION = array();

// 4. Delete the session cookie (Best Practice for full logout)
// This ensures the browser forgets the session ID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 5. Destroy the session
session_destroy();

// 6. Redirect with a success message
header("Location: auth/login.php?success=You have been logged out successfully.");
exit();
