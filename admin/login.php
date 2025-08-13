<?php
require_once '../config/config.php';

// Check if already logged in
if (isLoggedIn()) {
    redirect(ADMIN_URL . '/dashboard.php');
}

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $loginError = 'Username dan password wajib diisi.';
    } else {
        $stmt = $db->prepare("SELECT id, username, password, full_name FROM admin_users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['login_time'] = time();
                
                redirect(ADMIN_URL . '/dashboard.php');
            } else {
                $loginError = 'Username atau password salah.';
            }
        } else {
            $loginError = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?php echo getSetting('site_title', 'JEMBARA RISET DAN MEDIA'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo getSetting('primary_color', '#2563eb'); ?>',
                        secondary: '<?php echo getSetting('secondary_color', '#1e40af'); ?>',
                        accent: '<?php echo getSetting('accent_color', '#f59e0b'); ?>'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-primary to-secondary min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Login</h1>
            <p class="text-gray-600 mt-2"><?php echo getSetting('site_title', 'JEMBARA RISET DAN MEDIA'); ?></p>
        </div>
        
        <?php if ($loginError): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span><?php echo $loginError; ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username atau Email
                </label>
                <div class="relative">
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    <i class="fas fa-user absolute left-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password" required 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    <i class="fas fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 px-4 rounded-lg font-semibold transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Default login: <strong>admin</strong> / <strong>admin123</strong>
            </p>
            <a href="<?php echo SITE_URL; ?>/" class="text-primary hover:text-secondary text-sm font-medium mt-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i>
                Kembali ke Website
            </a>
        </div>
    </div>
</body>
</html>
