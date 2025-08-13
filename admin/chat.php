
<?php
require_once '../config/config.php';
requireLogin();

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';
$messageType = '';

if ($_POST) {
    switch ($_POST['action'] ?? '') {
        case 'reply':
            $sessionId = sanitize($_POST['session_id']);
            $replyMessage = sanitize($_POST['message']);
            
            if (!empty($sessionId) && !empty($replyMessage)) {
                $stmt = $db->prepare("INSERT INTO chat_messages (session_id, sender_type, sender_name, sender_whatsapp, message) VALUES (?, 'admin', ?, ?, ?)");
                $stmt->bind_param("ssss", $sessionId, $_SESSION['admin_name'], '', $replyMessage);
                
                if ($stmt->execute()) {
                    $message = 'Pesan berhasil dikirim';
                    $messageType = 'success';
                    
                    // Mark visitor messages as read
                    $stmt = $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender_type = 'visitor'");
                    $stmt->bind_param("s", $sessionId);
                    $stmt->execute();
                } else {
                    $message = 'Gagal mengirim pesan';
                    $messageType = 'error';
                }
            } else {
                $message = 'Session ID dan pesan harus diisi';
                $messageType = 'error';
            }
            break;
            
        case 'close_session':
            $sessionId = sanitize($_POST['session_id']);
            
            if (!empty($sessionId)) {
                $stmt = $db->prepare("UPDATE chat_sessions SET status = 'closed', admin_id = ? WHERE session_id = ?");
                $stmt->bind_param("is", $_SESSION['admin_id'], $sessionId);
                
                if ($stmt->execute()) {
                    $message = 'Sesi chat ditutup';
                    $messageType = 'success';
                } else {
                    $message = 'Gagal menutup sesi chat';
                    $messageType = 'error';
                }
            }
            break;
    }
}

// Get chat sessions
$sessionsQuery = "
    SELECT cs.*, 
           (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.session_id) as message_count,
           (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.session_id AND cm.sender_type = 'visitor' AND cm.is_read = 0) as unread_count,
           (SELECT cm.message FROM chat_messages cm WHERE cm.session_id = cs.session_id ORDER BY cm.created_at DESC LIMIT 1) as last_message,
           (SELECT cm.created_at FROM chat_messages cm WHERE cm.session_id = cs.session_id ORDER BY cm.created_at DESC LIMIT 1) as last_message_time
    FROM chat_sessions cs 
    WHERE cs.status = 'active' 
    ORDER BY cs.updated_at DESC
";
$sessions = $db->query($sessionsQuery);

