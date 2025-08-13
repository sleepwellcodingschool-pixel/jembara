<?php
$pageTitle = 'Tentang Kami';
include '../includes/header.php';

// Get content sections
$aboutSection = getContentSection('about_company');
$visionSection = getContentSection('company_vision');
$missionSection = getContentSection('company_mission');
?>

<!-- Page Header -->
<section class="bg-gradient-to-r from-primary to-secondary py-20 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Tentang Kami</h1>
            <p class="text-xl max-w-3xl mx-auto leading-relaxed">
                Mengenal lebih dalam tentang JEMBARA RISET DAN MEDIA
            </p>
        </div>
    </div>
</section>

<!-- About Company -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    <?php echo $aboutSection ? $aboutSection['title'] : 'Tentang Kami'; ?>
                </h2>
                <div class="text-lg text-gray-600 leading-relaxed mb-8 space-y-4">
                    <?php 
                    $content = $aboutSection ? $aboutSection['content'] : 'JEMBARA RISET DAN PUBLIKASI perusahaan yang berfokus pada layanan publikasi ilmiah, membantu peneliti, akademisi, dan profesional untuk menjembatani proses penerbitan artikel di jurnal nasional maupun internasional bereputasi.';
                    echo nl2br($content);
                    ?>
                </div>
                
                <!-- Key Features -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-start space-x-3">
                        <div class="bg-primary text-white p-2 rounded-lg">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Profesional</h4>
                            <p class="text-gray-600 text-sm">Tim berpengalaman di bidang publikasi ilmiah</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-primary text-white p-2 rounded-lg">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Transparan</h4>
                            <p class="text-gray-600 text-sm">Proses yang jelas dan dapat dipantau</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-primary text-white p-2 rounded-lg">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Terpercaya</h4>
                            <p class="text-gray-600 text-sm">Rekam jejak publikasi yang terbukti</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-primary text-white p-2 rounded-lg">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Berkualitas</h4>
                            <p class="text-gray-600 text-sm">Standar tinggi dalam setiap layanan</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="relative">
                <img src="https://pixabay.com/get/g5e47114c71ea47a26c28aebd778473ff5e94b46a0c86b272c5cc56e4e8d4cdd5becb5b5cc83cdf2eeecc15576061348322912e3caa637bcdf8d706bebaafcdc4_1280.jpg" alt="Academic Collaboration" class="rounded-xl shadow-2xl">
                <div class="absolute -top-6 -left-6 bg-accent text-gray-900 p-6 rounded-xl shadow-lg">
                    <div class="text-2xl font-bold">500+</div>
                    <div class="text-sm">Artikel Published</div>
                </div>
                <div class="absolute -bottom-6 -right-6 bg-white border-4 border-primary text-primary p-6 rounded-xl shadow-lg">
                    <div class="text-2xl font-bold">98%</div>
                    <div class="text-sm">Success Rate</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Vision -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-eye text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        <?php echo $visionSection ? $visionSection['title'] : 'Visi'; ?>
                    </h2>
                </div>
                <p class="text-lg text-gray-600 text-center leading-relaxed">
                    <?php echo $visionSection ? $visionSection['content'] : 'Menjadi jembatan riset nusantara yang berkualitas dan menghidupkan penelitian yang ada di Indonesia'; ?>
                </p>
            </div>
            
            <!-- Mission -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        <?php echo $missionSection ? $missionSection['title'] : 'Misi'; ?>
                    </h2>
                </div>
                <div class="text-gray-600 leading-relaxed">
                    <?php 
                    $missionContent = $missionSection ? $missionSection['content'] : '1. Menyediakan layanan publikasi ilmiah yang cepat, transparan, dan berkualitas.\n2. Menjalin kemitraan dengan penerbit jurnal bereputasi nasional dan internasional.\n3. Memberikan pendampingan dan konsultasi publikasi yang profesional dan berintegritas.\n4. Mendukung peningkatan kualitas karya ilmiah untuk kontribusi nyata bagi kemajuan ilmu pengetahuan.';
                    echo nl2br($missionContent);
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Advantages -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Keunggulan Kami
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Yang membuat kami berbeda dari yang lain
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Advantage 1 -->
            <div class="text-center p-6 rounded-xl border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-network-wired text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    Jaringan Luas Publisher Bereputasi
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Koneksi langsung dengan berbagai jurnal nasional terakreditasi Sinta dan internasional bereputasi.
                </p>
            </div>
            
            <!-- Advantage 2 -->
            <div class="text-center p-6 rounded-xl border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-eye text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    Proses Transparan
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Klien dapat memantau progres setiap tahap publikasi dengan sistem tracking yang jelas.
                </p>
            </div>
            
            <!-- Advantage 3 -->
            <div class="text-center p-6 rounded-xl border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="bg-accent text-gray-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    Tim Profesional Berpengalaman
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Berasal dari latar belakang akademisi dan praktisi publikasi yang berpengalaman.
                </p>
            </div>
            
            <!-- Advantage 4 -->
            <div class="text-center p-6 rounded-xl border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="bg-green-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-hands-helping text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    Dukungan Penuh Hingga Terbit
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Layanan end-to-end sampai artikel resmi terbit di jurnal target dengan dukungan berkelanjutan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-20 bg-gradient-to-r from-primary to-secondary text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Pencapaian Kami
            </h2>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Angka-angka yang menunjukkan komitmen kami terhadap kualitas
            </p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-accent mb-2">500+</div>
                <h3 class="text-lg font-semibold mb-2">Artikel Dipublikasikan</h3>
                <p class="text-blue-100 text-sm">Berhasil diterbitkan di berbagai jurnal</p>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-accent mb-2">98%</div>
                <h3 class="text-lg font-semibold mb-2">Tingkat Keberhasilan</h3>
                <p class="text-blue-100 text-sm">Artikel yang berhasil dipublikasikan</p>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-accent mb-2">100+</div>
                <h3 class="text-lg font-semibold mb-2">Jurnal Partner</h3>
                <p class="text-blue-100 text-sm">Nasional dan internasional bereputasi</p>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-accent mb-2">5+</div>
                <h3 class="text-lg font-semibold mb-2">Tahun Pengalaman</h3>
                <p class="text-blue-100 text-sm">Melayani publikasi ilmiah</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
            Bergabunglah dengan Ribuan Peneliti yang Telah Mempercayai Kami
        </h2>
        <p class="text-xl text-gray-600 mb-8 leading-relaxed">
            Wujudkan impian publikasi ilmiah Anda bersama JEMBARA RISET DAN MEDIA
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="bg-primary hover:bg-secondary text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300 hover:scale-105">
                Konsultasi Gratis
            </a>
            <a href="<?php echo SITE_URL; ?>/pages/services.php" class="border-2 border-primary text-primary hover:bg-primary hover:text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-300">
                Lihat Layanan
            </a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
