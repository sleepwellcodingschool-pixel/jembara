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
    
    $email = sanitize($input['email'] ?? '');
    $name = sanitize($input['name'] ?? '');
    
    // Validation
    if (empty($email)) {
        $response['message'] = 'Email wajib diisi.';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Format email tidak valid.';
        echo json_encode($response);
        exit;
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    
    if ($existing) {
        if ($existing['status'] === 'active') {
            $response['message'] = 'Email ini sudah terdaftar dalam newsletter kami.';
        } else {
            // Reactivate subscription
            $stmt = $db->prepare("UPDATE newsletter_subscribers SET status = 'active', name = ?, updated_at = CURRENT_TIMESTAMP WHERE email = ?");
            $stmt->bind_param("ss", $name, $email);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Terima kasih! Subscription newsletter Anda telah diaktifkan kembali.';
            } else {
                $response['message'] = 'Gagal mengaktifkan subscription. Silakan coba lagi.';
            }
        }
    } else {
        // New subscription
        $stmt = $db->prepare("INSERT INTO newsletter_subscribers (email, name) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $name);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Terima kasih! Anda berhasil berlangganan newsletter kami.';
            
            // Optional: Send welcome email
            $siteTitle = getSetting('site_title', 'Website');
            $welcomeSubject = "Selamat Datang di Newsletter " . $siteTitle;
            $welcomeBody = "Halo" . ($name ? " " . $name : "") . ",\n\n";
            $welcomeBody .= "Terima kasih telah berlangganan newsletter " . $siteTitle . "!\n\n";
            $welcomeBody .= "Anda akan mendapatkan update terbaru tentang publikasi ilmiah, tips penelitian, dan informasi menarik lainnya.\n\n";
            $welcomeBody .= "Jika Anda ingin berhenti berlangganan, silakan klik link berikut:\n";
            $welcomeBody .= SITE_URL . "/unsubscribe.php?email=" . urlencode($email) . "\n\n";
            $welcomeBody .= "Salam,\n";
            $welcomeBody .= "Tim " . $siteTitle;
            
            $headers = "From: " . getSetting('contact_email', 'noreply@example.com') . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // Uncomment the line below to enable welcome emails
            // mail($email, $welcomeSubject, $welcomeBody, $headers);
            
        } else {
            $response['message'] = 'Gagal mendaftarkan email. Silakan coba lagi.';
        }
    }
    
} catch (Exception $e) {
    $response['message'] = 'Server error. Silakan coba lagi nanti.';
    error_log('Newsletter subscription error: ' . $e->getMessage());
}

echo json_encode($response);
?>
