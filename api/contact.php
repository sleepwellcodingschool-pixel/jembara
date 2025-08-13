<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Method not allowed';
    echo json_encode($response);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Fallback to POST data if JSON is not provided
    if (!$input) {
        $input = $_POST;
    }
    
    $name = sanitize($input['name'] ?? '');
    $email = sanitize($input['email'] ?? '');
    $phone = sanitize($input['phone'] ?? '');
    $subject = sanitize($input['subject'] ?? '');
    $message = sanitize($input['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Nama, email, dan pesan wajib diisi.';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Format email tidak valid.';
        echo json_encode($response);
        exit;
    }
    
    if (strlen($message) < 10) {
        $response['message'] = 'Pesan minimal 10 karakter.';
        echo json_encode($response);
        exit;
    }
    
    // Check for spam (simple rate limiting)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM contact_inquiries WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $recentCount = $stmt->get_result()->fetch_assoc()['count'];
    
    if ($recentCount >= 3) {
        $response['message'] = 'Terlalu banyak pesan dari email ini dalam 1 jam terakhir. Silakan coba lagi nanti.';
        echo json_encode($response);
        exit;
    }
    
    // Insert into database
    $stmt = $db->prepare("INSERT INTO contact_inquiries (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Pesan Anda berhasil dikirim! Kami akan merespons dalam 1x24 jam.';
        
        // Optional: Send notification email to admin
        $adminEmail = getSetting('contact_email', 'admin@example.com');
        $siteTitle = getSetting('site_title', 'Website');
        
        $emailSubject = "Pesan Baru dari Website - " . $siteTitle;
        $emailBody = "Pesan baru dari:\n\n";
        $emailBody .= "Nama: " . $name . "\n";
        $emailBody .= "Email: " . $email . "\n";
        $emailBody .= "Telepon: " . $phone . "\n";
        $emailBody .= "Subjek: " . $subject . "\n\n";
        $emailBody .= "Pesan:\n" . $message . "\n\n";
        $emailBody .= "Dikirim pada: " . date('Y-m-d H:i:s') . "\n";
        
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Uncomment the line below to enable email notifications
        // mail($adminEmail, $emailSubject, $emailBody, $headers);
        
    } else {
        $response['message'] = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Server error. Silakan coba lagi nanti.';
    error_log('Contact form error: ' . $e->getMessage());
}

echo json_encode($response);
?>
