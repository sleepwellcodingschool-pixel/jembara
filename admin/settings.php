<?php
require_once '../config/config.php';
requireLogin();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_general':
                $settings = [
                    'site_title' => sanitize($_POST['site_title']),
                    'site_tagline' => sanitize($_POST['site_tagline']),
                    'site_description' => sanitize($_POST['site_description']),
                    'company_name' => sanitize($_POST['company_name']),
                    'business_field' => sanitize($_POST['business_field']),
                    'hero_title' => sanitize($_POST['hero_title']),
                    'hero_subtitle' => sanitize($_POST['hero_subtitle'])
                ];
                
                $success = true;
                foreach ($settings as $key => $value) {
                    if (!updateSetting($key, $value)) {
                        $success = false;
                        break;
                    }
                }
                
                if ($success) {
                    $message = 'Pengaturan umum berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui pengaturan umum.';
                }
                break;
                
            case 'update_contact':
                $settings = [
                    'contact_email' => sanitize($_POST['contact_email']),
                    'contact_phone' => sanitize($_POST['contact_phone']),
                    'contact_address' => sanitize($_POST['contact_address'])
                ];
                
                $success = true;
                foreach ($settings as $key => $value) {
                    if (!updateSetting($key, $value)) {
                        $success = false;
                        break;
                    }
                }
                
                if ($success) {
                    $message = 'Informasi kontak berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui informasi kontak.';
                }
                break;
                
            case 'update_colors':
                $settings = [
                    'primary_color' => sanitize($_POST['primary_color']),
                    'secondary_color' => sanitize($_POST['secondary_color']),
                    'accent_color' => sanitize($_POST['accent_color'])
                ];
                
                $success = true;
                foreach ($settings as $key => $value) {
                    if (!updateSetting($key, $value)) {
                        $success = false;
                        break;
                    }
                }
                
                if ($success) {
                    $message = 'Pengaturan warna berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui pengaturan warna.';
                }
                break;
                
            case 'update_password':
                $currentPassword = $_POST['current_password'];
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
                
                if ($newPassword !== $confirmPassword) {
                    $error = 'Password baru dan konfirmasi password tidak cocok.';
                } elseif (strlen($newPassword) < 6) {
                    $error = 'Password baru minimal 6 karakter.';
                } else {
                    $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
                    $stmt->bind_param("i", $_SESSION['admin_id']);
                    $stmt->execute();
                    $user = $stmt->get_result()->fetch_assoc();
                    
                    if (password_verify($currentPassword, $user['password'])) {
                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                        $stmt->bind_param("si", $hashedPassword, $_SESSION['admin_id']);
                        
                        if ($stmt->execute()) {
                            $message = 'Password berhasil diperbarui!';
                        } else {
                            $error = 'Gagal memperbarui password.';
                        }
                    } else {
                        $error = 'Password saat ini salah.';
                    }
                }
                break;
                
            case 'add_admin':
                $username = sanitize($_POST['username']);
                $email = sanitize($_POST['email']);
                $fullName = sanitize($_POST['full_name']);
                $password = $_POST['password'];
                
                if (strlen($password) < 6) {
                    $error = 'Password minimal 6 karakter.';
                } else {
                    // Check if username or email already exists
                    $stmt = $db->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
                    $stmt->bind_param("ss", $username, $email);
                    $stmt->execute();
                    
                    if ($stmt->get_result()->num_rows > 0) {
                        $error = 'Username atau email sudah digunakan.';
                    } else {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("INSERT INTO admin_users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $fullName);
                        
                        if ($stmt->execute()) {
                            $message = 'Admin baru berhasil ditambahkan!';
                        } else {
                            $error = 'Gagal menambahkan admin baru.';
                        }
                    }
                }
                break;
        }
    }
}

// Get current settings
$generalSettings = [
    'site_title' => getSetting('site_title'),
    'site_tagline' => getSetting('site_tagline'),
    'site_description' => getSetting('site_description'),
    'company_name' => getSetting('company_name'),
    'business_field' => getSetting('business_field'),
    'hero_title' => getSetting('hero_title'),
    'hero_subtitle' => getSetting('hero_subtitle')
];

$contactSettings = [
    'contact_email' => getSetting('contact_email'),
    'contact_phone' => getSetting('contact_phone'),
    'contact_address' => getSetting('contact_address')
];

$colorSettings = [
    'primary_color' => getSetting('primary_color'),
    'secondary_color' => getSetting('secondary_color'),
    'accent_color' => getSetting('accent_color')
];

// Get admin users
$adminUsers = $db->query("SELECT id, username, email, full_name, created_at FROM admin_users ORDER BY created_at DESC");

