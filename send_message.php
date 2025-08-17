<?php
header('Content-Type: application/json');

// Simple contact form handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($email) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email and message are required.'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }
    
    // In a real application, you would send an email here
    // For now, we'll just simulate success
    
    // Log the message (optional)
    $log_entry = date('Y-m-d H:i:s') . " - Email: $email, Message: $message\n";
    file_put_contents('contact_log.txt', $log_entry, FILE_APPEND | LOCK_EX);
    
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully! We will get back to you soon.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
