
<?php
require_once '../config/config.php';
requireLogin();

$message = '';
$error = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_file'])) {
    $uploadDir = '../uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $file = $_FILES['upload_file'];
    
    if ($file['error'] === 0) {
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Save to database
                $stmt = $db->prepare("INSERT INTO media_files (filename, original_name, file_path, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssii", $filename, $file['name'], $filepath, $file['type'], $file['size'], $_SESSION['admin_id']);
                
                if ($stmt->execute()) {
                    $message = 'File berhasil diupload!';
                } else {
                    $error = 'Gagal menyimpan informasi file ke database.';
                    unlink($filepath);
                }
            } else {
                $error = 'Gagal mengupload file.';
            }
        } else {
            $error = 'File tidak valid. Hanya file gambar dengan ukuran maksimal 5MB yang diizinkan.';
        }
    } else {
        $error = 'Error upload: ' . $file['error'];
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $fileId = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT file_path FROM media_files WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $file = $stmt->get_result()->fetch_assoc();
    
    if ($file) {
        if (unlink($file['file_path'])) {
            $stmt = $db->prepare("DELETE FROM media_files WHERE id = ?");
            $stmt->bind_param("i", $fileId);
            if ($stmt->execute()) {
                $message = 'File berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus data file dari database.';
            }
        } else {
            $error = 'Gagal menghapus file.';
        }
    }
}

// Handle website image updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_images') {
    $settings = [
        'logo_url' => sanitize($_POST['logo_url']),
        'hero_image' => sanitize($_POST['hero_image']),
        'about_image' => sanitize($_POST['about_image']),
        'services_bg' => sanitize($_POST['services_bg'])
    ];
    
    $success = true;
    foreach ($settings as $key => $value) {
        if (!updateSetting($key, $value)) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        $message = 'Gambar website berhasil diperbarui!';
    } else {
        $error = 'Gagal memperbarui gambar website.';
    }
}

