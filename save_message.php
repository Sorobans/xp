<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $date = date('Y-m-d H:i:s');

    // Validate input
    if (empty($name) || empty($message)) {
        throw new Exception('Name and message are required');
    }

    if (strlen($name) > 50) {
        throw new Exception('Name is too long (max 50 characters)');
    }

    if (strlen($message) > 500) {
        throw new Exception('Message is too long (max 500 characters)');
    }

    // Sanitize input
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Format the message entry
    $entry = "$name | $date\n$message\n\n";
    
    // Write to file
    if (file_put_contents('guestbook.txt', $entry, FILE_APPEND | LOCK_EX) === false) {
        throw new Exception('Failed to write to guestbook');
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 