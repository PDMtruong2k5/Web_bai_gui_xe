        </div>
    </main>

    <footer class="footer">
        <div class="footer__container">
            <p> Hệ thống Quản lý Bãi Gửi Xe</p>
        </div>
    </footer>

    <script>
        // Sidebar visibility persistence using localStorage
        (function() {
            const body = document.body;
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const main = document.querySelector('.main');
            const toggle = document.getElementById('sidebarToggle');

            // Apply saved preference on load
            const saved = localStorage.getItem('sidebarHidden');
            if (saved === '1') {
                body.classList.add('no-sidebar');
                sidebar?.classList.add('collapsed');
                overlay?.classList.remove('active');
            } else {
                body.classList.remove('no-sidebar');
                sidebar?.classList.remove('collapsed');
            }

            // Toggle and persist
            toggle?.addEventListener('click', function() {
                const isHidden = body.classList.toggle('no-sidebar');
                // keep sidebar collapsed class in sync for any other styles
                if (isHidden) {
                    sidebar?.classList.add('collapsed');
                    overlay?.classList.remove('active');
                    localStorage.setItem('sidebarHidden', '1');
                } else {
                    sidebar?.classList.remove('collapsed');
                    localStorage.setItem('sidebarHidden', '0');
                }
                // Also toggle expanded class on main for older CSS support
                main?.classList.toggle('expanded', isHidden);
            });

            // Floating menu button (for when sidebar is hidden)
            const floating = document.getElementById('menuFloating');
            floating?.addEventListener('click', function() {
                // clear stored hidden state and show sidebar
                localStorage.setItem('sidebarHidden', '0');
                body.classList.remove('no-sidebar');
                sidebar?.classList.remove('collapsed');
                main?.classList.remove('expanded');
            });
        })();

        // Auto-close alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Add mobile class to body if screen width is less than 768px
        function checkMobile() {
            if (window.innerWidth < 768) {
                document.body.classList.add('is-mobile');
                document.getElementById('sidebar')?.classList.add('collapsed');
            } else {
                document.body.classList.remove('is-mobile');
                document.getElementById('sidebar')?.classList.remove('collapsed');
            }
        }
        window.addEventListener('resize', checkMobile);
        checkMobile();
    </script>

    <script src="/btl_nhom-13-demo/js/theme-switcher.js"></script>
</body>
</html>