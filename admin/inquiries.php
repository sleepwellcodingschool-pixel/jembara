<?php
require_once '../config/config.php';
requireLogin();

$message = '';
$error = '';

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $inquiryId = (int)$_GET['id'];
                $stmt = $db->prepare("DELETE FROM contact_inquiries WHERE id = ?");
                $stmt->bind_param("i", $inquiryId);
                if ($stmt->execute()) {
                    $message = 'Pesan berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus pesan.';
                }
            }
            break;
            
        case 'mark-read':
            if (isset($_GET['id'])) {
                $inquiryId = (int)$_GET['id'];
                $stmt = $db->prepare("UPDATE contact_inquiries SET status = 'in_progress' WHERE id = ? AND status = 'new'");
                $stmt->bind_param("i", $inquiryId);
                if ($stmt->execute()) {
                    $message = 'Pesan ditandai sebagai dibaca!';
                }
            }
            break;
    }
}

// Handle form submission for updating inquiry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_inquiry') {
        $inquiryId = (int)$_POST['inquiry_id'];
        $status = sanitize($_POST['status']);
        $adminNotes = sanitize($_POST['admin_notes']);
        
        $stmt = $db->prepare("UPDATE contact_inquiries SET status = ?, admin_notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("ssi", $status, $adminNotes, $inquiryId);
        
        if ($stmt->execute()) {
            $message = 'Status pesan berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui status pesan.';
        }
    }
}

// Get inquiries with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Filter
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$whereClause = '';
$params = [];
$types = '';

if ($statusFilter) {
    $whereClause = "WHERE status = ?";
    $params[] = $statusFilter;
    $types = 's';
}

$totalInquiries = $db->prepare("SELECT COUNT(*) as count FROM contact_inquiries $whereClause");
if ($params) {
    $totalInquiries->bind_param($types, ...$params);
}
$totalInquiries->execute();
$totalCount = $totalInquiries->get_result()->fetch_assoc()['count'];
$totalPages = ceil($totalCount / $perPage);

$inquiriesQuery = "SELECT * FROM contact_inquiries $whereClause ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $db->prepare($inquiriesQuery);
if ($params) {
    $params[] = $offset;
    $params[] = $perPage;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $offset, $perPage);
}
$stmt->execute();
$inquiries = $stmt->get_result();

// Get inquiry details if viewing
$viewInquiry = null;
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    $stmt = $db->prepare("SELECT * FROM contact_inquiries WHERE id = ?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $viewInquiry = $stmt->get_result()->fetch_assoc();
    
    // Mark as read if it was new
    if ($viewInquiry && $viewInquiry['status'] === 'new') {
        $db->prepare("UPDATE contact_inquiries SET status = 'in_progress' WHERE id = ?")->execute([$viewId]);
        $viewInquiry['status'] = 'in_progress';
    }
}

// Get statistics
$stats = [
    'new' => $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'new'")->fetch_assoc()['count'],
    'in_progress' => $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'in_progress'")->fetch_assoc()['count'],
    'resolved' => $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'resolved'")->fetch_assoc()['count'],
    'closed' => $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'closed'")->fetch_assoc()['count']
];