$pageTitle = 'Pengaturan';
include 'includes/header.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pengaturan</h1>
        <p class="text-gray-600">Kelola pengaturan website dan akun admin.</p>
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
            <button class="tab-button active" data-tab="general">Umum</button>
            <button class="tab-button" data-tab="contact">Kontak</button>
            <button class="tab-button" data-tab="appearance">Tampilan</button>
            <button class="tab-button" data-tab="admins">Admin</button>
            <button class="tab-button" data-tab="security">Keamanan</button>
        </nav>
    </div>
    
    <!-- General Settings Tab -->
    <div id="general" class="tab-content active">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Pengaturan Umum</h2>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_general">
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Website</label>
                        <input type="text" name="site_title" value="<?php echo htmlspecialchars($generalSettings['site_title']); ?>" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan</label>
                        <input type="text" name="company_name" value="<?php echo htmlspecialchars($generalSettings['company_name']); ?>" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                    <input type="text" name="site_tagline" value="<?php echo htmlspecialchars($generalSettings['site_tagline']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bidang Usaha</label>
                    <input type="text" name="business_field" value="<?php echo htmlspecialchars($generalSettings['business_field']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Website</label>
                    <textarea name="site_description" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo htmlspecialchars($generalSettings['site_description']); ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Hero Section</label>
                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars($generalSettings['hero_title']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle Hero Section</label>
                    <textarea name="hero_subtitle" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo htmlspecialchars($generalSettings['hero_subtitle']); ?></textarea>
                </div>
                
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
    
    <!-- Contact Settings Tab -->
    <div id="contact" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Kontak</h2>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_contact">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="contact_email" value="<?php echo htmlspecialchars($contactSettings['contact_email']); ?>" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($contactSettings['contact_phone']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea name="contact_address" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo htmlspecialchars($contactSettings['contact_address']); ?></textarea>
                </div>
                
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Kontak
                </button>
            </form>
        </div>
    </div>
    
    <!-- Appearance Settings Tab -->
    <div id="appearance" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Pengaturan Tampilan</h2>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_colors">
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Primer</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="primary_color" value="<?php echo $colorSettings['primary_color']; ?>"
                                   class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                            <input type="text" value="<?php echo $colorSettings['primary_color']; ?>" readonly
                                   class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Sekunder</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="secondary_color" value="<?php echo $colorSettings['secondary_color']; ?>"
                                   class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                            <input type="text" value="<?php echo $colorSettings['secondary_color']; ?>" readonly
                                   class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Aksen</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="accent_color" value="<?php echo $colorSettings['accent_color']; ?>"
                                   class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                            <input type="text" value="<?php echo $colorSettings['accent_color']; ?>" readonly
                                   class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2">Preview</h3>
                    <div class="flex space-x-4">
                        <div class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color: <?php echo $colorSettings['primary_color']; ?>">
                            Primary
                        </div>
                        <div class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color: <?php echo $colorSettings['secondary_color']; ?>">
                            Secondary
                        </div>
                        <div class="px-4 py-2 rounded-lg text-gray-900 font-semibold" style="background-color: <?php echo $colorSettings['accent_color']; ?>">
                            Accent
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-palette mr-2"></i>Simpan Warna
                </button>
            </form>
        </div>
    </div>
    
    <!-- Admin Users Tab -->
    <div id="admins" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Kelola Admin</h2>
                <button onclick="showAddAdminModal()" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tambah Admin
                </button>
            </div>
            
            <div class="space-y-4">
                <?php if ($adminUsers && $adminUsers->num_rows > 0): ?>
                    <?php while ($admin = $adminUsers->fetch_assoc()): ?>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900"><?php echo $admin['full_name']; ?></h3>
                                <p class="text-gray-600">@<?php echo $admin['username']; ?> • <?php echo $admin['email']; ?></p>
                                <p class="text-sm text-gray-500">Bergabung: <?php echo formatDate($admin['created_at']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="<?php echo $admin['id'] == $_SESSION['admin_id'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?> px-2 py-1 rounded-full text-xs font-medium">
                                    <?php echo $admin['id'] == $_SESSION['admin_id'] ? 'Anda' : 'Admin'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Security Tab -->
    <div id="security" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Ganti Password</h2>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_password">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="new_password" required minlength="6"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required minlength="6"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-key mr-2"></i>Ganti Password
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addAdminModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Tambah Admin Baru</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add_admin">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                <input type="text" name="username" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="full_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="hideAddAdminModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
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
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Color input sync
    document.querySelectorAll('input[type="color"]').forEach(colorInput => {
        colorInput.addEventListener('change', function() {
            const textInput = this.nextElementSibling;
            textInput.value = this.value;
        });
    });
});

function showAddAdminModal() {
    document.getElementById('addAdminModal').classList.remove('hidden');
}

function hideAddAdminModal() {
    document.getElementById('addAdminModal').classList.add('hidden');
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
