<?php
$pageTitle = 'Layanan Kami';
include '../includes/header.php';

// Get all services
$services = getServices();
?>

<!-- Page Header -->
<section class="bg-gradient-to-r from-primary to-secondary py-20 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Layanan Kami</h1>
            <p class="text-xl max-w-3xl mx-auto leading-relaxed">
                Solusi lengkap untuk semua kebutuhan publikasi ilmiah Anda
            </p>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Penerbitan Naskah Ilmiah
            </h2>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                Kami menyediakan layanan komprehensif untuk membantu Anda menerbitkan karya ilmiah di jurnal bereputasi. 
                Setiap layanan dirancang untuk memaksimalkan peluang publikasi Anda.
            </p>
        </div>
        
        <!-- Services List -->
        <div class="space-y-16">
            <?php if ($services && $services->num_rows > 0): ?>
                <?php $serviceIndex = 1; ?>
                <?php while ($service = $services->fetch_assoc()): ?>
                <div id="service-<?php echo $service['id']; ?>" class="grid lg:grid-cols-2 gap-12 items-center <?php echo $serviceIndex % 2 == 0 ? 'lg:flex-row-reverse' : ''; ?>">
                    <!-- Service Content -->
                    <div class="<?php echo $serviceIndex % 2 == 0 ? 'lg:order-2' : ''; ?>">
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl">
                                <i class="<?php echo $service['icon']; ?>"></i>
                            </div>
                            <div>
                                <span class="text-primary font-semibold text-sm">LAYANAN <?php echo str_pad($serviceIndex, 2, '0', STR_PAD_LEFT); ?></span>
                                <h3 class="text-2xl md:text-3xl font-bold text-gray-900">
                                    <?php echo $service['title']; ?>
                                </h3>
                            </div>
                        </div>
                        
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                            <?php echo $service['description']; ?>
                        </p>
                        
                        <div class="text-gray-600 leading-relaxed mb-8">
                            <?php echo nl2br($service['detailed_description']); ?>
                        </div>
                        
                        <!-- Service Features -->
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700 text-sm">Konsultasi mendalam</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700 text-sm">Proses transparan</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700 text-sm">Tim profesional</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700 text-sm">Garansi kualitas</span>
                            </div>
                        </div>
                        
                        <a href="<?php echo SITE_URL; ?>/pages/contact.php?service=<?php echo urlencode($service['title']); ?>" class="bg-primary hover:bg-secondary text-white px-8 py-3 rounded-lg font-semibold transition-all duration-300 hover:scale-105 inline-flex items-center">
                            Konsultasi Layanan Ini
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    
                    <!-- Service Image -->
                    <div class="<?php echo $serviceIndex % 2 == 0 ? 'lg:order-1' : ''; ?>">
                        <?php 
                        $imageUrls = [
                            'https://pixabay.com/get/ga973b4216b30421607d7635eda1d955327003e2f2099b3982b366547378c3f9f55e0c3ffb2660a6db1615dc7da20fea4f1959657d4e0866ce6ead334710d5511_1280.jpg',
                            'https://pixabay.com/get/g528f688406bac4464652e668d120c15be4d40a1be8d72f8dfa7427fbadd95be6d25535e3fbff1f7089d6b421ef1c0c500a36f5be16a08c789e53876338728e38_1280.jpg',
                            'https://pixabay.com/get/g19efedb171ca925850e2e6ac702b3ce2b6c87150687b530a1c69aa4a5a6fcd14280625864012892b2b08bb15977ca0c7a5a7c9070dba88b20cad1061c67531d1_1280.jpg',
                            'https://pixabay.com/get/g462d197d0a527fc817430bff5a3382ba4c7a042b6bcd9b0f6c553062028d65e51c74c313c6e094a8cabbb6173d5de33258d622230455b11d3bf68921aa05d887_1280.jpg'
                        ];
                        $imageIndex = ($serviceIndex - 1) % count($imageUrls);
                        ?>
                        <div class="relative">
                            <img src="<?php echo $imageUrls[$imageIndex]; ?>" alt="<?php echo $service['title']; ?>" class="rounded-xl shadow-2xl w-full">
                            <div class="absolute -bottom-6 -right-6 bg-accent text-gray-900 p-4 rounded-xl shadow-lg">
                                <div class="text-2xl font-bold"><?php echo str_pad($serviceIndex, 2, '0', STR_PAD_LEFT); ?></div>
                                <div class="text-sm">Layanan</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $serviceIndex++; ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Our Services -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Mengapa Memilih Layanan Kami?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Keunggulan yang membuat kami menjadi pilihan terbaik
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Benefit 1 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Proses Cepat</h3>
                <p class="text-gray-600 leading-relaxed">
                    Timeline yang jelas dan proses yang efisien untuk mempercepat publikasi Anda.
                </p>
            </div>
            
            <!-- Benefit 2 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Terjamin Kualitas</h3>
                <p class="text-gray-600 leading-relaxed">
                    Standar tinggi dalam setiap layanan dengan jaminan hasil yang memuaskan.
                </p>
            </div>
            
            <!-- Benefit 3 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="bg-accent text-gray-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Support 24/7</h3>
                <p class="text-gray-600 leading-relaxed">
                    Tim support yang siap membantu Anda kapan saja selama proses publikasi.
                </p>
            </div>
            
            <!-- Benefit 4 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="bg-green-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-globe text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Jangkauan Global</h3>
                <p class="text-gray-600 leading-relaxed">
                    Akses ke jurnal nasional dan internasional bereputasi di seluruh dunia.
                </p>
            </div>
            
            <!-- Benefit 5 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="bg-purple-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Track Record Proven</h3>
                <p class="text-gray-600 leading-relaxed">
                    Ribuan artikel telah berhasil dipublikasikan dengan tingkat keberhasilan 98%.
                </p>
            </div>
            
            <!-- Benefit 6 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="bg-red-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Harga Kompetitif</h3>
                <p class="text-gray-600 leading-relaxed">
                    Investasi yang terjangkau dengan hasil maksimal untuk publikasi Anda.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Process Flow -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Alur Kerja Layanan
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Langkah demi langkah proses yang kami lakukan untuk kesuksesan publikasi Anda
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Step 1 -->
            <div class="text-center relative">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 relative z-10">
                    1
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Konsultasi Awal</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Diskusi mendalam tentang naskah, target jurnal, dan strategi publikasi yang tepat
                </p>
                <!-- Connection Line -->
                <div class="hidden lg:block absolute top-8 left-16 w-full h-1 bg-gray-200 -z-10">
                    <div class="h-full bg-primary w-1/2"></div>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="text-center relative">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 relative z-10">
                    2
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Analisis & Review</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Evaluasi mendalam terhadap naskah dan rekomendasi perbaikan yang diperlukan
                </p>
                <div class="hidden lg:block absolute top-8 left-16 w-full h-1 bg-gray-200 -z-10">
                    <div class="h-full bg-primary w-1/2"></div>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="text-center relative">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 relative z-10">
                    3
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Proses Editing</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Perbaikan naskah, formatting, dan penyesuaian dengan guidelines jurnal target
                </p>
                <div class="hidden lg:block absolute top-8 left-16 w-full h-1 bg-gray-200 -z-10">
                    <div class="h-full bg-primary w-1/2"></div>
                </div>
            </div>
            
            <!-- Step 4 -->
            <div class="text-center">
                <div class="bg-accent text-gray-900 w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    4
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Submission & Follow Up</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Pengiriman ke jurnal dan pemantauan hingga artikel berhasil dipublikasikan
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Information -->
<section class="py-20 bg-gradient-to-r from-primary to-secondary text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Tertarik Dengan Layanan Kami?
        </h2>
        <p class="text-xl mb-8 leading-relaxed text-blue-100">
            Dapatkan konsultasi gratis untuk menentukan layanan yang paling sesuai dengan kebutuhan publikasi Anda. 
            Tim ahli kami siap membantu mewujudkan impian publikasi ilmiah Anda.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="bg-accent hover:bg-yellow-500 text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 hover:scale-105">
                Konsultasi Gratis Sekarang
            </a>
            <a href="tel:<?php echo str_replace([' ', '-', '(', ')'], '', getSetting('contact_phone', '0822 4198 0834')); ?>" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300">
                Hubungi Langsung
            </a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
