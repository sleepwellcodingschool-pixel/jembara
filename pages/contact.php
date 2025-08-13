<?php
$pageTitle = 'Kontak Kami';
include '../includes/header.php';

// Get contact information
$contactEmail = getSetting('contact_email', 'jembararisetdanmedia@gmail.com');
$contactPhone = getSetting('contact_phone', '0822 4198 0834');
$contactAddress = getSetting('contact_address', 'Jl. K.H. Sholeh Iskandar Raya Km. 2, Kedung Badak, Bogor 16161');

// Check if service is specified in URL
$selectedService = isset($_GET['service']) ? sanitize($_GET['service']) : '';

// Handle form submission
$formSubmitted = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $formError = 'Nama, email, dan pesan wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Format email tidak valid.';
    } else {
        if (sendContactEmail($name, $email, $subject, $message)) {
            $formSubmitted = true;
        } else {
            $formError = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
        }
    }
}
?>

<!-- Page Header -->
<section class="bg-gradient-to-r from-primary to-secondary py-20 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Kontak Kami</h1>
            <p class="text-xl max-w-3xl mx-auto leading-relaxed">
                Hubungi kami untuk konsultasi gratis tentang kebutuhan publikasi ilmiah Anda
            </p>
        </div>
    </div>
</section>

<!-- Contact Form & Info -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>
                
                <?php if ($formSubmitted): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <div class="flex">
                            <i class="fas fa-check-circle mr-3 mt-1"></i>
                            <div>
                                <strong>Pesan berhasil dikirim!</strong>
                                <p class="text-sm mt-1">Terima kasih atas pesan Anda. Tim kami akan segera merespons dalam 1x24 jam.</p>
                            </div>
                        </div>
                    </div>
                <?php elseif ($formError): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle mr-3 mt-1"></i>
                            <div>
                                <strong>Error!</strong>
                                <p class="text-sm mt-1"><?php echo $formError; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6" id="contact-form">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Subjek
                            </label>
                            <select id="subject" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                <option value="">Pilih subjek...</option>
                                <option value="Konsultasi Publikasi Jurnal" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Konsultasi Publikasi Jurnal') || $selectedService == 'Konsultasi Publikasi Jurnal' ? 'selected' : ''; ?>>Konsultasi Publikasi Jurnal</option>
                                <option value="Manajemen Naskah & Submission" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Manajemen Naskah & Submission') || $selectedService == 'Manajemen Naskah & Submission' ? 'selected' : ''; ?>>Manajemen Naskah & Submission</option>
                                <option value="Proofreading & Editing Akademik" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Proofreading & Editing Akademik') || $selectedService == 'Proofreading & Editing Akademik' ? 'selected' : ''; ?>>Proofreading & Editing Akademik</option>
                                <option value="Terjemahan Akademik Profesional" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Terjemahan Akademik Profesional') || $selectedService == 'Terjemahan Akademik Profesional' ? 'selected' : ''; ?>>Terjemahan Akademik Profesional</option>
                                <option value="Monitoring & Follow Up" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Monitoring & Follow Up') || $selectedService == 'Monitoring & Follow Up' ? 'selected' : ''; ?>>Monitoring & Follow Up</option>
                                <option value="Informasi Umum" <?php echo isset($_POST['subject']) && $_POST['subject'] == 'Informasi Umum' ? 'selected' : ''; ?>>Informasi Umum</option>
                                <option value="Lainnya" <?php echo isset($_POST['subject']) && $_POST['subject'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" rows="6" required 
                                  placeholder="Jelaskan kebutuhan Anda..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-vertical"><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 px-6 rounded-lg font-semibold text-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Pesan
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Informasi Kontak</h2>
                
                <!-- Contact Cards -->
                <div class="space-y-6 mb-8">
                    <!-- Address -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-primary text-white p-3 rounded-lg">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Alamat Kantor</h3>
                                <p class="text-gray-600 leading-relaxed"><?php echo $contactAddress; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-secondary text-white p-3 rounded-lg">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
                                <a href="mailto:<?php echo $contactEmail; ?>" class="text-primary hover:text-secondary font-medium">
                                    <?php echo $contactEmail; ?>
                                </a>
                                <p class="text-gray-600 text-sm mt-1">Respons dalam 1x24 jam</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- WhatsApp -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-green-500 text-white p-3 rounded-lg">
                                <i class="fab fa-whatsapp text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">WhatsApp</h3>
                                <a href="https://wa.me/<?php echo str_replace([' ', '-', '(', ')'], '', $contactPhone); ?>" target="_blank" class="text-green-600 hover:text-green-700 font-medium">
                                    <?php echo $contactPhone; ?>
                                </a>
                                <p class="text-gray-600 text-sm mt-1">Chat langsung dengan tim kami</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Business Hours -->
                <div class="bg-gradient-to-r from-primary to-secondary text-white rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">Jam Operasional</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Senin - Jumat</span>
                            <span>08:00 - 17:00 WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sabtu</span>
                            <span>08:00 - 13:00 WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minggu</span>
                            <span>Tutup</span>
                        </div>
                    </div>
                    <div class="border-t border-blue-300 mt-4 pt-4">
                        <p class="text-sm text-blue-100">
                            <i class="fas fa-info-circle mr-2"></i>
                            Konsultasi di luar jam operasional dapat dilakukan melalui WhatsApp
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Lokasi Kami</h2>
            <p class="text-lg text-gray-600">Kunjungi kantor kami untuk konsultasi langsung</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="aspect-w-16 aspect-h-9">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.2!2d106.8!3d-6.2!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTInMDAuMCJTIDEwNsKwNDgnMDAuMCJF!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid"
                    width="100%" 
                    height="400" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-lg text-gray-600">Jawaban untuk pertanyaan umum tentang layanan kami</p>
        </div>
        
        <div class="space-y-6">
            <!-- FAQ Item 1 -->
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-6 py-4 text-left focus:outline-none" onclick="toggleFAQ(1)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Berapa lama proses publikasi artikel?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform" id="faq-icon-1"></i>
                    </div>
                </button>
                <div class="hidden px-6 pb-4" id="faq-content-1">
                    <p class="text-gray-600 leading-relaxed">
                        Waktu publikasi bervariasi tergantung jurnal target dan kompleksitas naskah. Umumnya, proses memerlukan 3-12 bulan. 
                        Kami akan memberikan estimasi waktu yang realistis setelah evaluasi awal naskah Anda.
                    </p>
                </div>
            </div>
            
            <!-- FAQ Item 2 -->
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-6 py-4 text-left focus:outline-none" onclick="toggleFAQ(2)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Apakah ada jaminan artikel akan diterima?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform" id="faq-icon-2"></i>
                    </div>
                </button>
                <div class="hidden px-6 pb-4" id="faq-content-2">
                    <p class="text-gray-600 leading-relaxed">
                        Meskipun tidak ada jaminan 100%, kami memiliki tingkat keberhasilan 98% berkat pengalaman dan strategi yang tepat. 
                        Kami akan memberikan alternatif jurnal jika submission pertama tidak berhasil.
                    </p>
                </div>
            </div>
            
            <!-- FAQ Item 3 -->
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-6 py-4 text-left focus:outline-none" onclick="toggleFAQ(3)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Bagaimana sistem pembayaran layanan?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform" id="faq-icon-3"></i>
                    </div>
                </button>
                <div class="hidden px-6 pb-4" id="faq-content-3">
                    <p class="text-gray-600 leading-relaxed">
                        Kami menerapkan sistem pembayaran bertahap yang transparan. Pembayaran awal untuk memulai proses, 
                        dan sisanya setelah artikel berhasil diterima untuk publikasi. Detail akan dijelaskan saat konsultasi.
                    </p>
                </div>
            </div>
            
            <!-- FAQ Item 4 -->
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-6 py-4 text-left focus:outline-none" onclick="toggleFAQ(4)">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Apakah konsultasi awal gratis?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform" id="faq-icon-4"></i>
                    </div>
                </button>
                <div class="hidden px-6 pb-4" id="faq-content-4">
                    <p class="text-gray-600 leading-relaxed">
                        Ya, konsultasi awal sepenuhnya gratis. Kami akan mengevaluasi naskah Anda, memberikan rekomendasi jurnal, 
                        dan menjelaskan strategi publikasi tanpa biaya apapun.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleFAQ(id) {
    const content = document.getElementById(`faq-content-${id}`);
    const icon = document.getElementById(`faq-icon-${id}`);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
