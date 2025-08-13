<?php
$pageTitle = 'Blog & Berita';
include '../includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Build query
$whereConditions = ["bp.status = 'published'"];
$params = [];
$types = '';

if ($search) {
    $whereConditions[] = "(bp.title LIKE ? OR bp.content LIKE ? OR bp.excerpt LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    $types .= 'sss';
}

if ($category) {
    $whereConditions[] = "bp.tags LIKE ?";
    $params[] = "%$category%";
    $types .= 's';
}

$whereClause = implode(' AND ', $whereConditions);

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM blog_posts bp WHERE $whereClause";
$countStmt = $db->prepare($countSql);
if ($params) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalPosts = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Get blog posts
$sql = "SELECT bp.*, au.full_name as author_name 
        FROM blog_posts bp 
        LEFT JOIN admin_users au ON bp.author_id = au.id 
        WHERE $whereClause 
        ORDER BY bp.published_at DESC 
        LIMIT ?, ?";

$stmt = $db->prepare($sql);
if ($params) {
    $params[] = $offset;
    $params[] = $perPage;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $offset, $perPage);
}
$stmt->execute();
$blogPosts = $stmt->get_result();

// Get popular posts
$popularPosts = $db->query("SELECT bp.*, au.full_name as author_name 
                           FROM blog_posts bp 
                           LEFT JOIN admin_users au ON bp.author_id = au.id 
                           WHERE bp.status = 'published' 
                           ORDER BY bp.views DESC 
                           LIMIT 5");

// Get categories (from tags)
$categories = $db->query("SELECT DISTINCT tags FROM blog_posts WHERE status = 'published' AND tags IS NOT NULL AND tags != ''");
?>

<!-- Page Header -->
<section class="bg-gradient-to-r from-primary to-secondary py-20 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Blog & Berita</h1>
            <p class="text-xl max-w-3xl mx-auto leading-relaxed">
                Informasi terkini seputar dunia publikasi ilmiah dan tips untuk peneliti
            </p>
        </div>
    </div>
</section>

<!-- Search & Filter -->
<section class="py-12 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-center">
            <div class="relative flex-1 max-w-md">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari artikel..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </div>
            <select name="category" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">Semua Kategori</option>
                <?php if ($categories && $categories->num_rows > 0): ?>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <?php 
                        $tags = explode(',', $cat['tags']);
                        foreach ($tags as $tag): 
                            $tag = trim($tag);
                            if ($tag):
                        ?>
                        <option value="<?php echo htmlspecialchars($tag); ?>" <?php echo $category == $tag ? 'selected' : ''; ?>>
                            <?php echo ucfirst($tag); ?>
                        </option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <button type="submit" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>
</section>

<!-- Blog Content -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <?php if ($search || $category): ?>
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            Hasil Pencarian
                            <?php if ($search): ?>
                                untuk "<?php echo htmlspecialchars($search); ?>"
                            <?php endif; ?>
                            <?php if ($category): ?>
                                dalam kategori "<?php echo htmlspecialchars($category); ?>"
                            <?php endif; ?>
                        </h2>
                        <p class="text-gray-600">Ditemukan <?php echo $totalPosts; ?> artikel</p>
                    </div>
                <?php endif; ?>

                <!-- Blog Posts Grid -->
                <?php if ($blogPosts && $blogPosts->num_rows > 0): ?>
                    <div class="grid md:grid-cols-2 gap-8 mb-12">
                        <?php while ($post = $blogPosts->fetch_assoc()): ?>
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <?php if ($post['featured_image']): ?>
                            <div class="relative">
                                <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>" class="w-full h-48 object-cover">
                                <div class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded-full text-xs font-medium">
                                    <?php echo formatDate($post['published_at'], 'd M Y'); ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-r from-primary to-secondary flex items-center justify-center relative">
                                <i class="fas fa-newspaper text-white text-4xl"></i>
                                <div class="absolute top-4 left-4 bg-white text-primary px-3 py-1 rounded-full text-xs font-medium">
                                    <?php echo formatDate($post['published_at'], 'd M Y'); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="p-6">
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <i class="fas fa-user mr-2"></i>
                                    <?php echo $post['author_name']; ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-eye mr-2"></i>
                                    <?php echo $post['views']; ?> views
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                                    <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $post['slug']; ?>" class="hover:text-primary transition-colors">
                                        <?php echo $post['title']; ?>
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 mb-4 leading-relaxed">
                                    <?php echo truncateText($post['excerpt'] ?: strip_tags($post['content']), 120); ?>
                                </p>
                                
                                <?php if ($post['tags']): ?>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <?php 
                                    $tags = explode(',', $post['tags']);
                                    foreach (array_slice($tags, 0, 3) as $tag): 
                                        $tag = trim($tag);
                                    ?>
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                        <?php echo $tag; ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $post['slug']; ?>" class="inline-flex items-center text-primary hover:text-secondary font-semibold">
                                    Baca Selengkapnya
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </article>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-left mr-2"></i>Previous
                            </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                               class="px-4 py-2 border <?php echo $i == $page ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50'; ?> rounded-lg transition-colors">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-16">
                        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Tidak Ada Artikel</h3>
                        <p class="text-gray-600 mb-6">
                            <?php if ($search || $category): ?>
                                Tidak ditemukan artikel yang sesuai dengan kriteria pencarian Anda.
                            <?php else: ?>
                                Belum ada artikel yang dipublikasikan.
                            <?php endif; ?>
                        </p>
                        <?php if ($search || $category): ?>
                        <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                            Lihat Semua Artikel
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Popular Posts -->
                <?php if ($popularPosts && $popularPosts->num_rows > 0): ?>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Artikel Populer</h3>
                    <div class="space-y-4">
                        <?php while ($popular = $popularPosts->fetch_assoc()): ?>
                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <?php if ($popular['featured_image']): ?>
                                <img src="<?php echo $popular['featured_image']; ?>" alt="<?php echo $popular['title']; ?>" class="w-16 h-16 object-cover rounded-lg">
                                <?php else: ?>
                                <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center">
                                    <i class="fas fa-newspaper text-white"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 leading-tight mb-1">
                                    <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $popular['slug']; ?>" class="hover:text-primary transition-colors">
                                        <?php echo truncateText($popular['title'], 60); ?>
                                    </a>
                                </h4>
                                <p class="text-xs text-gray-500">
                                    <?php echo formatDate($popular['published_at'], 'd M Y'); ?> • <?php echo $popular['views']; ?> views
                                </p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Newsletter Signup -->
                <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white">
                    <h3 class="text-xl font-bold mb-4">Newsletter</h3>
                    <p class="text-blue-100 mb-4">Dapatkan update artikel terbaru langsung di email Anda.</p>
                    <form id="sidebar-newsletter-form" class="space-y-3">
                        <input type="email" placeholder="Email Anda" class="w-full px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent">
                        <button type="submit" class="w-full bg-accent hover:bg-yellow-500 text-gray-900 py-2 rounded-lg font-semibold transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>
                
                <!-- Categories -->
                <?php 
                $categories->data_seek(0); // Reset result pointer
                if ($categories && $categories->num_rows > 0): 
                ?>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Kategori</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php 
                        $allTags = [];
                        while ($cat = $categories->fetch_assoc()): 
                            $tags = explode(',', $cat['tags']);
                            foreach ($tags as $tag) {
                                $tag = trim($tag);
                                if ($tag && !in_array($tag, $allTags)) {
                                    $allTags[] = $tag;
                                }
                            }
                        endwhile;
                        
                        foreach ($allTags as $tag):
                        ?>
                        <a href="?category=<?php echo urlencode($tag); ?>" class="bg-gray-100 hover:bg-primary hover:text-white text-gray-600 px-3 py-1 rounded-full text-sm transition-colors">
                            <?php echo ucfirst($tag); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
