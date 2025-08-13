<?php
if (!isLoggedIn()) {
    redirect(ADMIN_URL . '/login.php');
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$siteTitle = getSetting('site_title', 'JEMBARA RISET DAN MEDIA');
$primaryColor = getSetting('primary_color', '#2563eb');
$secondaryColor = getSetting('secondary_color', '#1e40af');
$accentColor = getSetting('accent_color', '#f59e0b');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Admin Panel - <?php echo $siteTitle; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $primaryColor; ?>',
                        secondary: '<?php echo $secondaryColor; ?>',
                        accent: '<?php echo $accentColor; ?>'
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo SITE_URL; ?>/assets/images/logo.svg">

    <style>
        .sidebar-link {
            @apply flex items-center space-x-3 px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-primary transition-colors;
        }
        .sidebar-link.active {
            @apply bg-primary text-white;
        }
        .sidebar-link.active:hover {
            @apply bg-secondary text-white;
        }
        .sidebar-link i {
            @apply w-5 text-center;
        }
        /* Mobile sidebar overlay */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            display: none;
        }
        .sidebar-overlay.active {
            display: block;
        }
        @media (max-width: 768px) {
            #sidebar.mobile-open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Page Title -->
                    <div class="flex-1 md:flex-none">
                        <h1 class="text-xl font-semibold text-gray-900">
                            <?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?>
                        </h1>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Website Link -->
                        <a href="<?php echo SITE_URL; ?>/" target="_blank" 
                           class="text-gray-600 hover:text-primary p-2 rounded-lg hover:bg-gray-100 transition-colors"
                           title="Lihat Website">
                            <i class="fas fa-external-link-alt"></i>
                        </a>

                        <!-- Notifications -->
                        <?php
                        $newInquiries = $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'new'")->fetch_assoc()['count'];
                        ?>
                        <div class="relative">
                            <a href="inquiries.php" 
                               class="text-gray-600 hover:text-primary p-2 rounded-lg hover:bg-gray-100 transition-colors relative"
                               title="Pesan Masuk">
                                <i class="fas fa-bell"></i>
                                <?php if ($newInquiries > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    <?php echo $newInquiries > 9 ? '9+' : $newInquiries; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative">
                            <button id="user-menu-btn" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <span class="hidden md:block font-medium"><?php echo $_SESSION['admin_name']; ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="p-3 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900"><?php echo $_SESSION['admin_name']; ?></p>
                                    <p class="text-sm text-gray-600">@<?php echo $_SESSION['admin_username']; ?></p>
                                </div>
                                <div class="py-2">
                                    <a href="settings.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-3"></i>
                                        Pengaturan
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-external-link-alt mr-3"></i>
                                        Lihat Website
                                    </a>
                                    <div class="border-t border-gray-200 my-2"></div>
                                    <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">