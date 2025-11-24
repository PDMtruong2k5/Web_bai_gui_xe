// Theme Switcher
(function() {
    const THEME_KEY = 'app-theme';
    const LIGHT_THEME = 'light';
    const DARK_THEME = 'dark';
    
    // Get saved theme or default to light
    function getSavedTheme() {
        return localStorage.getItem(THEME_KEY) || LIGHT_THEME;
    }
    
    // Apply theme
    function applyTheme(theme) {
        const root = document.documentElement;
        
        if (theme === DARK_THEME) {
            // Dark theme colors
            root.style.setProperty('--black', '#0f172a');
            root.style.setProperty('--text-primary', '#cbd5e1');
            root.style.setProperty('--text-secondary', '#94a3b8');
            root.style.setProperty('--white', '#1e293b');
            root.style.setProperty('--gray-100', '#334155');
            root.style.setProperty('--gray-200', '#475569');
            root.style.setProperty('--gray-300', '#64748b');
            root.style.setProperty('--gray-400', '#94a3b8');
            root.style.setProperty('--gray-500', '#cbd5e1');
            
            document.body.style.background = 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)';
            document.body.classList.add('dark-theme');
            document.body.classList.remove('light-theme');
        } else {
            // Light theme colors
            root.style.setProperty('--black', '#ffffff');
            root.style.setProperty('--text-primary', '#111827');
            root.style.setProperty('--text-secondary', '#6b7280');
            root.style.setProperty('--white', '#FFFFFF');
            root.style.setProperty('--gray-100', '#f3f4f6');
            root.style.setProperty('--gray-200', '#e5e7eb');
            root.style.setProperty('--gray-300', '#d1d5db');
            root.style.setProperty('--gray-400', '#9ca3af');
            root.style.setProperty('--gray-500', '#6b7280');
            
            document.body.style.background = '#ffffff';
            document.body.classList.add('light-theme');
            document.body.classList.remove('dark-theme');
        }
        
        localStorage.setItem(THEME_KEY, theme);
        updateThemeButton(theme);
    }
    
    // Update button state
    function updateThemeButton(theme) {
        const btn = document.getElementById('theme-toggle-btn');
        if (btn) {
            if (theme === DARK_THEME) {
                btn.innerHTML = '<i class="fas fa-sun"></i>';
                btn.title = 'Chuyển sang chế độ sáng';
                btn.classList.add('dark-active');
            } else {
                btn.innerHTML = '<i class="fas fa-moon"></i>';
                btn.title = 'Chuyển sang chế độ tối';
                btn.classList.remove('dark-active');
            }
        }
    }
    
    // Toggle theme
    window.toggleTheme = function() {
        const currentTheme = getSavedTheme();
        const newTheme = currentTheme === LIGHT_THEME ? DARK_THEME : LIGHT_THEME;
        applyTheme(newTheme);
    };
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = getSavedTheme();
        applyTheme(savedTheme);
    });
    
    // Also apply theme immediately if DOM is already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = getSavedTheme();
            applyTheme(savedTheme);
        });
    } else {
        const savedTheme = getSavedTheme();
        applyTheme(savedTheme);
    }
})();
