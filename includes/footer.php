<?php
$contactEmail = getSetting('contact_email', 'jembararisetdanmedia@gmail.com');
$contactPhone = getSetting('contact_phone', '0822 4198 0834');
$contactAddress = getSetting('contact_address', 'Jl. K.H. Sholeh Iskandar Raya Km. 2, Kedung Badak, Bogor 16161');
$companyName = getSetting('company_name', 'JEMBARA RISET DAN MEDIA');
$siteTitle = getSetting('site_title', 'JEMBARA RISET DAN MEDIA');
?>
    </div> <!-- Close pt-16 div from header -->
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="<?php echo SITE_URL; ?>/assets/images/logo.svg" alt="<?php echo $siteTitle; ?>" class="h-10 w-10 filter invert">
                        <div>
                            <h3 class="text-xl font-bold"><?php echo $siteTitle; ?></h3>
                            <p class="text-gray-400 text-sm"><?php echo getSetting('business_field', 'Layanan Publikasi Ilmiah'); ?></p>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-4 leading-relaxed">
                        <?php echo getSetting('site_tagline', 'AKU, KAMU DAN DIA, HIDUPKAN PENELITIAN DI INDONESIA'); ?>
                    </p>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Kami membantu peneliti, akademisi, dan profesional untuk menjembatani proses penerbitan artikel di jurnal nasional maupun internasional bereputasi.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Menu</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo SITE_URL; ?>/" class="text-gray-400 hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/about.php" class="text-gray-400 hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/services.php" class="text-gray-400 hover:text-white transition-colors">Layanan</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/blog.php" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/contact.php" class="text-gray-400 hover:text-white transition-colors">Kontak</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                            <p class="text-gray-400 text-sm"><?php echo $contactAddress; ?></p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-primary"></i>
                            <a href="mailto:<?php echo $contactEmail; ?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?php echo $contactEmail; ?></a>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="fab fa-whatsapp text-primary"></i>
                            <a href="https://wa.me/<?php echo str_replace([' ', '-', '(', ')'], '', $contactPhone); ?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?php echo $contactPhone; ?></a>
                        </div>
                    </div>
                    
                    <!-- Newsletter Signup -->
                    <div class="mt-6">
                        <h5 class="font-medium mb-2">Newsletter</h5>
                        <form id="newsletter-form" class="flex">
                            <input type="email" id="newsletter-email" placeholder="Email Anda" class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-l-lg text-sm focus:outline-none focus:border-primary">
                            <button type="submit" class="bg-primary hover:bg-secondary px-4 py-2 rounded-r-lg transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; <?php echo date('Y'); ?> <?php echo $companyName; ?>. All rights reserved.
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Live Chat Widget -->
    <div id="chat-widget" class="fixed bottom-4 right-4 z-50">
        <!-- Chat Button -->
        <button id="chat-toggle" class="bg-primary hover:bg-secondary text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110">
            <i class="fas fa-comments text-xl"></i>
        </button>
        
        <!-- Chat Window -->
        <div id="chat-window" class="hidden absolute bottom-16 right-0 w-80 bg-white rounded-lg shadow-2xl border">
            <!-- Chat Header -->
            <div class="bg-primary text-white p-4 rounded-t-lg flex justify-between items-center">
                <div>
                    <h4 class="font-semibold">Live Chat</h4>
                    <p class="text-sm opacity-90">Kami siap membantu Anda</p>
                </div>
                <button id="chat-close" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Chat Messages -->
            <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-3">
                <div class="text-center text-gray-500 text-sm">
                    Mulai percakapan dengan mengetik pesan Anda
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="p-4 border-t">
                <form id="chat-form">
                    <div class="flex space-x-2">
                        <input type="text" id="chat-input" placeholder="Ketik pesan..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Contact Info Form (shown initially) -->
                <div id="chat-info-form" class="mt-3">
                    <input type="text" id="chat-name" placeholder="Nama Anda" class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:outline-none focus:border-primary">
                    <input type="email" id="chat-email" placeholder="Email Anda" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                </div>
            </div>
        </div>
    </div>
    
    <!-- WhatsApp Float Button -->
    <div class="fixed bottom-4 left-4 z-40">
        <a href="https://wa.me/<?php echo str_replace([' ', '-', '(', ')'], '', $contactPhone); ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110 flex items-center space-x-2">
            <i class="fab fa-whatsapp text-xl"></i>
            <span class="hidden sm:inline text-sm font-medium">WhatsApp</span>
        </a>
    </div>
    
    <!-- Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/chat.js"></script>
</body>
</html>
