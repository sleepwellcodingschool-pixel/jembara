
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $action = sanitize($_POST['action'] ?? '');
            
            switch ($action) {
                case 'login':
                    $username = sanitize($_POST['username'] ?? '');
                    $password = $_POST['password'] ?? '';
                    
                    if (empty($username) || empty($password)) {
                        $response['message'] = 'Username dan password wajib diisi';
                        break;
                    }
                    
                    $stmt = $db->prepare("SELECT id, username, email, password, full_name FROM admin_users WHERE username = ? OR email = ?");
                    $stmt->bind_param("ss", $username, $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($user = $result->fetch_assoc()) {
                        if (password_verify($password, $user['password'])) {
                            // Remove password from response
                            unset($user['password']);
                            
                            $response['success'] = true;
                            $response['data'] = $user;
                            $response['message'] = 'Login berhasil';
                            
                            // Log login activity
                            $stmt = $db->prepare("INSERT INTO admin_activity_log (admin_id, activity_type, activity_description, ip_address, user_agent) VALUES (?, 'login', 'Admin login to chat app', ?, ?)");
                            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                            $stmt->bind_param("iss", $user['id'], $ipAddress, $userAgent);
                            $stmt->execute();
                            
                        } else {
                            $response['message'] = 'Username atau password salah';
                            
                            // Log failed login attempt
                            error_log("Failed login attempt for username: $username from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                        }
                    } else {
                        $response['message'] = 'Username atau password salah';
                    }
                    break;
                    
                case 'verify_session':
                    $adminId = (int)($_POST['admin_id'] ?? 0);
                    
                    if ($adminId <= 0) {
                        $response['message'] = 'Invalid admin ID';
                        break;
                    }
                    
                    $stmt = $db->prepare("SELECT id, username, email, full_name FROM admin_users WHERE id = ?");
                    $stmt->bind_param("i", $adminId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($user = $result->fetch_assoc()) {
                        $response['success'] = true;
                        $response['data'] = $user;
                        $response['message'] = 'Session valid';
                    } else {
                        $response['message'] = 'Session invalid';
                    }
                    break;
                    
                case 'logout':
                    $adminId = (int)($_POST['admin_id'] ?? 0);
                    
                    if ($adminId > 0) {
                        // Log logout activity
                        $stmt = $db->prepare("INSERT INTO admin_activity_log (admin_id, activity_type, activity_description, ip_address, user_agent) VALUES (?, 'logout', 'Admin logout from chat app', ?, ?)");
                        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                        $stmt->bind_param("iss", $adminId, $ipAddress, $userAgent);
                        $stmt->execute();
                    }
                    
                    $response['success'] = true;
                    $response['message'] = 'Logout berhasil';
                    break;
                    
                default:
                    $response['message'] = 'Invalid action';
            }
            break;
            
        default:
            $response['message'] = 'Method not allowed';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
    error_log('Auth API error: ' . $e->getMessage());
}

echo json_encode($response);
?>
