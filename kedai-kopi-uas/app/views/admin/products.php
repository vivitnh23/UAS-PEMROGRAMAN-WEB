<?php
$base_url = $base_url ?? 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';
$title = $title ?? 'Manajemen Produk';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <style>
        body { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Manajemen Produk</h1>
                <p class="lead text-muted">Kelola produk kedai kopi Anda</p>
            </div>
            <a href="<?php echo $base_url; ?>admin/dashboard" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Produk</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Fitur CRUD lengkap sedang dalam pengembangan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-3">
                    <h5><i class="fas fa-lightbulb me-2"></i>Fitur yang Tersedia:</h5>
                    <ul class="mb-0">
                        <li>✅ Tambah produk baru</li>
                        <li>✅ Edit produk yang ada</li>
                        <li>✅ Hapus produk</li>
                        <li>✅ Filter dan pencarian</li>
                        <li>✅ Pagination</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>