// Get selected session messages
$selectedSession = $_GET['session'] ?? '';
$messages = [];
if ($selectedSession) {
    $stmt = $db->prepare("SELECT * FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("s", $selectedSession);
    $stmt->execute();
    $messagesResult = $stmt->get_result();
    while ($row = $messagesResult->fetch_assoc()) {
        $messages[] = $row;
    }
    
    // Get session info
    $stmt = $db->prepare("SELECT * FROM chat_sessions WHERE session_id = ?");
    $stmt->bind_param("s", $selectedSession);
    $stmt->execute();
    $sessionInfo = $stmt->get_result()->fetch_assoc();
}

$pageTitle = 'Live Chat';
include 'includes/header.php';
?>

<!-- Chat Management Content -->
<div class="p-6">
    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-8rem)]">
        <!-- Chat Sessions List -->
        <div class="lg:w-1/3 bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Sesi Chat Aktif</h2>
                <p class="text-sm text-gray-600">Kelola percakapan dengan pengunjung</p>
            </div>
            
            <div class="overflow-y-auto max-h-96">
                <?php if ($sessions && $sessions->num_rows > 0): ?>
                    <?php while ($session = $sessions->fetch_assoc()): ?>
                    <a href="?session=<?php echo $session['session_id']; ?>" 
                       class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors <?php echo $selectedSession === $session['session_id'] ? 'bg-primary/5 border-primary/20' : ''; ?>">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-primary/10 text-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        <?php echo $session['visitor_name'] ?: 'Pengunjung'; ?>
                                    </h4>
                                    <?php if ($session['unread_count'] > 0): ?>
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        <?php echo $session['unread_count']; ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-sm text-gray-600 truncate">
                                    <?php echo $session['visitor_whatsapp'] ?: 'WhatsApp tidak tersedia'; ?>
                                </p>
                                <?php if ($session['last_message']): ?>
                                <p class="text-xs text-gray-500 truncate mt-1">
                                    <?php echo substr($session['last_message'], 0, 50) . '...'; ?>
                                </p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-400 mt-1">
                                    <?php echo $session['last_message_time'] ? timeAgo($session['last_message_time']) : timeAgo($session['created_at']); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-comments text-gray-300 text-4xl mb-4"></i>
                        <p>Belum ada sesi chat aktif</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Chat Messages -->
        <div class="flex-1 bg-white rounded-xl shadow-lg flex flex-col">
            <?php if ($selectedSession && isset($sessionInfo)): ?>
                <!-- Chat Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                <?php echo $sessionInfo['visitor_name'] ?: 'Pengunjung'; ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <?php echo $sessionInfo['visitor_whatsapp'] ?: 'WhatsApp tidak tersedia'; ?>
                            </p>
                        </div>
                        <form method="post" class="inline">
                            <input type="hidden" name="action" value="close_session">
                            <input type="hidden" name="session_id" value="<?php echo $selectedSession; ?>">
                            <button type="submit" 
                                    onclick="return confirm('Yakin ingin menutup sesi chat ini?')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                                <i class="fas fa-times mr-2"></i>
                                Tutup Sesi
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4" id="chat-messages-area">
                    <?php foreach ($messages as $msg): ?>
                    <div class="flex <?php echo $msg['sender_type'] === 'admin' ? 'justify-end' : 'justify-start'; ?>">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="<?php echo $msg['sender_type'] === 'admin' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-900'; ?> rounded-lg px-4 py-2">
                                <p class="text-sm"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 <?php echo $msg['sender_type'] === 'admin' ? 'text-right' : 'text-left'; ?>">
                                <?php echo $msg['sender_name'] ?: ($msg['sender_type'] === 'admin' ? 'Admin' : 'Pengunjung'); ?> • 
                                <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Reply Form -->
                <div class="p-6 border-t border-gray-200">
                    <form method="post" class="flex space-x-3">
                        <input type="hidden" name="action" value="reply">
                        <input type="hidden" name="session_id" value="<?php echo $selectedSession; ?>">
                        <div class="flex-1">
                            <textarea name="message" 
                                      rows="2" 
                                      placeholder="Ketik balasan Anda..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary resize-none"
                                      required></textarea>
                        </div>
                        <button type="submit" 
                                class="bg-primary hover:bg-secondary text-white px-6 py-2 rounded-lg font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- No Session Selected -->
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-600 mb-2">Pilih Sesi Chat</h3>
                        <p class="text-gray-500">Pilih sesi chat dari daftar di sebelah kiri untuk mulai berinteraksi</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto refresh chat sessions every 30 seconds
setInterval(function() {
    if (document.querySelector('.animate-pulse')) return; // Don't refresh if form is being submitted
    
    // Refresh the current page to get new messages
    const currentSession = '<?php echo $selectedSession; ?>';
    if (currentSession) {
        fetch(`?session=${currentSession}&ajax=1`)
            .then(response => response.text())
            .then(data => {
                // Update only the messages area if needed
                // This is a simple implementation - you could make it more sophisticated
            })
            .catch(console.error);
    }
}, 30000);

// Auto scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesArea = document.getElementById('chat-messages-area');
    if (messagesArea) {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
});
</script>

<?php include 'includes/footer.php'; ?>
