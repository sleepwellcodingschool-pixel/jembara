<?php
require_once '../config/config.php';
requireLogin();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_content':
                $sectionKey = sanitize($_POST['section_key']);
                $title = sanitize($_POST['title']);
                $content = sanitize($_POST['content']);
                
                $stmt = $db->prepare("UPDATE content_sections SET title = ?, content = ? WHERE section_key = ?");
                $stmt->bind_param("sss", $title, $content, $sectionKey);
                
                if ($stmt->execute()) {
                    $message = 'Konten berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui konten.';
                }
                break;
                
            case 'update_service':
                $serviceId = (int)$_POST['service_id'];
                $title = sanitize($_POST['title']);
                $description = sanitize($_POST['description']);
                $detailedDescription = sanitize($_POST['detailed_description']);
                $icon = sanitize($_POST['icon']);
                $sortOrder = (int)$_POST['sort_order'];
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                
                $stmt = $db->prepare("UPDATE services SET title = ?, description = ?, detailed_description = ?, icon = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param("ssssiii", $title, $description, $detailedDescription, $icon, $sortOrder, $isActive, $serviceId);
                
                if ($stmt->execute()) {
                    $message = 'Layanan berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui layanan.';
                }
                break;
                
            case 'create_service':
                $title = sanitize($_POST['title']);
                $description = sanitize($_POST['description']);
                $detailedDescription = sanitize($_POST['detailed_description']);
                $icon = sanitize($_POST['icon']);
                $sortOrder = (int)$_POST['sort_order'];
                
                $stmt = $db->prepare("INSERT INTO services (title, description, detailed_description, icon, sort_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $title, $description, $detailedDescription, $icon, $sortOrder);
                
                if ($stmt->execute()) {
                    $message = 'Layanan berhasil ditambahkan!';
                } else {
                    $error = 'Gagal menambahkan layanan.';
                }
                break;
                
            case 'delete_service':
                $serviceId = (int)$_POST['service_id'];
                $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
                $stmt->bind_param("i", $serviceId);
                
                if ($stmt->execute()) {
                    $message = 'Layanan berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus layanan.';
                }
                break;
                
            case 'add_testimonial':
                $clientName = sanitize($_POST['client_name']);
                $clientPosition = sanitize($_POST['client_position']);
                $clientCompany = sanitize($_POST['client_company']);
                $testimonial = sanitize($_POST['testimonial']);
                $rating = (int)$_POST['rating'];
                $sortOrder = (int)$_POST['sort_order'];
                
                $stmt = $db->prepare("INSERT INTO testimonials (client_name, client_position, client_company, testimonial, rating, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssii", $clientName, $clientPosition, $clientCompany, $testimonial, $rating, $sortOrder);
                
                if ($stmt->execute()) {
                    $message = 'Testimoni berhasil ditambahkan!';
                } else {
                    $error = 'Gagal menambahkan testimoni.';
                }
                break;
                
            case 'update_testimonial':
                $testimonialId = (int)$_POST['testimonial_id'];
                $clientName = sanitize($_POST['client_name']);
                $clientPosition = sanitize($_POST['client_position']);
                $clientCompany = sanitize($_POST['client_company']);
                $testimonial = sanitize($_POST['testimonial']);
                $rating = (int)$_POST['rating'];
                $sortOrder = (int)$_POST['sort_order'];
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                
                $stmt = $db->prepare("UPDATE testimonials SET client_name = ?, client_position = ?, client_company = ?, testimonial = ?, rating = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param("ssssiiil", $clientName, $clientPosition, $clientCompany, $testimonial, $rating, $sortOrder, $isActive, $testimonialId);
                
                if ($stmt->execute()) {
                    $message = 'Testimoni berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui testimoni.';
                }
                break;
                
            case 'delete_testimonial':
                $testimonialId = (int)$_POST['testimonial_id'];
                $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
                $stmt->bind_param("i", $testimonialId);
                
                if ($stmt->execute()) {
                    $message = 'Testimoni berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus testimoni.';
                }
                break;
        }
    }
}

// Get content sections
$contentSections = $db->query("SELECT * FROM content_sections ORDER BY section_key");

// Get services
$services = $db->query("SELECT * FROM services ORDER BY sort_order ASC");

// Get testimonials
$testimonials = $db->query("SELECT * FROM testimonials ORDER BY sort_order ASC");

