<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$siteTitle = getSetting('site_title', 'JEMBARA RISET DAN MEDIA');
$siteTagline = getSetting('site_tagline', 'AKU, KAMU DAN DIA, HIDUPKAN PENELITIAN DI INDONESIA');
$primaryColor = getSetting('primary_color', '#2563eb');
$secondaryColor = getSetting('secondary_color', '#1e40af');
$accentColor = getSetting('accent_color', '#f59e0b');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getPageTitle(); ?></title>
    <meta name="description" content="<?php echo getSetting('site_description', 'Layanan Publikasi Ilmiah & Akses Penerbitan Jurnal'); ?>">
    <meta name="keywords" content="publikasi ilmiah, jurnal, penelitian, akademik, Indonesia">
    
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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo SITE_URL; ?>/assets/images/logo.svg">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.svg" alt="<?php echo $siteTitle; ?>" class="h-10 w-10">
                    <div class="hidden md:block">
                        <h1 class="text-xl font-bold text-gray-800"><?php echo $siteTitle; ?></h1>
                        <p class="text-xs text-gray-600"><?php echo getSetting('business_field', 'Layanan Publikasi Ilmiah'); ?></p>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo SITE_URL; ?>/" class="<?php echo $currentPage == 'index' ? 'text-primary' : 'text-gray-700 hover:text-primary'; ?> px-3 py-2 text-sm font-medium transition-colors">
                        Beranda
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/about.php" class="<?php echo $currentPage == 'about' ? 'text-primary' : 'text-gray-700 hover:text-primary'; ?> px-3 py-2 text-sm font-medium transition-colors">
                        Tentang Kami
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/services.php" class="<?php echo $currentPage == 'services' ? 'text-primary' : 'text-gray-700 hover:text-primary'; ?> px-3 py-2 text-sm font-medium transition-colors">
                        Layanan
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="<?php echo $currentPage == 'blog' || $currentPage == 'blog-detail' ? 'text-primary' : 'text-gray-700 hover:text-primary'; ?> px-3 py-2 text-sm font-medium transition-colors">
                        Blog
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="<?php echo $currentPage == 'contact' ? 'text-primary' : 'text-gray-700 hover:text-primary'; ?> px-3 py-2 text-sm font-medium transition-colors">
                        Kontak
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-secondary transition-colors">
                        Konsultasi Gratis
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700 hover:text-primary focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="<?php echo SITE_URL; ?>/" class="<?php echo $currentPage == 'index' ? 'text-primary bg-gray-50' : 'text-gray-700'; ?> block px-3 py-2 text-base font-medium hover:text-primary hover:bg-gray-50">
                        Beranda
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/about.php" class="<?php echo $currentPage == 'about' ? 'text-primary bg-gray-50' : 'text-gray-700'; ?> block px-3 py-2 text-base font-medium hover:text-primary hover:bg-gray-50">
                        Tentang Kami
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/services.php" class="<?php echo $currentPage == 'services' ? 'text-primary bg-gray-50' : 'text-gray-700'; ?> block px-3 py-2 text-base font-medium hover:text-primary hover:bg-gray-50">
                        Layanan
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="<?php echo $currentPage == 'blog' || $currentPage == 'blog-detail' ? 'text-primary bg-gray-50' : 'text-gray-700'; ?> block px-3 py-2 text-base font-medium hover:text-primary hover:bg-gray-50">
                        Blog
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="<?php echo $currentPage == 'contact' ? 'text-primary bg-gray-50' : 'text-gray-700'; ?> block px-3 py-2 text-base font-medium hover:text-primary hover:bg-gray-50">
                        Kontak
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="bg-primary text-white block mx-3 my-2 px-4 py-2 rounded-lg text-base font-medium text-center hover:bg-secondary transition-colors">
                        Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Add top margin to account for fixed navbar -->
    <div class="pt-16">
