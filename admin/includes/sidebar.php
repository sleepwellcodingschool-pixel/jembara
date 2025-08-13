<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$siteTitle = getSetting('site_title', 'JEMBARA RISET DAN MEDIA');

// Get notification counts
$newInquiries = $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'new'")->fetch_assoc()['count'];
$draftPosts = $db->query("SELECT COUNT(*) as count FROM blog_posts WHERE status = 'draft'")->fetch_assoc()['count'];
?>

<div id="sidebar" class="bg-white shadow-lg border-r border-gray-200 w-64 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-50">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.svg" alt="Logo" class="h-8 w-8">
            <div class="hidden lg:block">
                <h2 class="text-lg font-bold text-gray-900">Admin Panel</h2>
                <p class="text-xs text-gray-600">JEMBARA RISET</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="mt-8 px-4 space-y-2">
        <!-- Dashboard -->
        <a href="dashboard.php" class="sidebar-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <!-- Content Management -->
        <div class="pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Konten</p>
            
            <a href="content.php" class="sidebar-link <?php echo $currentPage === 'content' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Kelola Konten</span>
            </a>
            
            <a href="blog.php" class="sidebar-link <?php echo $currentPage === 'blog' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i>
                <span>Blog & Artikel</span>
                <?php if ($draftPosts > 0): ?>
                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full ml-auto">
                    <?php echo $draftPosts; ?>
                </span>
                <?php endif; ?>
            </a>
        </div>
        
        <!-- Communication -->
        <div class="pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Komunikasi</p>
            
            <a href="inquiries.php" class="sidebar-link <?php echo $currentPage === 'inquiries' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>Pesan Masuk</span>
                <?php if ($newInquiries > 0): ?>
                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full ml-auto animate-pulse">
                    <?php echo $newInquiries; ?>
                </span>
                <?php endif; ?>
            </a>
            
            <a href="chat.php" class="sidebar-link <?php echo $currentPage === 'chat' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i>
                <span>Live Chat</span>
            </a>
            
            <a href="newsletter.php" class="sidebar-link <?php echo $currentPage === 'newsletter' ? 'active' : ''; ?>">
                <i class="fas fa-mail-bulk"></i>
                <span>Newsletter</span>
            </a>
        </div>
        
        <!-- Website Management -->
        <div class="pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Website</p>
            
            <a href="settings.php" class="sidebar-link <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            
            <a href="media.php" class="sidebar-link <?php echo $currentPage === 'media' ? 'active' : ''; ?>">
                <i class="fas fa-images"></i>
                <span>Media</span>
            </a>
        </div>
        
        <!-- Quick Actions -->
        <div class="pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Aksi Cepat</p>
            
            <a href="blog.php?action=create" class="sidebar-link">
                <i class="fas fa-plus"></i>
                <span>Artikel Baru</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/" target="_blank" class="sidebar-link">
                <i class="fas fa-external-link-alt"></i>
                <span>Lihat Website</span>
            </a>
        </div>
    </nav>
    
    <!-- User Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center">
                <i class="fas fa-user"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    <?php echo $_SESSION['admin_name']; ?>
                </p>
                <p class="text-xs text-gray-600">
                    Admin
                </p>
            </div>
            <a href="logout.php" class="text-gray-400 hover:text-red-600 transition-colors" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>


