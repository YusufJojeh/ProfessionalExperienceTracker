<?php
session_start();

// Destroy all session data
session_destroy();

// Clear any cookies
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to home page
header("Location: index.php");
exit();
?> 