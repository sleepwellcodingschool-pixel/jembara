
<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['upload_file'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$uploadDir = '../uploads/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
$maxSize = 5 * 1024 * 1024; // 5MB

$file = $_FILES['upload_file'];

if ($file['error'] !== 0) {
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'File type not allowed']);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large']);
    exit;
}

$filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
$filepath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Save to database
    $stmt = $db->prepare("INSERT INTO media_files (filename, original_name, file_path, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $filename, $file['name'], $filepath, $file['type'], $file['size'], $_SESSION['admin_id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'File uploaded successfully',
            'file_path' => str_replace('../', '/', $filepath),
            'filename' => $filename
        ]);
    } else {
        unlink($filepath);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>