$pageTitle = 'Kelola Konten';
include 'includes/header.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Konten</h1>
        <p class="text-gray-600">Kelola semua konten yang tampil di website utama.</p>
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
    
    <!-- Tabs Navigation -->
    <div class="mb-6">
        <nav class="flex space-x-8">
            <button class="tab-button active" data-tab="content-sections">Konten Utama</button>
            <button class="tab-button" data-tab="services">Layanan</button>
            <button class="tab-button" data-tab="testimonials">Testimoni</button>
        </nav>
    </div>
    
    <!-- Content Sections Tab -->
    <div id="content-sections" class="tab-content active">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Konten Utama Website</h2>
            
            <?php if ($contentSections && $contentSections->num_rows > 0): ?>
                <?php while ($section = $contentSections->fetch_assoc()): ?>
                <div class="border border-gray-200 rounded-lg p-6 mb-6">
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_content">
                        <input type="hidden" name="section_key" value="<?php echo $section['section_key']; ?>">
                        
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <?php echo ucwords(str_replace('_', ' ', $section['section_key'])); ?>
                            </h3>
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm">
                                <?php echo $section['section_key']; ?>
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($section['title']); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                            <textarea name="content" rows="6" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo htmlspecialchars($section['content']); ?></textarea>
                        </div>
                        
                        <button type="submit" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-save mr-2"></i>Update Konten
                        </button>
                    </form>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Services Tab -->
    <div id="services" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Kelola Layanan</h2>
                <button onclick="showCreateServiceModal()" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tambah Layanan
                </button>
            </div>
            
            <div class="space-y-6">
                <?php if ($services && $services->num_rows > 0): ?>
                    <?php while ($service = $services->fetch_assoc()): ?>
                    <div class="border border-gray-200 rounded-lg p-6">
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="update_service">
                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                            
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo $service['title']; ?></h3>
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-primary hover:bg-secondary text-white px-3 py-1 rounded text-sm">Update</button>
                                    <button type="button" onclick="deleteService(<?php echo $service['id']; ?>)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Hapus</button>
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Layanan</label>
                                    <input type="text" name="title" value="<?php echo htmlspecialchars($service['title']); ?>" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Icon (FontAwesome)</label>
                                    <input type="text" name="icon" value="<?php echo htmlspecialchars($service['icon']); ?>" 
                                           placeholder="fas fa-comments"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                                <textarea name="description" rows="3" required
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"><?php echo htmlspecialchars($service['description']); ?></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Detail</label>
                                <textarea name="detailed_description" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"><?php echo htmlspecialchars($service['detailed_description']); ?></textarea>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                                    <input type="number" name="sort_order" value="<?php echo $service['sort_order']; ?>" min="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="active_<?php echo $service['id']; ?>" 
                                           <?php echo $service['is_active'] ? 'checked' : ''; ?>
                                           class="mr-2">
                                    <label for="active_<?php echo $service['id']; ?>" class="text-sm font-medium text-gray-700">
                                        Aktif di website
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-cogs text-4xl mb-4"></i>
                        <p>Belum ada layanan yang ditambahkan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Testimonials Tab -->
    <div id="testimonials" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Kelola Testimoni</h2>
                <button onclick="showCreateTestimonialModal()" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tambah Testimoni
                </button>
            </div>
            
            <div class="space-y-6">
                <?php if ($testimonials && $testimonials->num_rows > 0): ?>
                    <?php while ($testimonial = $testimonials->fetch_assoc()): ?>
                    <div class="border border-gray-200 rounded-lg p-6">
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="update_testimonial">
                            <input type="hidden" name="testimonial_id" value="<?php echo $testimonial['id']; ?>">
                            
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo $testimonial['client_name']; ?></h3>
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-primary hover:bg-secondary text-white px-3 py-1 rounded text-sm">Update</button>
                                    <button type="button" onclick="deleteTestimonial(<?php echo $testimonial['id']; ?>)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Hapus</button>
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Klien</label>
                                    <input type="text" name="client_name" value="<?php echo htmlspecialchars($testimonial['client_name']); ?>" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                                    <input type="text" name="client_position" value="<?php echo htmlspecialchars($testimonial['client_position']); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Perusahaan/Instansi</label>
                                    <input type="text" name="client_company" value="<?php echo htmlspecialchars($testimonial['client_company']); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Testimoni</label>
                                <textarea name="testimonial" rows="4" required
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"><?php echo htmlspecialchars($testimonial['testimonial']); ?></textarea>
                            </div>
                            
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating (1-5)</label>
                                    <select name="rating"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $testimonial['rating'] == $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?>
                                        </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                                    <input type="number" name="sort_order" value="<?php echo $testimonial['sort_order']; ?>" min="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="testimonial_active_<?php echo $testimonial['id']; ?>" 
                                           <?php echo $testimonial['is_active'] ? 'checked' : ''; ?>
                                           class="mr-2">
                                    <label for="testimonial_active_<?php echo $testimonial['id']; ?>" class="text-sm font-medium text-gray-700">
                                        Tampil di website
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-star text-4xl mb-4"></i>
                        <p>Belum ada testimoni yang ditambahkan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Service Modal -->
<div id="createServiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Tambah Layanan Baru</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create_service">
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Layanan</label>
                    <input type="text" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Icon (FontAwesome)</label>
                    <input type="text" name="icon" placeholder="fas fa-comments"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                <textarea name="description" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Detail</label>
                <textarea name="detailed_description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                <input type="number" name="sort_order" value="0" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="hideCreateServiceModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create Testimonial Modal -->
