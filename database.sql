-- Database schema for JEMBARA RISET DAN MEDIA website
CREATE DATABASE IF NOT EXISTS jembara_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jembara_website;

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Website settings table
CREATE TABLE website_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'image', 'color', 'number') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Content sections table
CREATE TABLE content_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255),
    content TEXT,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    detailed_description TEXT,
    icon VARCHAR(100),
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog/News table
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    featured_image VARCHAR(255),
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    tags TEXT,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Testimonials table
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    client_position VARCHAR(100),
    client_company VARCHAR(100),
    testimonial TEXT NOT NULL,
    client_image VARCHAR(255),
    rating INT DEFAULT 5,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact inquiries table
CREATE TABLE contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Live chat messages table
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    sender_type ENUM('visitor', 'admin') NOT NULL,
    sender_name VARCHAR(100),
    sender_email VARCHAR(100),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Chat sessions table
CREATE TABLE chat_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) UNIQUE NOT NULL,
    visitor_name VARCHAR(100),
    visitor_email VARCHAR(100),
    status ENUM('active', 'closed') DEFAULT 'active',
    admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Newsletter subscribers table
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Media files table
CREATE TABLE media_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    alt_text VARCHAR(255),
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, email, password, full_name) VALUES 
('admin', 'admin@jembarariset.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert default website settings
INSERT INTO website_settings (setting_key, setting_value, setting_type) VALUES
('site_title', 'JEMBARA RISET DAN MEDIA', 'text'),
('site_tagline', 'AKU, KAMU DAN DIA, HIDUPKAN PENELITIAN DI INDONESIA', 'text'),
('site_description', 'Layanan Publikasi Ilmiah & Akses Penerbitan Jurnal', 'textarea'),
('primary_color', '#2563eb', 'color'),
('secondary_color', '#1e40af', 'color'),
('accent_color', '#f59e0b', 'color'),
('contact_email', 'jembararisetdanmedia@gmail.com', 'text'),
('contact_phone', '0822 4198 0834', 'text'),
('contact_address', 'Jl. K.H. Sholeh Iskandar Raya Km. 2, Kedung Badak, Bogor 16161', 'textarea'),
('company_name', 'JEMBARA RISET DAN MEDIA', 'text'),
('business_field', 'Layanan Publikasi Ilmiah & Akses Penerbitan Jurnal', 'text'),
('hero_title', 'Jembatan Menuju Publikasi Ilmiah Berkualitas', 'text'),
('hero_subtitle', 'Kami membantu peneliti, akademisi, dan profesional mewujudkan publikasi artikel di jurnal nasional dan internasional bereputasi', 'textarea');

-- Insert default content sections
INSERT INTO content_sections (section_key, title, content) VALUES
('about_company', 'Tentang Kami', 'JEMBARA RISET DAN PUBLIKASI perusahaan yang berfokus pada layanan publikasi ilmiah, membantu peneliti, akademisi, dan profesional untuk menjembatani proses penerbitan artikel di jurnal nasional maupun internasional bereputasi. Kami memahami bahwa proses publikasi memerlukan waktu, keahlian, dan strategi yang tepat, sehingga kami hadir sebagai mitra yang memastikan setiap karya ilmiah memiliki peluang terbaik untuk dipublikasikan sesuai standar akademik.'),
('company_vision', 'Visi', 'Menjadi jembatan riset nusantara yang berkualitas dan menghidupkan penelitian yang ada di Indonesia'),
('company_mission', 'Misi', '1. Menyediakan layanan publikasi ilmiah yang cepat, transparan, dan berkualitas.\n2. Menjalin kemitraan dengan penerbit jurnal bereputasi nasional dan internasional.\n3. Memberikan pendampingan dan konsultasi publikasi yang profesional dan berintegritas.\n4. Mendukung peningkatan kualitas karya ilmiah untuk kontribusi nyata bagi kemajuan ilmu pengetahuan.');

-- Insert default services
INSERT INTO services (title, description, detailed_description, icon, sort_order) VALUES
('Konsultasi Publikasi Jurnal', 'Pendampingan pemilihan jurnal yang sesuai dengan bidang penelitian dan target publikasi', 'Layanan konsultasi komprehensif untuk membantu peneliti memilih jurnal yang tepat sesuai dengan bidang penelitian, dampak faktor, dan target publikasi. Tim ahli kami akan menganalisis naskah dan memberikan rekomendasi jurnal terbaik.', 'fas fa-comments', 1),
('Manajemen Naskah & Submission', 'Pengelolaan naskah mulai dari editing, format, hingga proses submission ke publisher', 'Layanan lengkap pengelolaan naskah ilmiah mulai dari tahap editing, formatting sesuai standar jurnal, hingga proses submission ke publisher. Kami memastikan setiap detail naskah sesuai dengan guidelines jurnal target.', 'fas fa-file-alt', 2),
('Proofreading & Editing Akademik', 'Perbaikan tata bahasa, gaya penulisan akademik, dan format sesuai panduan jurnal', 'Layanan editing profesional untuk meningkatkan kualitas penulisan akademik, memperbaiki tata bahasa, struktur kalimat, dan memastikan konsistensi gaya penulisan sesuai standar internasional.', 'fas fa-edit', 3),
('Terjemahan Akademik Profesional', 'Penerjemahan karya ilmiah ke bahasa Inggris atau bahasa lain dengan standar akademik internasional', 'Jasa terjemahan khusus untuk publikasi ilmiah dengan tim penerjemah berpengalaman di bidang akademik. Kami memastikan terminologi teknis dan gaya penulisan akademik tetap terjaga dalam bahasa target.', 'fas fa-language', 4),
('Monitoring & Follow Up', 'Pemantauan proses review dan komunikasi dengan pihak jurnal hingga publikasi selesai', 'Layanan pemantauan berkelanjutan terhadap proses review artikel, komunikasi dengan editor jurnal, dan update progress hingga artikel berhasil dipublikasikan. Klien akan mendapat laporan berkala tentang status publikasi.', 'fas fa-chart-line', 5);

-- Insert sample testimonials
INSERT INTO testimonials (client_name, client_position, client_company, testimonial, rating, sort_order) VALUES
('Dr. Ahmad Susanto', 'Dosen', 'Universitas Indonesia', 'Layanan JEMBARA sangat membantu dalam proses publikasi artikel saya di jurnal internasional. Tim profesional dan proses yang transparan.', 5, 1),
('Prof. Siti Aminah', 'Peneliti Senior', 'LIPI', 'Berkat bantuan JEMBARA, artikel penelitian saya berhasil dipublikasikan di jurnal bereputasi. Highly recommended!', 5, 2),
('Dr. Bambang Prasetyo', 'Kepala Penelitian', 'Institut Teknologi Bandung', 'Proses yang cepat dan hasil yang memuaskan. JEMBARA benar-benar memahami kebutuhan publikasi ilmiah.', 5, 3);

-- Insert sample blog posts
INSERT INTO blog_posts (title, slug, excerpt, content, author_id, status, published_at) VALUES
('Tips Memilih Jurnal yang Tepat untuk Publikasi', 'tips-memilih-jurnal-publikasi', 'Panduan lengkap untuk peneliti dalam memilih jurnal yang sesuai dengan bidang penelitian dan target publikasi.', 'Memilih jurnal yang tepat merupakan langkah krusial dalam proses publikasi ilmiah. Artikel ini akan membahas berbagai faktor yang perlu dipertimbangkan, mulai dari scope jurnal, impact factor, hingga proses review. \n\nFaktor-faktor penting yang harus diperhatikan:\n1. Kesesuaian dengan bidang penelitian\n2. Impact factor dan reputasi jurnal\n3. Proses dan waktu review\n4. Biaya publikasi\n5. Open access policy\n\nDengan mempertimbangkan faktor-faktor tersebut, peneliti dapat meningkatkan peluang publikasi yang sukses.', 1, 'published', NOW()),
('Pentingnya Proofreading dalam Publikasi Ilmiah', 'pentingnya-proofreading-publikasi-ilmiah', 'Mengapa proofreading menjadi tahap krusial dalam menyiapkan naskah untuk publikasi jurnal internasional.', 'Proofreading merupakan tahap akhir yang sangat penting dalam proses penulisan ilmiah. Banyak artikel berkualitas ditolak karena kesalahan bahasa dan format yang dapat dihindari.\n\nManfaat proofreading profesional:\n- Memperbaiki grammar dan struktur kalimat\n- Memastikan konsistensi terminologi\n- Menyesuaikan format dengan guidelines jurnal\n- Meningkatkan readability artikel\n\nInvestasi dalam proofreading profesional akan significantly meningkatkan peluang acceptance artikel Anda.', 1, 'published', NOW());
