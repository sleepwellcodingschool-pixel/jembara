
<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$mediaFiles = $db->query("SELECT id, filename, original_name, file_path, file_type, file_size 
                         FROM media_files 
                         WHERE file_type LIKE 'image/%' 
                         ORDER BY created_at DESC");

$files = [];
if ($mediaFiles && $mediaFiles->num_rows > 0) {
    while ($file = $mediaFiles->fetch_assoc()) {
        $file['file_path'] = str_replace('../', '/', $file['file_path']);
        $files[] = $file;
    }
}

echo json_encode($files);
?>
