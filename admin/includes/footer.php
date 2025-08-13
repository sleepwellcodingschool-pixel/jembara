            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>
    
    <!-- Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking overlay
        document.getElementById('mobile-overlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
        
        // User menu dropdown
        document.getElementById('user-menu-btn').addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        });
        
        // Close user menu when clicking outside
        document.addEventListener('click', function() {
            const menu = document.getElementById('user-menu');
            menu.classList.add('hidden');
        });
        
        // Prevent closing when clicking inside the menu
        document.getElementById('user-menu').addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
