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
    </script>
</body>
</html>
