<?php
require_once '../config/config.php';
requireLogin();

$message = '';
$error = '';

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $postId = (int)$_GET['id'];
                $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
                $stmt->bind_param("i", $postId);
                if ($stmt->execute()) {
                    $message = 'Artikel berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus artikel.';
                }
            }
            break;
            
        case 'toggle-status':
            if (isset($_GET['id'])) {
                $postId = (int)$_GET['id'];
                $stmt = $db->prepare("UPDATE blog_posts SET status = IF(status = 'published', 'draft', 'published') WHERE id = ?");
                $stmt->bind_param("i", $postId);
                if ($stmt->execute()) {
                    $message = 'Status artikel berhasil diubah!';
                } else {
                    $error = 'Gagal mengubah status artikel.';
                }
            }
            break;
    }
}

// Handle form submission for create/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $slug = generateSlug($title);
    $excerpt = sanitize($_POST['excerpt']);
    $content = sanitize($_POST['content']);
    $featuredImage = sanitize($_POST['featured_image']);
    $metaTitle = sanitize($_POST['meta_title']);
    $metaDescription = sanitize($_POST['meta_description']);
    $tags = sanitize($_POST['tags']);
    $status = sanitize($_POST['status']);
    $authorId = $_SESSION['admin_id'];
    
    // Check if editing existing post
    if (isset($_POST['post_id']) && !empty($_POST['post_id'])) {
        $postId = (int)$_POST['post_id'];
        
        // Make slug unique if editing
        $existingPost = $db->prepare("SELECT slug FROM blog_posts WHERE id = ?");
        $existingPost->bind_param("i", $postId);
        $existingPost->execute();
        $existing = $existingPost->get_result()->fetch_assoc();
        
        if ($existing['slug'] !== $slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (true) {
                $checkSlug = $db->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
                $checkSlug->bind_param("si", $slug, $postId);
                $checkSlug->execute();
                if ($checkSlug->get_result()->num_rows === 0) {
                    break;
                }
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        
        $publishedAt = ($status === 'published' && $existing['published_at'] === null) ? date('Y-m-d H:i:s') : null;
        
        if ($publishedAt) {
            $stmt = $db->prepare("UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, status = ?, meta_title = ?, meta_description = ?, tags = ?, published_at = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("ssssssssssi", $title, $slug, $excerpt, $content, $featuredImage, $status, $metaTitle, $metaDescription, $tags, $publishedAt, $postId);
        } else {
            $stmt = $db->prepare("UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, status = ?, meta_title = ?, meta_description = ?, tags = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("sssssssssi", $title, $slug, $excerpt, $content, $featuredImage, $status, $metaTitle, $metaDescription, $tags, $postId);
        }
        
        if ($stmt->execute()) {
            $message = 'Artikel berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui artikel.';
        }
    } else {
        // Creating new post
        // Make slug unique
        $originalSlug = $slug;
        $counter = 1;
        while (true) {
            $checkSlug = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
            $checkSlug->bind_param("s", $slug);
            $checkSlug->execute();
            if ($checkSlug->get_result()->num_rows === 0) {
                break;
            }
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;
        
        $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, author_id, status, meta_title, meta_description, tags, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssissss", $title, $slug, $excerpt, $content, $featuredImage, $authorId, $status, $metaTitle, $metaDescription, $tags, $publishedAt);
        
        if ($stmt->execute()) {
            $message = 'Artikel berhasil dibuat!';
        } else {
            $error = 'Gagal membuat artikel.';
        }
    }
}

// Get blog posts
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$totalPosts = $db->query("SELECT COUNT(*) as count FROM blog_posts")->fetch_assoc()['count'];
$totalPages = ceil($totalPosts / $perPage);

$posts = $db->query("SELECT bp.*, au.full_name as author_name 
                    FROM blog_posts bp 
                    LEFT JOIN admin_users au ON bp.author_id = au.id 
                    ORDER BY bp.created_at DESC 
                    LIMIT $offset, $perPage");

// Check if editing
$editPost = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editPost = $stmt->get_result()->fetch_assoc();
}

$pageTitle = 'Kelola Blog';
include 'includes/header.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Blog</h1>
        <p class="text-gray-600">Kelola artikel dan konten blog website.</p>
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
    
    <!-- Article Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">
                <?php echo $editPost ? 'Edit Artikel' : 'Buat Artikel Baru'; ?>
            </h2>
            <?php if ($editPost): ?>
                <a href="blog.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal Edit
                </a>
            <?php endif; ?>
        </div>
        
        <form method="POST" class="space-y-6">
            <?php if ($editPost): ?>
                <input type="hidden" name="post_id" value="<?php echo $editPost['id']; ?>">
            <?php endif; ?>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Artikel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo $editPost ? htmlspecialchars($editPost['title']) : ''; ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="draft" <?php echo ($editPost && $editPost['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo ($editPost && $editPost['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                        <option value="archived" <?php echo ($editPost && $editPost['status'] === 'archived') ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Ringkasan Artikel</label>
                <textarea id="excerpt" name="excerpt" rows="3" 
                          placeholder="Ringkasan singkat tentang artikel ini..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo $editPost ? htmlspecialchars($editPost['excerpt']) : ''; ?></textarea>
            </div>
            
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Konten Artikel <span class="text-red-500">*</span>
                </label>
                <textarea id="content" name="content" rows="15" required 
                          placeholder="Tulis konten artikel di sini..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo $editPost ? htmlspecialchars($editPost['content']) : ''; ?></textarea>
            </div>
            
            <div>
                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">URL Gambar Utama</label>
                <input type="url" id="featured_image" name="featured_image" 
                       value="<?php echo $editPost ? htmlspecialchars($editPost['featured_image']) : ''; ?>"
                       placeholder="https://example.com/image.jpg"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            
            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags (pisahkan dengan koma)</label>
                <input type="text" id="tags" name="tags" 
                       value="<?php echo $editPost ? htmlspecialchars($editPost['tags']) : ''; ?>"
                       placeholder="publikasi, jurnal, penelitian"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            
            <!-- SEO Section -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Settings</h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" 
                               value="<?php echo $editPost ? htmlspecialchars($editPost['meta_title']) : ''; ?>"
                               placeholder="Title untuk SEO (kosongkan jika sama dengan judul artikel)"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="3" 
                                  placeholder="Deskripsi untuk mesin pencari (160 karakter)"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo $editPost ? htmlspecialchars($editPost['meta_description']) : ''; ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $editPost ? 'Update Artikel' : 'Simpan Artikel'; ?>
                </button>
                
                <?php if ($editPost): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $editPost['slug']; ?>" target="_blank" 
                       class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>Preview
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Articles List -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Semua Artikel</h2>
        </div>
        
        <?php if ($posts && $posts->num_rows > 0): ?>
            <div class="divide-y divide-gray-200">
                <?php while ($post = $posts->fetch_assoc()): ?>
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <?php echo $post['title']; ?>
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php 
                                    echo $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 
                                         ($post['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); 
                                ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-3">
                                <?php echo truncateText($post['excerpt'] ?: strip_tags($post['content']), 150); ?>
                            </p>
                            
                            <div class="flex items-center space-x-6 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-1"></i>
                                    <?php echo $post['author_name']; ?>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?php echo formatDate($post['created_at']); ?>
                                </div>
                                <?php if ($post['published_at']): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-globe mr-1"></i>
                                    Published: <?php echo formatDate($post['published_at']); ?>
                                </div>
                                <?php endif; ?>
                                <div class="flex items-center">
                                    <i class="fas fa-eye mr-1"></i>
                                    <?php echo $post['views']; ?> views
                                </div>
                            </div>
                            
                            <?php if ($post['tags']): ?>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <?php 
                                $tags = explode(',', $post['tags']);
                                foreach (array_slice($tags, 0, 5) as $tag): 
                                    $tag = trim($tag);
                                ?>
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                    <?php echo $tag; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center space-x-2 ml-6">
                            <a href="blog.php?edit=<?php echo $post['id']; ?>" 
                               class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <?php if ($post['status'] === 'published'): ?>
                            <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $post['slug']; ?>" target="_blank"
                               class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <?php endif; ?>
                            
                            <a href="blog.php?action=toggle-status&id=<?php echo $post['id']; ?>" 
                               class="text-yellow-600 hover:text-yellow-800 p-2 hover:bg-yellow-50 rounded"
                               title="Toggle Status">
                                <i class="fas fa-toggle-<?php echo $post['status'] === 'published' ? 'on' : 'off'; ?>"></i>
                            </a>
                            
                            <a href="blog.php?action=delete&id=<?php echo $post['id']; ?>" 
                               class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="p-6 border-t border-gray-200">
                <div class="flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-left mr-2"></i>Previous
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="px-4 py-2 border <?php echo $i == $page ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50'; ?> rounded-lg transition-colors">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                            Next<i class="fas fa-chevron-right ml-2"></i>
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-newspaper text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Artikel</h3>
                <p class="mb-6">Mulai dengan membuat artikel pertama Anda.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
