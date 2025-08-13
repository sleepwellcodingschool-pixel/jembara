<?php
$pageTitle = '';
include 'includes/header.php';

// Get dynamic content
$aboutSection = getContentSection('about_company');
$heroTitle = getSetting('hero_title', 'Jembatan Menuju Publikasi Ilmiah Berkualitas');
$heroSubtitle = getSetting('hero_subtitle', 'Kami membantu peneliti, akademisi, dan profesional mewujudkan publikasi artikel di jurnal nasional dan internasional bereputasi');

// Get services (limit to 3 for homepage)
$services = getServices(3);

// Get latest blog posts (limit to 3)
$blogPosts = getBlogPosts(3);

// Get testimonials
$testimonials = getTestimonials(3);
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary to-secondary min-h-screen flex items-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 opacity-20">
        <img src="https://pixabay.com/get/g8335bad221d8d74dbb3242b22d93d85f8b7b73ed6c0633eb0928cc0a74f1d38a22011e715bac45b816fd8aaa593cfbad37e252989e2c355873e2240008dd6d19_1280.jpg" alt="Scientific Research" class="w-full h-full object-cover">
    </div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Hero Text -->
            <div class="text-white">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    <?php echo $heroTitle; ?>
                </h1>
                <p class="text-xl md:text-2xl leading-relaxed mb-8 text-blue-100">
                    <?php echo $heroSubtitle; ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="bg-accent hover:bg-yellow-500 text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 hover:scale-105 text-center">
                        Konsultasi Gratis
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/services.php" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 text-center">
                        Lihat Layanan
                    </a>
                </div>
            </div>
            
            <!-- Hero Stats -->
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center text-white">
                    <div class="text-3xl font-bold text-accent">500+</div>
                    <div class="text-sm mt-2">Artikel Dipublikasikan</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center text-white">
                    <div class="text-3xl font-bold text-accent">98%</div>
                    <div class="text-sm mt-2">Tingkat Keberhasilan</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center text-white">
                    <div class="text-3xl font-bold text-accent">100+</div>
                    <div class="text-sm mt-2">Jurnal Partner</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center text-white">
                    <div class="text-3xl font-bold text-accent">24/7</div>
                    <div class="text-sm mt-2">Support</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
        <i class="fas fa-chevron-down text-2xl"></i>
    </div>
</section>