// Get media files
$mediaFiles = $db->query("SELECT mf.*, au.full_name as uploaded_by_name 
                         FROM media_files mf 
                         LEFT JOIN admin_users au ON mf.uploaded_by = au.id 
                         ORDER BY mf.created_at DESC");

// Get current website images
$websiteImages = [
    'logo_url' => getSetting('logo_url', '/assets/images/logo.svg'),
    'hero_image' => getSetting('hero_image', ''),
    'about_image' => getSetting('about_image', ''),
    'services_bg' => getSetting('services_bg', '')
];

$pageTitle = 'Media & Gambar';
include 'includes/header.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Media & Gambar</h1>
        <p class="text-gray-600">Kelola semua gambar dan file media website.</p>
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
            <button class="tab-button active" data-tab="upload">Upload File</button>
            <button class="tab-button" data-tab="website-images">Gambar Website</button>
            <button class="tab-button" data-tab="gallery">Galeri Media</button>
        </nav>
    </div>
    
    <!-- Upload Tab -->
    <div id="upload" class="tab-content active">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Upload File Baru</h2>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary transition-colors" id="dropzone">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-2">Klik untuk memilih file atau drag & drop</p>
                    <p class="text-sm text-gray-500 mb-4">Hanya file gambar (JPEG, PNG, GIF, WebP, SVG) dengan ukuran maksimal 5MB</p>
                    <input type="file" name="upload_file" id="fileInput" accept="image/*" required class="hidden">
                    <button type="button" onclick="document.getElementById('fileInput').click()" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-plus mr-2"></i>Pilih File
                    </button>
                </div>
                
                <div id="filePreview" class="hidden">
                    <img id="previewImage" class="max-w-xs max-h-48 mx-auto rounded-lg shadow-md">
                    <p id="fileName" class="text-center text-gray-600 mt-2"></p>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-upload mr-2"></i>Upload File
                </button>
            </form>
        </div>
    </div>
    
    <!-- Website Images Tab -->
    <div id="website-images" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Gambar Website</h2>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_images">
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo Website</label>
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo $websiteImages['logo_url']; ?>" alt="Logo" class="w-16 h-16 object-contain border rounded">
                            <div class="flex-1">
                                <input type="text" name="logo_url" value="<?php echo $websiteImages['logo_url']; ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                <button type="button" onclick="openMediaPicker('logo_url')" class="mt-2 text-primary hover:text-secondary text-sm">
                                    <i class="fas fa-images mr-1"></i>Pilih dari Galeri
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Hero Section</label>
                        <div class="flex items-center space-x-4">
                            <?php if ($websiteImages['hero_image']): ?>
                            <img src="<?php echo $websiteImages['hero_image']; ?>" alt="Hero" class="w-16 h-16 object-cover border rounded">
                            <?php else: ?>
                            <div class="w-16 h-16 bg-gray-200 border rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <input type="text" name="hero_image" value="<?php echo $websiteImages['hero_image']; ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                <button type="button" onclick="openMediaPicker('hero_image')" class="mt-2 text-primary hover:text-secondary text-sm">
                                    <i class="fas fa-images mr-1"></i>Pilih dari Galeri
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar About</label>
                        <div class="flex items-center space-x-4">
                            <?php if ($websiteImages['about_image']): ?>
                            <img src="<?php echo $websiteImages['about_image']; ?>" alt="About" class="w-16 h-16 object-cover border rounded">
                            <?php else: ?>
                            <div class="w-16 h-16 bg-gray-200 border rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <input type="text" name="about_image" value="<?php echo $websiteImages['about_image']; ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                <button type="button" onclick="openMediaPicker('about_image')" class="mt-2 text-primary hover:text-secondary text-sm">
                                    <i class="fas fa-images mr-1"></i>Pilih dari Galeri
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Background Services</label>
                        <div class="flex items-center space-x-4">
                            <?php if ($websiteImages['services_bg']): ?>
                            <img src="<?php echo $websiteImages['services_bg']; ?>" alt="Services" class="w-16 h-16 object-cover border rounded">
                            <?php else: ?>
                            <div class="w-16 h-16 bg-gray-200 border rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <input type="text" name="services_bg" value="<?php echo $websiteImages['services_bg']; ?>" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                <button type="button" onclick="openMediaPicker('services_bg')" class="mt-2 text-primary hover:text-secondary text-sm">
                                    <i class="fas fa-images mr-1"></i>Pilih dari Galeri
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
    
    <!-- Gallery Tab -->
    <div id="gallery" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Galeri Media</h2>
            
            <?php if ($mediaFiles && $mediaFiles->num_rows > 0): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <?php while ($file = $mediaFiles->fetch_assoc()): ?>
                    <div class="relative group">
                        <img src="<?php echo str_replace('../', '/', $file['file_path']); ?>" alt="<?php echo $file['alt_text'] ?: $file['original_name']; ?>" 
                             class="w-full h-32 object-cover rounded-lg border cursor-pointer hover:opacity-75 transition-opacity"
                             onclick="selectImage('<?php echo str_replace('../', '/', $file['file_path']); ?>')">
                        
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <div class="flex space-x-2">
                                <button onclick="copyImageUrl('<?php echo str_replace('../', '/', $file['file_path']); ?>')" 
                                        class="bg-white text-gray-800 p-2 rounded-full hover:bg-gray-100" title="Copy URL">
                                    <i class="fas fa-copy text-sm"></i>
                                </button>
                                <a href="media.php?action=delete&id=<?php echo $file['id']; ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')"
                                   class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </a>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-600 mt-2 truncate"><?php echo $file['original_name']; ?></p>
                        <p class="text-xs text-gray-500"><?php echo formatFileSize($file['file_size']); ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada File</h3>
                    <p class="text-gray-600 mb-6">Upload file pertama Anda untuk memulai galeri.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Media Picker Modal -->
<div id="mediaPicker" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Pilih Gambar</h3>
            <button onclick="closeMediaPicker()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-4 md:grid-cols-6 gap-4" id="mediaPickerGrid">
            <?php 
            $mediaFiles->data_seek(0);
            while ($file = $mediaFiles->fetch_assoc()): 
            ?>
            <img src="<?php echo str_replace('../', '/', $file['file_path']); ?>" alt="<?php echo $file['original_name']; ?>" 
                 class="w-full h-20 object-cover rounded border cursor-pointer hover:opacity-75 transition-opacity"
                 onclick="selectImageFromPicker('<?php echo str_replace('../', '/', $file['file_path']); ?>')">
            <?php endwhile; ?>
        </div>
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
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // File input preview
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                fileName.textContent = file.name;
                filePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Drag and drop
    const dropzone = document.getElementById('dropzone');
    
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-primary', 'bg-primary-50');
    });
    
    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary-50');
    });
    
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
});

let currentInputField = null;

function openMediaPicker(inputName) {
    currentInputField = inputName;
    document.getElementById('mediaPicker').classList.remove('hidden');
}

function closeMediaPicker() {
    document.getElementById('mediaPicker').classList.add('hidden');
    currentInputField = null;
}

function selectImageFromPicker(imageUrl) {
    if (currentInputField) {
        document.querySelector(`input[name="${currentInputField}"]`).value = imageUrl;
        
        // Update preview image
        const container = document.querySelector(`input[name="${currentInputField}"]`).closest('.flex');
        const img = container.querySelector('img');
        if (img) {
            img.src = imageUrl;
        }
    }
    closeMediaPicker();
}

function selectImage(imageUrl) {
    navigator.clipboard.writeText(imageUrl).then(function() {
        showNotification('URL gambar berhasil disalin ke clipboard!');
    });
}

function copyImageUrl(imageUrl) {
    navigator.clipboard.writeText(imageUrl).then(function() {
        showNotification('URL gambar berhasil disalin!');
    });
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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

<?php
function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $factor = floor((strlen($size) - 1) / 3);
    return sprintf("%.1f", $size / pow(1024, $factor)) . ' ' . $units[$factor];
}

include 'includes/footer.php'; 
?>
