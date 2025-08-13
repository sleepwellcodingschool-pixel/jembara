<?php
// Prevent direct access to uploads directory
// This file provides security by preventing directory browsing

// Set proper headers
header('HTTP/1.0 403 Forbidden');
header('Content-Type: text/html; charset=UTF-8');

// Get the requested file from URL
$requestedFile = $_SERVER['REQUEST_URI'];
$uploadDir = '/uploads/';

// Check if this is a legitimate file request
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = __DIR__ . '/' . $filename;
    
    // Security checks
    if (file_exists($filepath) && is_file($filepath)) {
        // Check file extension
        $allowedExtensions = [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',  // Images
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',  // Documents
            'txt', 'csv'  // Text files
        ];
        
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            exit('File type not allowed');
        }
        
        // Check file size (max 50MB)
        if (filesize($filepath) > 50 * 1024 * 1024) {
            exit('File too large');
        }
        
        // Set appropriate headers based on file type
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'csv' => 'text/csv'
        ];
        
        $contentType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';
        
        // Security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        // Set content type
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($filepath));
        
        // For images, allow inline display
        if (strpos($contentType, 'image/') === 0) {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        } else {
            // For other files, force download
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        
        // Output file
        readfile($filepath);
        exit;
    }
}

// If we reach here, access is forbidden
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Access Denied</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        
        .container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            margin: 0;
            background: linear-gradient(45deg, #f59e0b, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .error-title {
            font-size: 2rem;
            margin: 1rem 0;
            font-weight: 600;
        }
        
        .error-description {
            font-size: 1.1rem;
            margin: 1.5rem 0;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .security-info {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #f59e0b;
            padding: 1rem;
            margin: 2rem 0;
            text-align: left;
            border-radius: 0 8px 8px 0;
        }
        
        .security-info h3 {
            margin: 0 0 0.5rem 0;
            color: #f59e0b;
        }
        
        .security-info ul {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        
        .security-info li {
            margin: 0.25rem 0;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #f59e0b;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }
        
        .btn:hover {
            background: #d97706;
            transform: translateY(-2px);
        }
        
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .error-code {
                font-size: 4rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔒</div>
        <h1 class="error-code">403</h1>
        <h2 class="error-title">Akses Ditolak</h2>
        <p class="error-description">
            Maaf, Anda tidak memiliki izin untuk mengakses direktori ini. 
            Halaman ini dilindungi untuk keamanan website.
        </p>
        
        <div class="security-info">
            <h3>Informasi Keamanan</h3>
            <p>Direktori uploads dilindungi untuk mencegah:</p>
            <ul>
                <li>Akses langsung ke file yang tidak sah</li>
                <li>Directory browsing</li>
                <li>Eksekusi script berbahaya</li>
                <li>Akses ke file pribadi</li>
            </ul>
        </div>
        
        <div style="margin-top: 2rem;">
            <a href="/" class="btn">🏠 Kembali ke Beranda</a>
            <a href="/pages/contact.php" class="btn">📞 Hubungi Kami</a>
        </div>
        
        <div style="margin-top: 2rem; font-size: 0.9rem; opacity: 0.7;">
            <p>Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator.</p>
            <p><strong>JEMBARA RISET DAN MEDIA</strong> - Layanan Publikasi Ilmiah</p>
        </div>
    </div>
    
    <script>
        // Log security attempt (optional)
        console.warn('Unauthorized access attempt to uploads directory');
        
        // Optional: Send security notification to admin
        // This could be implemented to notify administrators of access attempts
        
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.container');
            
            // Add floating animation
            container.style.animation = 'float 3s ease-in-out infinite';
            
            // Add CSS animation keyframes
            const style = document.createElement('style');
            style.textContent = `
                @keyframes float {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-10px); }
                }
            `;
            document.head.appendChild(style);
        });
        
        // Prevent common bypass attempts
        document.addEventListener('keydown', function(e) {
            // Disable F12, Ctrl+Shift+I, Ctrl+U
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || 
                (e.ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    </script>
</body>
</html>
