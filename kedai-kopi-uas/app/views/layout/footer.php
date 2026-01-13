    </main>
    
    <!-- Footer -->
    <footer class="mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="fas fa-coffee me-2"></i>Kedai Kopi Jeje</h5>
                    <p class="mb-0">Menghadirkan kopi terbaik dengan kualitas premium untuk kenikmatan sejati.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Kontak Kami</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i>Jl. Kopi Raya No. 123, Kota Jeje</li>
                        <li><i class="fas fa-phone me-2"></i>(021) 1234-5678</li>
                        <li><i class="fas fa-envelope me-2"></i>info@kedaikopijeje.com</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Follow Us</h5>
                    <div class="social-links">
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Kedai Kopi Jeje. All rights reserved.</p>
                <p class="mb-0">Project UAS Pemrograman Web</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
    
    <script>
    // Handle active nav link
    document.addEventListener('DOMContentLoaded', function() {
        const currentUrl = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentUrl) {
                link.classList.add('active');
            }
        });
    });
    </script>
</body>
</html>