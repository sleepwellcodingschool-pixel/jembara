<?php
require_once '../config/config.php';
requireLogin();

// Get statistics
$totalPosts = $db->query("SELECT COUNT(*) as count FROM blog_posts")->fetch_assoc()['count'];
$publishedPosts = $db->query("SELECT COUNT(*) as count FROM blog_posts WHERE status = 'published'")->fetch_assoc()['count'];
$totalInquiries = $db->query("SELECT COUNT(*) as count FROM contact_inquiries")->fetch_assoc()['count'];
$newInquiries = $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'new'")->fetch_assoc()['count'];
$totalTestimonials = $db->query("SELECT COUNT(*) as count FROM testimonials WHERE is_active = 1")->fetch_assoc()['count'];
$totalSubscribers = $db->query("SELECT COUNT(*) as count FROM newsletter_subscribers WHERE status = 'active'")->fetch_assoc()['count'];

// Get recent activities
$recentPosts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5");
$recentInquiries = $db->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC LIMIT 5");

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="p-6">
    <!-- Welcome Message -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang, <?php echo $_SESSION['admin_name']; ?>!</h1>
        <p class="text-gray-600">Kelola website JEMBARA RISET DAN MEDIA dari panel admin ini.</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Blog Posts -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-primary text-white p-3 rounded-lg">
                    <i class="fas fa-newspaper text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Artikel</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $totalPosts; ?></p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    <?php echo $publishedPosts; ?> Dipublikasikan
                </div>
            </div>
        </div>
        
        <!-- Inquiries -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-secondary text-white p-3 rounded-lg">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pesan Masuk</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $totalInquiries; ?></p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-orange-600">
                    <i class="fas fa-clock mr-1"></i>
                    <?php echo $newInquiries; ?> Pesan Baru
                </div>
            </div>
        </div>
        
        <!-- Testimonials -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-accent text-gray-900 p-3 rounded-lg">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Testimoni</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $totalTestimonials; ?></p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-blue-600">
                    <i class="fas fa-eye mr-1"></i>
                    Aktif di website
                </div>
            </div>
        </div>
        
        <!-- Newsletter Subscribers -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-green-500 text-white p-3 rounded-lg">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Subscribers</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $totalSubscribers; ?></p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-green-600">
                    <i class="fas fa-mail-bulk mr-1"></i>
                    Newsletter aktif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Recent Blog Posts -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Artikel Terbaru</h2>
                    <a href="blog.php" class="text-primary hover:text-secondary text-sm font-medium">
                        Lihat Semua
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if ($recentPosts && $recentPosts->num_rows > 0): ?>
                    <?php while ($post = $recentPosts->fetch_assoc()): ?>
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-primary/10 text-primary rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate">
                                    <?php echo $post['title']; ?>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Status: <span class="<?php echo $post['status'] == 'published' ? 'text-green-600' : 'text-orange-600'; ?>">
                                        <?php echo ucfirst($post['status']); ?>
                                    </span>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    <?php echo timeAgo($post['created_at']); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="blog.php?edit=<?php echo $post['id']; ?>" class="text-gray-400 hover:text-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-newspaper text-gray-300 text-4xl mb-4"></i>
                        <p>Belum ada artikel</p>
                        <a href="blog.php" class="text-primary hover:text-secondary text-sm font-medium mt-2 inline-block">
                            Buat Artikel Pertama
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Inquiries -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Pesan Terbaru</h2>
                    <a href="inquiries.php" class="text-primary hover:text-secondary text-sm font-medium">
                        Lihat Semua
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if ($recentInquiries && $recentInquiries->num_rows > 0): ?>
                    <?php while ($inquiry = $recentInquiries->fetch_assoc()): ?>
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-secondary/10 text-secondary rounded-lg flex items-center justify-center">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <?php echo $inquiry['name']; ?>
                                </h3>
                                <p class="text-sm text-gray-600 truncate">
                                    <?php echo $inquiry['subject'] ?: 'No subject'; ?>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Status: <span class="<?php 
                                        echo $inquiry['status'] == 'new' ? 'text-orange-600' : 
                                             ($inquiry['status'] == 'resolved' ? 'text-green-600' : 'text-blue-600'); 
                                    ?>">
                                        <?php echo ucfirst($inquiry['status']); ?>
                                    </span>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    <?php echo timeAgo($inquiry['created_at']); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="inquiries.php?view=<?php echo $inquiry['id']; ?>" class="text-gray-400 hover:text-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-envelope text-gray-300 text-4xl mb-4"></i>
                        <p>Belum ada pesan masuk</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="blog.php?action=create" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                <i class="fas fa-plus text-2xl text-primary mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Buat Artikel</span>
            </a>
            <a href="content.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-secondary hover:bg-secondary/5 transition-colors">
                <i class="fas fa-edit text-2xl text-secondary mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Edit Konten</span>
            </a>
            <a href="settings.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-accent hover:bg-accent/5 transition-colors">
                <i class="fas fa-cog text-2xl text-accent mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Pengaturan</span>
            </a>
            <a href="inquiries.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors">
                <i class="fas fa-inbox text-2xl text-green-500 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Pesan Masuk</span>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