<!-- About Preview Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Mengapa Memilih JEMBARA?
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed mb-8">
                    <?php echo $aboutSection ? $aboutSection['content'] : 'JEMBARA RISET DAN PUBLIKASI perusahaan yang berfokus pada layanan publikasi ilmiah, membantu peneliti, akademisi, dan profesional untuk menjembatani proses penerbitan artikel di jurnal nasional maupun internasional bereputasi.'; ?>
                </p>
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="text-center p-4 border rounded-lg">
                        <i class="fas fa-award text-3xl text-primary mb-3"></i>
                        <h4 class="font-semibold text-gray-900">Berpengalaman</h4>
                        <p class="text-sm text-gray-600">Tim profesional dengan track record terbukti</p>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <i class="fas fa-globe text-3xl text-primary mb-3"></i>
                        <h4 class="font-semibold text-gray-900">Jaringan Luas</h4>
                        <p class="text-sm text-gray-600">Koneksi dengan jurnal nasional & internasional</p>
                    </div>
                </div>
                <a href="<?php echo SITE_URL; ?>/pages/about.php" class="inline-flex items-center text-primary hover:text-secondary font-semibold">
                    Pelajari Lebih Lanjut
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="relative">
                <img src="https://pixabay.com/get/g34992a94c0d3f8731d3fd3d230acd185d167c2a16b5acb3b15c38ae346186a0c63b397604afacdb952ec1e502e17fe503c860d20797fd3f06be7fcd75a3904a8_1280.jpg" alt="Professional Office" class="rounded-xl shadow-2xl">
                <div class="absolute -bottom-6 -right-6 bg-accent text-gray-900 p-6 rounded-xl shadow-lg">
                    <div class="text-2xl font-bold">5+</div>
                    <div class="text-sm">Tahun Pengalaman</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Layanan Kami
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Solusi lengkap untuk kebutuhan publikasi ilmiah Anda
            </p>
        </div>
        
        <!-- Services Grid -->
        <div class="grid md:grid-cols-3 gap-8">
            <?php if ($services && $services->num_rows > 0): ?>
                <?php while ($service = $services->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="text-primary text-4xl mb-6">
                        <i class="<?php echo $service['icon']; ?>"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <?php echo $service['title']; ?>
                    </h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        <?php echo $service['description']; ?>
                    </p>
                    <a href="<?php echo SITE_URL; ?>/pages/services.php#service-<?php echo $service['id']; ?>" class="inline-flex items-center text-primary hover:text-secondary font-semibold">
                        Pelajari Lebih Lanjut
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo SITE_URL; ?>/pages/services.php" class="bg-primary hover:bg-secondary text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 hover:scale-105">
                Lihat Semua Layanan
            </a>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Proses Kerja Kami
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Langkah demi langkah menuju publikasi yang sukses
            </p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Step 1 -->
            <div class="text-center relative">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    1
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Konsultasi</h3>
                <p class="text-gray-600">Diskusi awal tentang naskah dan target jurnal</p>
                <!-- Arrow for desktop -->
                <div class="hidden md:block absolute top-8 -right-4 text-gray-300">
                    <i class="fas fa-arrow-right text-2xl"></i>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="text-center relative">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    2
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Analisis & Review</h3>
                <p class="text-gray-600">Evaluasi mendalam dan perbaikan naskah</p>
                <div class="hidden md:block absolute top-8 -right-4 text-gray-300">
                    <i class="fas fa-arrow-right text-2xl"></i>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="text-center relative">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    3
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Submission</h3>
                <p class="text-gray-600">Proses pengiriman ke jurnal target</p>
                <div class="hidden md:block absolute top-8 -right-4 text-gray-300">
                    <i class="fas fa-arrow-right text-2xl"></i>
                </div>
            </div>
            
            <!-- Step 4 -->
            <div class="text-center">
                <div class="bg-accent text-gray-900 w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    4
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Publikasi</h3>
                <p class="text-gray-600">Artikel berhasil dipublikasikan</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<?php if ($testimonials && $testimonials->num_rows > 0): ?>
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Testimoni Klien
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Apa kata mereka tentang layanan kami
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php while ($testimonial = $testimonials->fetch_assoc()): ?>
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-4">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-600 italic mb-6 leading-relaxed">
                    "<?php echo $testimonial['testimonial']; ?>"
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-gray-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900"><?php echo $testimonial['client_name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $testimonial['client_position']; ?></p>
                        <?php if ($testimonial['client_company']): ?>
                        <p class="text-sm text-gray-500"><?php echo $testimonial['client_company']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Blog Posts -->
<?php if ($blogPosts && $blogPosts->num_rows > 0): ?>
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Blog & Berita Terbaru
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Informasi terkini seputar dunia publikasi ilmiah
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php while ($post = $blogPosts->fetch_assoc()): ?>
            <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <?php if ($post['featured_image']): ?>
                <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                <div class="w-full h-48 bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                    <i class="fas fa-newspaper text-white text-4xl"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-calendar mr-2"></i>
                        <?php echo formatDate($post['published_at']); ?>
                        <span class="mx-2">•</span>
                        <i class="fas fa-user mr-2"></i>
                        <?php echo $post['author_name']; ?>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                        <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $post['slug']; ?>" class="hover:text-primary transition-colors">
                            <?php echo $post['title']; ?>
                        </a>
                    </h3>
                    
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        <?php echo truncateText($post['excerpt'] ?: strip_tags($post['content']), 120); ?>
                    </p>
                    
                    <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $post['slug']; ?>" class="inline-flex items-center text-primary hover:text-secondary font-semibold">
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

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary to-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Siap Mewujudkan Publikasi Impian Anda?
        </h2>
        <p class="text-xl mb-8 max-w-3xl mx-auto leading-relaxed">
            Jangan biarkan karya ilmiah Anda tertunda. Mari bersama kami wujudkan publikasi di jurnal bereputasi.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="bg-accent hover:bg-yellow-500 text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 hover:scale-105">
                Konsultasi Gratis Sekarang
            </a>
            <a href="tel:<?php echo str_replace([' ', '-', '(', ')'], '', $contactPhone); ?>" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300">
                Hubungi Kami
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
