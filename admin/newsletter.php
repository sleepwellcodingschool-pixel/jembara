
<?php
require_once '../config/config.php';
requireLogin();

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';
$messageType = '';

if ($_POST) {
    switch ($_POST['action'] ?? '') {
        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = 'Subscriber berhasil dihapus';
                $messageType = 'success';
            } else {
                $message = 'Gagal menghapus subscriber';
                $messageType = 'error';
            }
            break;
            
        case 'toggle_status':
            $id = (int)$_POST['id'];
            $status = $_POST['status'] === 'active' ? 'unsubscribed' : 'active';
            
            $stmt = $db->prepare("UPDATE newsletter_subscribers SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            
            if ($stmt->execute()) {
                $message = 'Status subscriber berhasil diubah';
                $messageType = 'success';
            } else {
                $message = 'Gagal mengubah status subscriber';
                $messageType = 'error';
            }
            break;
    }
}

// Get subscribers with pagination
$page = (int)($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

$countQuery = "SELECT COUNT(*) as total FROM newsletter_subscribers";
$totalResult = $db->query($countQuery);
$total = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

$subscribers = $db->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC LIMIT $offset, $limit");

// Get statistics
$stats = [
    'total' => $total,
    'active' => $db->query("SELECT COUNT(*) as count FROM newsletter_subscribers WHERE status = 'active'")->fetch_assoc()['count'],
    'unsubscribed' => $db->query("SELECT COUNT(*) as count FROM newsletter_subscribers WHERE status = 'unsubscribed'")->fetch_assoc()['count']
];

$pageTitle = 'Newsletter';
include 'includes/header.php';
?>

<div class="p-6">
    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary/10 text-primary">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Subscribers</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Aktif</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['active']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Unsubscribed</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['unsubscribed']); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Subscribers Table -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Newsletter Subscribers</h1>
                    <p class="text-gray-600 mt-1">Kelola subscribers newsletter Anda</p>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-download mr-2"></i>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($subscribers && $subscribers->num_rows > 0): ?>
                        <?php while ($subscriber = $subscribers->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($subscriber['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($subscriber['name'] ?: '-'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $subscriber['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $subscriber['status'] === 'active' ? 'Aktif' : 'Unsubscribed'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d M Y H:i', strtotime($subscriber['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="toggleStatus(<?php echo $subscriber['id']; ?>, '<?php echo $subscriber['status']; ?>')" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-toggle-<?php echo $subscriber['status'] === 'active' ? 'on' : 'off'; ?>"></i>
                                </button>
                                <button onclick="deleteSubscriber(<?php echo $subscriber['id']; ?>)" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-envelope text-gray-300 text-4xl mb-4"></i>
                                <p>Belum ada subscribers</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $total); ?> dari <?php echo $total; ?> subscribers
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="px-3 py-2 border <?php echo $i === $page ? 'border-primary bg-primary text-white' : 'border-gray-300 hover:bg-gray-50'; ?> rounded-lg text-sm">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleStatus(id, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'unsubscribed' : 'active';
    const message = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';
    
    if (confirm(`Yakin ingin ${message} subscriber ini?`)) {
        const form = document.createElement('form');
        form.method = 'post';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="id" value="${id}">
            <input type="hidden" name="status" value="${currentStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSubscriber(id) {
    if (confirm('Yakin ingin menghapus subscriber ini?')) {
        const form = document.createElement('form');
        form.method = 'post';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
