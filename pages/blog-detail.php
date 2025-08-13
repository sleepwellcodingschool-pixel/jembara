<?php
if (!isset($_GET['slug'])) {
    header('HTTP/1.0 404 Not Found');
    include '../includes/header.php';
    echo '<div class="min-h-screen flex items-center justify-center"><h1 class="text-4xl font-bold text-gray-900">404 - Artikel Tidak Ditemukan</h1></div>';
    include '../includes/footer.php';
    exit;
}

$slug = sanitize($_GET['slug']);
$post = getBlogPost($slug);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    include '../includes/header.php';
    echo '<div class="min-h-screen flex items-center justify-center"><h1 class="text-4xl font-bold text-gray-900">404 - Artikel Tidak Ditemukan</h1></div>';
    include '../includes/footer.php';
    exit;
}

// Increment view count
incrementBlogViews($post['id']);

// Get related posts
$relatedPosts = $db->query("SELECT bp.*, au.full_name as author_name 
                           FROM blog_posts bp 
                           LEFT JOIN admin_users au ON bp.author_id = au.id 
                           WHERE bp.status = 'published' 
                           AND bp.id != {$post['id']} 
                           ORDER BY RAND() 
                           LIMIT 3");

$pageTitle = $post['title'];
include '../includes/header.php';
?>

<!-- Article Header -->
<article class="bg-white">
    <!-- Hero Section -->
    <div class="relative <?php echo $post['featured_image'] ? 'bg-gray-900' : 'bg-gradient-to-r from-primary to-secondary'; ?> text-white">
        <?php if ($post['featured_image']): ?>
        <div class="absolute inset-0">
            <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>" class="w-full h-full object-cover opacity-50">
        </div>
        <?php endif; ?>
        
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <ol class="flex items-center justify-center space-x-2 text-sm">
                        <li><a href="<?php echo SITE_URL; ?>/" class="text-blue-200 hover:text-white transition-colors">Beranda</a></li>
                        <li><i class="fas fa-chevron-right text-blue-300 mx-2"></i></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/blog.php" class="text-blue-200 hover:text-white transition-colors">Blog</a></li>
                        <li><i class="fas fa-chevron-right text-blue-300 mx-2"></i></li>
                        <li class="text-blue-100">Artikel</li>
                    </ol>
                </nav>
                
                <!-- Article Meta -->
                <div class="flex items-center justify-center space-x-6 text-sm text-blue-200 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-calendar mr-2"></i>
                        <?php echo formatDate($post['published_at'], 'd F Y'); ?>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo $post['author_name']; ?>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-eye mr-2"></i>
                        <?php echo $post['views'] + 1; ?> views
                    </div>
                </div>
                
                <!-- Title -->
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-6">
                    <?php echo $post['title']; ?>
                </h1>
                
                <!-- Excerpt -->
                <?php if ($post['excerpt']): ?>
                <p class="text-xl text-blue-100 leading-relaxed max-w-3xl mx-auto">
                    <?php echo $post['excerpt']; ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Article Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="prose prose-lg prose-blue max-w-none">
            <?php echo nl2br($post['content']); ?>
        </div>
        
        <!-- Tags -->
        <?php if ($post['tags']): ?>
        <div class="mt-12 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags:</h3>
            <div class="flex flex-wrap gap-2">
                <?php 
                $tags = explode(',', $post['tags']);
                foreach ($tags as $tag): 
                    $tag = trim($tag);
                ?>
                <a href="<?php echo SITE_URL; ?>/pages/blog.php?category=<?php echo urlencode($tag); ?>" class="bg-gray-100 hover:bg-primary hover:text-white text-gray-600 px-3 py-1 rounded-full text-sm transition-colors">
                    <?php echo $tag; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Share Buttons -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bagikan Artikel:</h3>
            <div class="flex space-x-4">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/pages/blog-detail.php?slug=' . $post['slug']); ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fab fa-facebook-f mr-2"></i>Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/pages/blog-detail.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fab fa-twitter mr-2"></i>Twitter
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL . '/pages/blog-detail.php?slug=' . $post['slug']); ?>" target="_blank" class="bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fab fa-linkedin-in mr-2"></i>LinkedIn
                </a>
                <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - ' . SITE_URL . '/pages/blog-detail.php?slug=' . $post['slug']); ?>" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                </a>
            </div>
        </div>
        
        <!-- Author Info -->
        <div class="mt-12 p-6 bg-gray-50 rounded-xl">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900"><?php echo $post['author_name']; ?></h4>
                    <p class="text-gray-600">Penulis di JEMBARA RISET DAN MEDIA</p>
                </div>
            </div>
        </div>
    </div>
</article>

<!-- Related Articles -->
<?php if ($relatedPosts && $relatedPosts->num_rows > 0): ?>
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Artikel Terkait</h2>
            <p class="text-lg text-gray-600">Artikel lain yang mungkin Anda minati</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php while ($related = $relatedPosts->fetch_assoc()): ?>
            <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <?php if ($related['featured_image']): ?>
                <img src="<?php echo $related['featured_image']; ?>" alt="<?php echo $related['title']; ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                <div class="w-full h-48 bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                    <i class="fas fa-newspaper text-white text-4xl"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-calendar mr-2"></i>
                        <?php echo formatDate($related['published_at']); ?>
                        <span class="mx-2">•</span>
                        <i class="fas fa-user mr-2"></i>
                        <?php echo $related['author_name']; ?>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                        <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $related['slug']; ?>" class="hover:text-primary transition-colors">
                            <?php echo $related['title']; ?>
                        </a>
                    </h3>
                    
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        <?php echo truncateText($related['excerpt'] ?: strip_tags($related['content']), 100); ?>
                    </p>
                    
                    <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $related['slug']; ?>" class="inline-flex items-center text-primary hover:text-secondary font-semibold">
                        Baca Selengkapnya
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="bg-primary hover:bg-secondary text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 hover:scale-105">
                Lihat Semua Artikel
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter CTA -->
<section class="py-20 bg-gradient-to-r from-primary to-secondary text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Jangan Lewatkan Artikel Terbaru!
        </h2>
        <p class="text-xl mb-8 text-blue-100 leading-relaxed">
            Subscribe newsletter kami dan dapatkan tips publikasi ilmiah langsung di email Anda.
        </p>
        
        <form id="article-newsletter-form" class="max-w-md mx-auto flex">
            <input type="email" placeholder="Masukkan email Anda" class="flex-1 px-4 py-3 rounded-l-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent">
            <button type="submit" class="bg-accent hover:bg-yellow-500 text-gray-900 px-6 py-3 rounded-r-lg font-semibold transition-colors">
                Subscribe
            </button>
        </form>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
