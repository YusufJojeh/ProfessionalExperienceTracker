<?php
// Platform Configuration
define('PLATFORM_NAME', 'Profolio Elite');
define('PLATFORM_NAME_AR', 'إيليت المحفظة المهنية');

// Database Configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'professional_experience_tracker';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

// Function to sanitize input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to generate random string
function generate_random_string($length = 10) {
    return bin2hex(random_bytes($length));
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?> 