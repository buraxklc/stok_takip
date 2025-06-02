<?php
// app/views/layouts/footer.php
?>
        <footer class="bg-white py-4 mt-5 border-top">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Stok Takip Sistemi <?php echo date('Y'); ?></div>
                    <div>
                        <span class="text-muted">Versiyon 1.0.0</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- End of Main Content -->

    <!-- Bootstrap 5 JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-collapsed');
                sidebar.classList.toggle('show');
                content.classList.toggle('content-fluid');
            });
        }
        
        // Handle responsive behavior
        function checkWidth() {
            if (window.innerWidth < 768) {
                sidebar.classList.add('sidebar-collapsed');
                content.classList.add('content-fluid');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                content.classList.remove('content-fluid');
            }
        }
        
        // Initial check
        checkWidth();
        
        // Check on resize
        window.addEventListener('resize', checkWidth);
        
        // Collapse menu on item click (mobile)
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        if (window.innerWidth < 768) {
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (!this.classList.contains('collapsed') && !this.nextElementSibling) {
                        sidebar.classList.add('sidebar-collapsed');
                        sidebar.classList.remove('show');
                    }
                });
            });
        }
        
        // Auto close alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // Enable tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Enable popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
        
        // Active sidebar menu based on current URL
        function setActiveMenuItem() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                    
                    // If in a dropdown, expand the dropdown
                    const dropdown = link.closest('.collapse');
                    if (dropdown) {
                        dropdown.classList.add('show');
                        const toggler = document.querySelector(`[data-bs-target="#${dropdown.id}"]`);
                        if (toggler) {
                            toggler.classList.remove('collapsed');
                            toggler.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });
        }
        
        setActiveMenuItem();
        
        // Custom file input label
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = this.files[0].name;
                const label = this.nextElementSibling;
                if (label) {
                    label.textContent = fileName;
                }
                
                // Preview image if there's a preview element
                const previewId = this.getAttribute('data-preview');
                if (previewId) {
                    const preview = document.getElementById(previewId);
                    if (preview && this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                }
            });
        });
    });
    </script>
</body>
</html>