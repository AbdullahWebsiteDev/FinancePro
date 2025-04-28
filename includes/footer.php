</div>
            </main>
        </div>
    </div>
    
    <script>
        // User dropdown toggle
        document.getElementById('userMenuButton').addEventListener('click', function() {
            document.getElementById('userDropdown').classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.closest('#userMenuButton') && !document.getElementById('userDropdown').classList.contains('hidden')) {
                document.getElementById('userDropdown').classList.add('hidden');
            }
        });
        
        // Mobile sidebar toggle
        document.getElementById('mobileSidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        });
        
        // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const htmlElement = document.documentElement;
        
        // Check for saved theme preference or use system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Apply the theme on page load
        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            htmlElement.classList.add('dark');
            darkModeIcon.classList.remove('fa-moon');
            darkModeIcon.classList.add('fa-sun');
        }
        
        // Toggle dark mode when button is clicked
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                htmlElement.classList.toggle('dark');
                
                // Update icon
                if (htmlElement.classList.contains('dark')) {
                    darkModeIcon.classList.remove('fa-moon');
                    darkModeIcon.classList.add('fa-sun');
                    localStorage.setItem('theme', 'dark');
                } else {
                    darkModeIcon.classList.remove('fa-sun');
                    darkModeIcon.classList.add('fa-moon');
                    localStorage.setItem('theme', 'light');
                }
            });
        }
    </script>
</body>
</html>
