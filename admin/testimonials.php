
<?php
require_once '../config/config.php';
requireLogin();

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';
$messageType = '';

if ($_POST) {
    switch ($_POST['action'] ?? '') {
        case 'add':
            $clientName = sanitize($_POST['client_name']);
            $clientPosition = sanitize($_POST['client_position']);
            $clientCompany = sanitize($_POST['client_company']);
            $testimonial = sanitize($_POST['testimonial']);
            $rating = (int)($_POST['rating'] ?? 5);
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            
            if (!empty($clientName) && !empty($testimonial)) {
                $stmt = $db->prepare("INSERT INTO testimonials (client_name, client_position, client_company, testimonial, rating, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssii", $clientName, $clientPosition, $clientCompany, $testimonial, $rating, $sortOrder);
                
                if ($stmt->execute()) {
                    $message = 'Testimonial berhasil ditambahkan';
                    $messageType = 'success';
                } else {
                    $message = 'Gagal menambahkan testimonial';
                    $messageType = 'error';
                }
            } else {
                $message = 'Nama klien dan testimonial harus diisi';
                $messageType = 'error';
            }
            break;
            
        case 'edit':
            $id = (int)$_POST['id'];
            $clientName = sanitize($_POST['client_name']);
            $clientPosition = sanitize($_POST['client_position']);
            $clientCompany = sanitize($_POST['client_company']);
            $testimonial = sanitize($_POST['testimonial']);
            $rating = (int)($_POST['rating'] ?? 5);
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            if (!empty($clientName) && !empty($testimonial)) {
                $stmt = $db->prepare("UPDATE testimonials SET client_name = ?, client_position = ?, client_company = ?, testimonial = ?, rating = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param("ssssiiil", $clientName, $clientPosition, $clientCompany, $testimonial, $rating, $sortOrder, $isActive, $id);
                
                if ($stmt->execute()) {
                    $message = 'Testimonial berhasil diperbarui';
                    $messageType = 'success';
                } else {
                    $message = 'Gagal memperbarui testimonial';
                    $messageType = 'error';
                }
            } else {
                $message = 'Nama klien dan testimonial harus diisi';
                $messageType = 'error';
            }
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = 'Testimonial berhasil dihapus';
                $messageType = 'success';
            } else {
                $message = 'Gagal menghapus testimonial';
                $messageType = 'error';
            }
            break;
    }
}

// Get testimonials
$testimonials = $db->query("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC");

// Get testimonial for editing
$editTestimonial = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editTestimonial = $stmt->get_result()->fetch_assoc();
}

$pageTitle = 'Kelola Testimonial';
include 'includes/header.php';
?>

<div class="p-6">
    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Testimonial</h1>
                    <p class="text-gray-600 mt-1">Tambah, edit, atau hapus testimonial klien</p>
                </div>
                <button onclick="showAddModal()" class="bg-primary hover:bg-secondary text-white px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Testimonial
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Testimonial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($testimonials && $testimonials->num_rows > 0): ?>
                        <?php while ($testimonial = $testimonials->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($testimonial['client_name']); ?></div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($testimonial['client_position']); ?>
                                        <?php if ($testimonial['client_company']): ?>
                                            di <?php echo htmlspecialchars($testimonial['client_company']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900 line-clamp-3"><?php echo htmlspecialchars(substr($testimonial['testimonial'], 0, 100)) . '...'; ?></p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star text-<?php echo $i <= $testimonial['rating'] ? 'yellow' : 'gray'; ?>-400"></i>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $testimonial['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $testimonial['is_active'] ? 'Aktif' : 'Non-aktif'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="?action=edit&id=<?php echo $testimonial['id']; ?>" class="text-primary hover:text-secondary mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteTestimonial(<?php echo $testimonial['id']; ?>)" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-comments text-gray-300 text-4xl mb-4"></i>
                                <p>Belum ada testimonial</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="testimonialModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Testimonial</h2>
        </div>
        
        <form method="post" class="p-6">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="testimonialId" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Klien *</label>
                    <input type="text" name="client_name" id="clientName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Posisi</label>
                    <input type="text" name="client_position" id="clientPosition" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Perusahaan</label>
                    <input type="text" name="client_company" id="clientCompany" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select name="rating" id="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                        <option value="5">5 Bintang</option>
                        <option value="4">4 Bintang</option>
                        <option value="3">3 Bintang</option>
                        <option value="2">2 Bintang</option>
                        <option value="1">1 Bintang</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Testimonial *</label>
                <textarea name="testimonial" id="testimonial" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary resize-vertical" required></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="sortOrder" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                </div>
                
                <div id="statusField" class="hidden">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="isActive" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm font-medium text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-primary hover:bg-secondary text-white rounded-lg font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Testimonial';
    document.getElementById('formAction').value = 'add';
    document.getElementById('testimonialId').value = '';
    document.getElementById('statusField').classList.add('hidden');
    
    // Reset form
    document.getElementById('clientName').value = '';
    document.getElementById('clientPosition').value = '';
    document.getElementById('clientCompany').value = '';
    document.getElementById('testimonial').value = '';
    document.getElementById('rating').value = '5';
    document.getElementById('sortOrder').value = '0';
    
    document.getElementById('testimonialModal').classList.remove('hidden');
}

function hideModal() {
    document.getElementById('testimonialModal').classList.add('hidden');
}

function deleteTestimonial(id) {
    if (confirm('Yakin ingin menghapus testimonial ini?')) {
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

<?php if ($editTestimonial): ?>
// Fill edit form
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalTitle').textContent = 'Edit Testimonial';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('testimonialId').value = '<?php echo $editTestimonial['id']; ?>';
    document.getElementById('clientName').value = '<?php echo addslashes($editTestimonial['client_name']); ?>';
    document.getElementById('clientPosition').value = '<?php echo addslashes($editTestimonial['client_position']); ?>';
    document.getElementById('clientCompany').value = '<?php echo addslashes($editTestimonial['client_company']); ?>';
    document.getElementById('testimonial').value = '<?php echo addslashes($editTestimonial['testimonial']); ?>';
    document.getElementById('rating').value = '<?php echo $editTestimonial['rating']; ?>';
    document.getElementById('sortOrder').value = '<?php echo $editTestimonial['sort_order']; ?>';
    document.getElementById('isActive').checked = <?php echo $editTestimonial['is_active'] ? 'true' : 'false'; ?>;
    document.getElementById('statusField').classList.remove('hidden');
    
    document.getElementById('testimonialModal').classList.remove('hidden');
});
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