$pageTitle = 'Pesan Masuk';
include 'includes/header.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesan Masuk</h1>
        <p class="text-gray-600">Kelola pesan dan inquiry dari pengunjung website.</p>
    </div>
    
    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <div class="flex">
                <i class="fas fa-check-circle mr-3 mt-1"></i>
                <span><?php echo $message; ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-circle mr-3 mt-1"></i>
                <span><?php echo $error; ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-orange-500 text-white p-3 rounded-lg">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pesan Baru</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['new']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white p-3 rounded-lg">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Dalam Proses</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['in_progress']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-green-500 text-white p-3 rounded-lg">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['resolved']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-gray-500 text-white p-3 rounded-lg">
                    <i class="fas fa-archive text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ditutup</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['closed']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($viewInquiry): ?>
    <!-- Inquiry Detail Modal -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Detail Pesan</h2>
            <a href="inquiries.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                <i class="fas fa-times mr-2"></i>Tutup
            </a>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Message Details -->
            <div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <p class="text-gray-900 font-semibold"><?php echo htmlspecialchars($viewInquiry['name']); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($viewInquiry['email']); ?></p>
                    </div>
                    
                    <?php if ($viewInquiry['phone']): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telepon</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($viewInquiry['phone']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($viewInquiry['subject']): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subjek</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($viewInquiry['subject']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pesan</label>
                        <div class="bg-gray-50 p-4 rounded-lg mt-2">
                            <p class="text-gray-900 leading-relaxed"><?php echo nl2br(htmlspecialchars($viewInquiry['message'])); ?></p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Diterima</label>
                        <p class="text-gray-600"><?php echo formatDate($viewInquiry['created_at'], 'd M Y H:i'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Action Form -->
            <div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_inquiry">
                    <input type="hidden" name="inquiry_id" value="<?php echo $viewInquiry['id']; ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="new" <?php echo $viewInquiry['status'] === 'new' ? 'selected' : ''; ?>>Baru</option>
                            <option value="in_progress" <?php echo $viewInquiry['status'] === 'in_progress' ? 'selected' : ''; ?>>Dalam Proses</option>
                            <option value="resolved" <?php echo $viewInquiry['status'] === 'resolved' ? 'selected' : ''; ?>>Selesai</option>
                            <option value="closed" <?php echo $viewInquiry['status'] === 'closed' ? 'selected' : ''; ?>>Ditutup</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                        <textarea name="admin_notes" rows="6" 
                                  placeholder="Tambahkan catatan internal..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo htmlspecialchars($viewInquiry['admin_notes']); ?></textarea>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-save mr-2"></i>Update
                        </button>
                        
                        <a href="mailto:<?php echo $viewInquiry['email']; ?>?subject=Re: <?php echo urlencode($viewInquiry['subject'] ?: 'Inquiry'); ?>" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-reply mr-2"></i>Balas Email
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Inquiries List -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Semua Pesan</h2>
                
                <!-- Filter -->
                <form method="GET" class="flex items-center space-x-4">
                    <select name="status" onchange="this.form.submit()" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="">Semua Status</option>
                        <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>Baru</option>
                        <option value="in_progress" <?php echo $statusFilter === 'in_progress' ? 'selected' : ''; ?>>Dalam Proses</option>
                        <option value="resolved" <?php echo $statusFilter === 'resolved' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="closed" <?php echo $statusFilter === 'closed' ? 'selected' : ''; ?>>Ditutup</option>
                    </select>
                </form>
            </div>
        </div>
        
        <?php if ($inquiries && $inquiries->num_rows > 0): ?>
            <div class="divide-y divide-gray-200">
                <?php while ($inquiry = $inquiries->fetch_assoc()): ?>
                <div class="p-6 <?php echo $inquiry['status'] === 'new' ? 'bg-orange-50' : ''; ?>">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <?php echo htmlspecialchars($inquiry['name']); ?>
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php 
                                    echo $inquiry['status'] === 'new' ? 'bg-orange-100 text-orange-800' : 
                                         ($inquiry['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                          ($inquiry['status'] === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')); 
                                ?>">
                                    <?php 
                                    echo $inquiry['status'] === 'new' ? 'Baru' : 
                                         ($inquiry['status'] === 'in_progress' ? 'Proses' : 
                                          ($inquiry['status'] === 'resolved' ? 'Selesai' : 'Ditutup'));
                                    ?>
                                </span>
                                <?php if ($inquiry['status'] === 'new'): ?>
                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">NEW</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center space-x-6 text-sm text-gray-600 mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope mr-1"></i>
                                    <?php echo htmlspecialchars($inquiry['email']); ?>
                                </div>
                                <?php if ($inquiry['phone']): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-phone mr-1"></i>
                                    <?php echo htmlspecialchars($inquiry['phone']); ?>
                                </div>
                                <?php endif; ?>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?php echo timeAgo($inquiry['created_at']); ?>
                                </div>
                            </div>
                            
                            <?php if ($inquiry['subject']): ?>
                            <p class="text-gray-900 font-medium mb-2">
                                Subjek: <?php echo htmlspecialchars($inquiry['subject']); ?>
                            </p>
                            <?php endif; ?>
                            
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo truncateText($inquiry['message'], 200); ?>
                            </p>
                        </div>
                        
                        <div class="flex items-center space-x-2 ml-6">
                            <a href="inquiries.php?view=<?php echo $inquiry['id']; ?>" 
                               class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="mailto:<?php echo $inquiry['email']; ?>" 
                               class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded">
                                <i class="fas fa-reply"></i>
                            </a>
                            
                            <?php if ($inquiry['status'] === 'new'): ?>
                            <a href="inquiries.php?action=mark-read&id=<?php echo $inquiry['id']; ?>" 
                               class="text-yellow-600 hover:text-yellow-800 p-2 hover:bg-yellow-50 rounded"
                               title="Tandai Dibaca">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php endif; ?>
                            
                            <a href="inquiries.php?action=delete&id=<?php echo $inquiry['id']; ?>" 
                               class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="p-6 border-t border-gray-200">
                <div class="flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?>" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-left mr-2"></i>Previous
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?>" 
                           class="px-4 py-2 border <?php echo $i == $page ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50'; ?> rounded-lg transition-colors">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?>" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                            Next<i class="fas fa-chevron-right ml-2"></i>
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-inbox text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Pesan</h3>
                <p class="mb-6">
                    <?php if ($statusFilter): ?>
                        Tidak ada pesan dengan status "<?php echo $statusFilter; ?>".
                    <?php else: ?>
                        Belum ada pesan masuk dari pengunjung.
                    <?php endif; ?>
                </p>
                <?php if ($statusFilter): ?>
                <a href="inquiries.php" class="text-primary hover:text-secondary font-medium">
                    Lihat Semua Pesan
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
