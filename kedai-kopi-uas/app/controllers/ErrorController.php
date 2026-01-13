<?php
class ErrorController {
    public function notFound() {
        http_response_code(404);
        $title = "404 - Halaman Tidak Ditemukan";
        include 'app/views/layout/header.php';
        ?>
        
        <div class="text-center py-5">
            <h1 class="display-1 text-muted">404</h1>
            <h2 class="mb-4">Halaman Tidak Ditemukan</h2>
            <p class="lead mb-4">Maaf, halaman yang Anda cari tidak ditemukan.</p>
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
        </div>
        
        <?php
        include 'app/views/layout/footer.php';
    }
    
    public function unauthorized() {
        http_response_code(403);
        $title = "403 - Akses Ditolak";
        include 'app/views/layout/header.php';
        ?>
        
        <div class="text-center py-5">
            <h1 class="display-1 text-muted">403</h1>
            <h2 class="mb-4">Akses Ditolak</h2>
            <p class="lead mb-4">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
        </div>
        
        <?php
        include 'app/views/layout/footer.php';
    }
}
?>