<?php
session_start();
require_once 'config/database.php';

// Test script to verify role checking functionality
echo "<h1>Role Checking Test</h1>";

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
    echo "<p><strong>User Name:</strong> " . $_SESSION['user_name'] . "</p>";
    echo "<p><strong>User Email:</strong> " . $_SESSION['user_email'] . "</p>";
    echo "<p><strong>User Role:</strong> " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Not set') . "</p>";
    
    // Test is_admin() function
    echo "<p><strong>Is Admin (function):</strong> " . (is_admin() ? 'Yes' : 'No') . "</p>";
    
    // Test manual role check
    echo "<p><strong>Is Admin (manual):</strong> " . (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'Yes' : 'No') . "</p>";
    
    // Show appropriate links
    if (is_admin()) {
        echo "<p><a href='admin.php'>Go to Admin Dashboard</a></p>";
        echo "<p><a href='manage_users.php'>Manage Users</a></p>";
        echo "<p><a href='manage_projects.php'>Manage Projects</a></p>";
        echo "<p><a href='manage_categories.php'>Manage Categories</a></p>";
        echo "<p><a href='platform_settings.php'>Platform Settings</a></p>";
    } else {
        echo "<p><a href='dashboard.php'>Go to User Dashboard</a></p>";
    }
    
    echo "<p><a href='logout.php'>Logout</a></p>";
} else {
    echo "<p>No user logged in.</p>";
    echo "<p><a href='login.php'>Login</a></p>";
    echo "<p><a href='register.php'>Register</a></p>";
}

// Test database connection and show user roles
echo "<h2>Database Test</h2>";
$users_query = "SELECT id, username, full_name, email, role FROM users ORDER BY id";
$users_result = mysqli_query($conn, $users_query);

if ($users_result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th></tr>";
    
    while ($user = mysqli_fetch_assoc($users_result)) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['full_name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Error querying users: " . mysqli_error($conn) . "</p>";
}

echo "<h2>Session Variables</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
