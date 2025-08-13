<?php
require_once __DIR__ . '/../config/config.php';

function getServices($limit = null) {
    global $db;
    $sql = "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    return $db->query($sql);
}

function getBlogPosts($limit = null, $offset = 0) {
    global $db;
    $sql = "SELECT bp.*, au.full_name as author_name 
            FROM blog_posts bp 
            LEFT JOIN admin_users au ON bp.author_id = au.id 
            WHERE bp.status = 'published' 
            ORDER BY bp.published_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . intval($offset) . ", " . intval($limit);
    }
    return $db->query($sql);
}

function getBlogPost($slug) {
    global $db;
    $stmt = $db->prepare("SELECT bp.*, au.full_name as author_name 
                         FROM blog_posts bp 
                         LEFT JOIN admin_users au ON bp.author_id = au.id 
                         WHERE bp.slug = ? AND bp.status = 'published'");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getTestimonials($limit = null) {
    global $db;
    $sql = "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    return $db->query($sql);
}

function getContentSection($key) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM content_sections WHERE section_key = ? AND is_active = 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function incrementBlogViews($id) {
    global $db;
    $stmt = $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function sendContactEmail($name, $email, $subject, $message) {
    // In a real application, implement email sending here
    // For now, we'll just store in database
    global $db;
    $stmt = $db->prepare("INSERT INTO contact_inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    return $stmt->execute();
}

function subscribeNewsletter($email, $name = '') {
    global $db;
    $stmt = $db->prepare("INSERT INTO newsletter_subscribers (email, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), status = 'active'");
    $stmt->bind_param("ss", $email, $name);
    return $stmt->execute();
}

function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function getPageTitle($page = '') {
    $siteTitle = getSetting('site_title', 'JEMBARA RISET DAN MEDIA');
    return empty($page) ? $siteTitle : $page . ' - ' . $siteTitle;
}

function generateChatSessionId() {
    return 'chat_' . uniqid() . '_' . time();
}
?>