<div id="createTestimonialModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Tambah Testimoni Baru</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add_testimonial">
            
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Klien</label>
                    <input type="text" name="client_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <input type="text" name="client_position"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Perusahaan/Instansi</label>
                    <input type="text" name="client_company"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Testimoni</label>
                <textarea name="testimonial" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating (1-5)</label>
                    <select name="rating"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="0" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="hideCreateTestimonialModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and target content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
});

function showCreateServiceModal() {
    document.getElementById('createServiceModal').classList.remove('hidden');
}

function hideCreateServiceModal() {
    document.getElementById('createServiceModal').classList.add('hidden');
}

function showCreateTestimonialModal() {
    document.getElementById('createTestimonialModal').classList.remove('hidden');
}

function hideCreateTestimonialModal() {
    document.getElementById('createTestimonialModal').classList.add('hidden');
}

function deleteService(serviceId) {
    if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_service">
            <input type="hidden" name="service_id" value="${serviceId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteTestimonial(testimonialId) {
    if (confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_testimonial">
            <input type="hidden" name="testimonial_id" value="${testimonialId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.tab-button {
    @apply px-4 py-2 font-semibold text-gray-600 border-b-2 border-transparent hover:text-primary hover:border-primary transition-colors;
}

.tab-button.active {
    @apply text-primary border-primary;
}

.tab-content {
    @apply hidden;
}

.tab-content.active {
    @apply block;
}
</style>

<?php include 'includes/footer.php'; ?>
<?php
require_once '../config/config.php';
requireLogin();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_content') {
        $sectionKey = sanitize($_POST['section_key']);
        $title = sanitize($_POST['title']);
        $content = sanitize($_POST['content']);
        $imageUrl = sanitize($_POST['image_url']);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        $stmt = $db->prepare("UPDATE content_sections SET title = ?, content = ?, image_url = ?, is_active = ? WHERE section_key = ?");
        $stmt->bind_param("sssis", $title, $content, $imageUrl, $isActive, $sectionKey);
        
        if ($stmt->execute()) {
            $message = 'Konten berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui konten.';
        }
    }
}

// Get content sections
$contentSections = $db->query("SELECT * FROM content_sections ORDER BY sort_order ASC");

$pageTitle = 'Kelola Konten';
include 'includes/header.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Konten & Halaman</h1>
        <p class="text-gray-600">Kelola konten utama website seperti About, Vision, Mission, dan lainnya.</p>
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
    
    <div class="space-y-8">
        <?php while ($section = $contentSections->fetch_assoc()): ?>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_content">
                <input type="hidden" name="section_key" value="<?php echo $section['section_key']; ?>">
                
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-2">
                            <?php echo ucwords(str_replace('_', ' ', $section['section_key'])); ?>
                        </h2>
                        <p class="text-sm text-gray-600">Section Key: <?php echo $section['section_key']; ?></p>
                    </div>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" <?php echo $section['is_active'] ? 'checked' : ''; ?> 
                               class="rounded border-gray-300 text-primary focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($section['title']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                    <textarea name="content" rows="8" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo htmlspecialchars($section['content']); ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL Gambar</label>
                    <div class="flex space-x-4">
                        <input type="url" name="image_url" value="<?php echo htmlspecialchars($section['image_url']); ?>"
                               placeholder="https://example.com/image.jpg"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <button type="button" onclick="openImagePicker(this)" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-images mr-2"></i>Pilih Gambar
                        </button>
                    </div>
                    
                    <?php if ($section['image_url']): ?>
                    <div class="mt-4">
                        <img src="<?php echo $section['image_url']; ?>" alt="Preview" class="w-32 h-24 object-cover rounded border">
                    </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Image Picker Modal -->
<div id="imagePicker" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Pilih Gambar</h3>
            <button onclick="closeImagePicker()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-4 md:grid-cols-6 gap-4" id="imagePickerGrid">
            <!-- Images will be loaded here -->
        </div>
    </div>
</div>

<script>
let currentImageInput = null;

function openImagePicker(button) {
    currentImageInput = button.previousElementSibling;
    document.getElementById('imagePicker').classList.remove('hidden');
    loadImages();
}

function closeImagePicker() {
    document.getElementById('imagePicker').classList.add('hidden');
    currentImageInput = null;
}

function loadImages() {
    fetch('api/get-media.php')
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('imagePickerGrid');
            grid.innerHTML = '';
            
            data.forEach(image => {
                const img = document.createElement('img');
                img.src = image.file_path;
                img.alt = image.original_name;
                img.className = 'w-full h-20 object-cover rounded border cursor-pointer hover:opacity-75 transition-opacity';
                img.onclick = () => selectImage(image.file_path);
                grid.appendChild(img);
            });
        });
}

function selectImage(imageUrl) {
    if (currentImageInput) {
        currentImageInput.value = imageUrl;
    }
    closeImagePicker();
}
</script>

<?php include 'includes/footer.php'; ?>
