    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="text-gray-600 text-sm">
                        © 2026 Bhadriko - HomeBakle33. All rights reserved.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <a href="#" class="hover:text-invoice-primary transition-colors">Bantuan</a>
                    <span>•</span>
                    <a href="#" class="hover:text-invoice-primary transition-colors">Dokumentasi</a>
                    <span>•</span>
                    <a href="<?php echo $baseUrl ?? ''; ?>page/settings/company.php" class="hover:text-invoice-primary transition-colors">Pengaturan</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        // Auto-hide flash messages after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    </script>
    
    <script src="<?php echo $baseUrl ?? ''; ?>js/script.js"></script>
</body>
</html>
