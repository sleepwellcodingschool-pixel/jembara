
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
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $response['message'] = 'Invalid JSON data';
                break;
            }
            
            $subscription = $input['subscription'] ?? null;
            $adminId = (int)($input['admin_id'] ?? 0);
            
            if (!$subscription || $adminId <= 0) {
                $response['message'] = 'Subscription data and admin ID required';
                break;
            }
            
            $endpoint = $subscription['endpoint'];
            $p256dhKey = $subscription['keys']['p256dh'] ?? '';
            $authKey = $subscription['keys']['auth'] ?? '';
            
            // Check if subscription already exists
            $stmt = $db->prepare("SELECT id FROM push_subscriptions WHERE admin_id = ? AND endpoint = ?");
            $stmt->bind_param("is", $adminId, $endpoint);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            
            if ($existing) {
                // Update existing subscription
                $stmt = $db->prepare("UPDATE push_subscriptions SET p256dh_key = ?, auth_key = ?, updated_at = NOW() WHERE id = ?");
                $stmt->bind_param("ssi", $p256dhKey, $authKey, $existing['id']);
                $success = $stmt->execute();
            } else {
                // Insert new subscription
                $stmt = $db->prepare("INSERT INTO push_subscriptions (admin_id, endpoint, p256dh_key, auth_key) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $adminId, $endpoint, $p256dhKey, $authKey);
                $success = $stmt->execute();
            }
            
            if ($success) {
                $response['success'] = true;
                $response['message'] = 'Push subscription saved successfully';
            } else {
                $response['message'] = 'Failed to save push subscription';
            }
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $adminId = (int)($input['admin_id'] ?? 0);
            $endpoint = $input['endpoint'] ?? '';
            
            if ($adminId <= 0 || empty($endpoint)) {
                $response['message'] = 'Admin ID and endpoint required';
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM push_subscriptions WHERE admin_id = ? AND endpoint = ?");
            $stmt->bind_param("is", $adminId, $endpoint);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Push subscription removed successfully';
            } else {
                $response['message'] = 'Failed to remove push subscription';
            }
            break;
            
        default:
            $response['message'] = 'Method not allowed';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
    error_log('Push subscription API error: ' . $e->getMessage());
}

echo json_encode($response);
?>
