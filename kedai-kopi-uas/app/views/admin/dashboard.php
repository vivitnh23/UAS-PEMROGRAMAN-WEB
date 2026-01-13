<?php
// Akses data dari Controller
$result = $result ?? ['products' => []];
$products = $result['products'];
$base_url = $base_url ?? 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';
$title = $title ?? 'Dashboard Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    
    <style>
        .admin-sidebar {
            background: linear-gradient(180deg, #3E2723 0%, #6F4E37 100%);
            min-height: 100vh;
            color: white;
        }
        
        .admin-sidebar .nav-link {
            color: #E6D5B8 !important;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
        }
        
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background-color: rgba(201, 166, 107, 0.2);
            color: #C9A66B !important;
        }
        
        .stat-card {
            border-radius: 15px;
            border: none;
            color: white;
            padding: 20px;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-lg-2 admin-sidebar">
                <div class="p-3">
                    <div class="text-center mb-4">
                        <div class="bg-secondary rounded-circle d-inline-flex p-3 mb-2">
                            <i class="fas fa-crown fa-2x text-warning"></i>
                        </div>
                        <h4 class="mb-0">Admin Panel</h4>
                        <small class="text-accent">Kedai Kopi Jeje</small>
                        <hr class="bg-secondary my-3">
                        
                        <div class="mb-3">
                            <div class="bg-secondary rounded-pill py-1 px-3 d-inline-block">
                                <i class="fas fa-user me-1"></i>
                                <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo $base_url; ?>admin/dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/products">
                                <i class="fas fa-boxes me-2"></i>Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users me-2"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>">
                                <i class="fas fa-store me-2"></i>Toko
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="<?php echo $base_url; ?>auth/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard Admin
                        </h2>
                        <div>
                            <a href="<?php echo $base_url; ?>admin/products?action=create" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Tambah Produk
                            </a>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #6F4E37 0%, #8B6B61 100%);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Total Produk</h6>
                                        <h2 class="mb-0"><?php echo count($products); ?></h2>
                                    </div>
                                    <i class="fas fa-coffee stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Kategori</h6>
                                        <h2 class="mb-0">
                                            <?php 
                                            $categories = [];
                                            if (is_array($products) && count($products) > 0) {
                                                foreach($products as $p) {
                                                    if(!in_array($p['category'], $categories)) {
                                                        $categories[] = $p['category'];
                                                    }
                                                }
                                            }
                                            echo count($categories);
                                            ?>
                                        </h2>
                                    </div>
                                    <i class="fas fa-tags stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #FF9800 0%, #FFB74D 100%);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Total Stok</h6>
                                        <h2 class="mb-0">
                                            <?php 
                                            $totalStock = 0;
                                            if (is_array($products) && count($products) > 0) {
                                                foreach($products as $p) {
                                                    $totalStock += $p['stock'];
                                                }
                                            }
                                            echo $totalStock;
                                            ?>
                                        </h2>
                                    </div>
                                    <i class="fas fa-boxes stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #2196F3 0%, #64B5F6 100%);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Nilai Stok</h6>
                                        <h4 class="mb-0">
                                            Rp <?php 
                                            $totalValue = 0;
                                            if (is_array($products) && count($products) > 0) {
                                                foreach($products as $p) {
                                                    $totalValue += $p['price'] * $p['stock'];
                                                }
                                            }
                                            echo number_format($totalValue, 0, ',', '.');
                                            ?>
                                        </h4>
                                    </div>
                                    <i class="fas fa-money-bill-wave stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Products -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Produk Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (is_array($products) && count($products) > 0):
                                            $counter = 1;
                                            $recentProducts = array_slice($products, 0, 5);
                                            foreach($recentProducts as $product): 
                                        ?>
                                        <tr>
                                            <td><?php echo $counter++; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($product['category']); ?></span>
                                            </td>
                                            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge <?php echo ($product['stock'] < 10) ? 'bg-danger' : 'bg-success'; ?>">
                                                    <?php echo $product['stock']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo $base_url; ?>admin/products?action=edit&id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo $base_url; ?>admin/products?action=delete&id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Yakin hapus produk?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php 
                                            endforeach;
                                        else: 
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Tidak ada produk yang ditampilkan
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="<?php echo $base_url; ?>admin/products" class="btn btn-primary mt-3">
                                <i class="fas fa-list me-2"></i>Lihat Semua Produk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>