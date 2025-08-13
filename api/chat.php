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
                case 'start_session':
                    $sessionId = generateChatSessionId();
                    $visitorName = sanitize($_POST['name'] ?? '');
                    $visitorWhatsapp = sanitize($_POST['whatsapp'] ?? '');
                    
                    $stmt = $db->prepare("INSERT INTO chat_sessions (session_id, visitor_name, visitor_whatsapp) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $sessionId, $visitorName, $visitorWhatsapp);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['data'] = ['session_id' => $sessionId];
                        $response['message'] = 'Chat session started successfully';
                    } else {
                        $response['message'] = 'Failed to start chat session';
                    }
                    break;
                    
                case 'send_message':
                    $sessionId = sanitize($_POST['session_id'] ?? '');
                    $senderType = sanitize($_POST['sender_type'] ?? 'visitor');
                    $senderName = sanitize($_POST['sender_name'] ?? '');
                    $senderWhatsapp = sanitize($_POST['sender_whatsapp'] ?? '');
                    $message = sanitize($_POST['message'] ?? '');
                    
                    if (empty($sessionId) || empty($message)) {
                        $response['message'] = 'Session ID and message are required';
                        break;
                    }
                    
                    $stmt = $db->prepare("INSERT INTO chat_messages (session_id, sender_type, sender_name, sender_whatsapp, message) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $sessionId, $senderType, $senderName, $senderWhatsapp, $message);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Message sent successfully';
                        
                        // Get the inserted message
                        $messageId = $db->lastInsertId();
                        $stmt = $db->prepare("SELECT * FROM chat_messages WHERE id = ?");
                        $stmt->bind_param("i", $messageId);
                        $stmt->execute();
                        $response['data'] = $stmt->get_result()->fetch_assoc();
                    } else {
                        $response['message'] = 'Failed to send message';
                    }
                    break;
                    
                case 'close_session':
                    $sessionId = sanitize($_POST['session_id'] ?? '');
                    
                    if (empty($sessionId)) {
                        $response['message'] = 'Session ID is required';
                        break;
                    }
                    
                    $stmt = $db->prepare("UPDATE chat_sessions SET status = 'closed' WHERE session_id = ?");
                    $stmt->bind_param("s", $sessionId);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Chat session closed successfully';
                    } else {
                        $response['message'] = 'Failed to close chat session';
                    }
                    break;
                    
                default:
                    $response['message'] = 'Invalid action';
            }
            break;
            
        case 'GET':
            $action = sanitize($_GET['action'] ?? '');
            
            switch ($action) {
                case 'get_messages':
                    $sessionId = sanitize($_GET['session_id'] ?? '');
                    $lastMessageId = (int)($_GET['last_message_id'] ?? 0);
                    
                    if (empty($sessionId)) {
                        $response['message'] = 'Session ID is required';
                        break;
                    }
                    
                    $sql = "SELECT * FROM chat_messages WHERE session_id = ?";
                    $params = [$sessionId];
                    $types = "s";
                    
                    if ($lastMessageId > 0) {
                        $sql .= " AND id > ?";
                        $params[] = $lastMessageId;
                        $types .= "i";
                    }
                    
                    $sql .= " ORDER BY created_at ASC";
                    
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    $messages = [];
                    while ($row = $result->fetch_assoc()) {
                        $messages[] = $row;
                    }
                    
                    $response['success'] = true;
                    $response['data'] = $messages;
                    break;
                    
                case 'get_sessions':
                    // Admin only - get all active chat sessions
                    $stmt = $db->prepare("SELECT cs.*, 
                                                (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.session_id) as message_count,
                                                (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.session_id AND cm.sender_type = 'visitor' AND cm.is_read = 0) as unread_count,
                                                (SELECT cm.message FROM chat_messages cm WHERE cm.session_id = cs.session_id ORDER BY cm.created_at DESC LIMIT 1) as last_message,
                                                (SELECT cm.created_at FROM chat_messages cm WHERE cm.session_id = cs.session_id ORDER BY cm.created_at DESC LIMIT 1) as last_message_time
                                         FROM chat_sessions cs 
                                         WHERE cs.status = 'active' 
                                         ORDER BY cs.updated_at DESC");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    $sessions = [];
                    while ($row = $result->fetch_assoc()) {
                        $sessions[] = $row;
                    }
                    
                    $response['success'] = true;
                    $response['data'] = $sessions;
                    break;
                    
                case 'mark_read':
                    $sessionId = sanitize($_GET['session_id'] ?? '');
                    
                    if (empty($sessionId)) {
                        $response['message'] = 'Session ID is required';
                        break;
                    }
                    
                    $stmt = $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender_type = 'visitor'");
                    $stmt->bind_param("s", $sessionId);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Messages marked as read';
                    } else {
                        $response['message'] = 'Failed to mark messages as read';
                    }
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
}

echo json_encode($response);
?>
