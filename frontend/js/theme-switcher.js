(function() {
    // 🔍 Check saved theme or default to dark
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    document.documentElement.style.colorScheme = savedTheme; // 🔥 Forced OS-level sync


    // 🚀 Wait for DOM to load for toggle binding
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggles = document.querySelectorAll('.theme-toggle');
        
        themeToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                // ✨ Apply theme
                document.documentElement.setAttribute('data-theme', newTheme);
                document.documentElement.style.colorScheme = newTheme; // 🔥 Real-time OS-level sync
                
                // 💾 Save preference
                localStorage.setItem('theme', newTheme);

            });
        });
    });
})();
