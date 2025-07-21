<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
    $language = $_POST['language'];
    
    // Validate language
    if (in_array($language, ['en', 'ar'])) {
        $_SESSION['language'] = $language;
        
        // Update user's language preference in database if logged in
        if (isset($_SESSION['user_id'])) {
            require_once 'config/database.php';
            $user_id = $_SESSION['user_id'];
            $update_query = "UPDATE users SET language = '$language' WHERE id = $user_id";
            mysqli_query($conn, $update_query);
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid language']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?> 