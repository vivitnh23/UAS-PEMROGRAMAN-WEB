<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class AdminController {
    
    public function dashboard() {
        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';
        
        // 1. BUAT/KONEK KE DATABASE
        $conn = $this->setupDatabase();
        
        if (!$conn) {
            $this->showDatabaseSetupPage($base_url);
            return;
        }
        
        // 2. AMBIL DATA PRODUK
        $products = $this->getProducts($conn);
        
        // 3. TAMPILKAN DASHBOARD
        $this->showDashboard($products, $base_url, $conn);
    }
    
    private function setupDatabase() {
        try {
            // Step 1: Connect to MySQL server (without database)
            $conn = new PDO("mysql:host=localhost", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Step 2: Create database if not exists
            $conn->exec("CREATE DATABASE IF NOT EXISTS kedai_kopi 
                        CHARACTER SET utf8mb4 
                        COLLATE utf8mb4_general_ci");
            
            // Step 3: Select the database
            $conn->exec("USE kedai_kopi");
            
            // Step 4: Create products table
            $sql = "
            CREATE TABLE IF NOT EXISTS products (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                stock INT DEFAULT 0,
                category VARCHAR(50),
                image VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $conn->exec($sql);
            
            // Step 5: Check if table has data
            $stmt = $conn->query("SELECT COUNT(*) as count FROM products");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Step 6: Insert sample data if empty
            if ($result['count'] == 0) {
                $this->insertSampleData($conn);
            }
            
            return $conn;
            
        } catch(PDOException $e) {
            error_log("Database Setup Error: " . $e->getMessage());
            return false;
        }
    }
    
    private function insertSampleData($conn) {
        $sampleData = [
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Espresso', 'Kopi murni tanpa campuran', 20000, 50, 'Kopi')",
            
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Cappuccino', 'Kopi dengan susu steamed', 25000, 30, 'Kopi')",
            
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Latte', 'Kopi dengan banyak susu', 28000, 25, 'Kopi')",
            
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Mocha', 'Kopi dengan coklat', 30000, 20, 'Kopi')",
            
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Americano', 'Espresso dengan air panas', 22000, 40, 'Kopi')",
            
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Green Tea Latte', 'Teh hijau dengan susu', 23000, 35, 'Non-Coffee')",
            
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Croissant', 'Roti pastry ala Perancis', 18000, 60, 'Pastry')",
            
            "INSERT INTO products (name, description, price, stock, category) VALUES 
            ('Chocolate Cake', 'Kue coklat lezat', 35000, 15, 'Dessert')"
        ];
        
        foreach ($sampleData as $sql) {
            try {
                $conn->exec($sql);
            } catch(PDOException $e) {
                error_log("Insert Error: " . $e->getMessage());
            }
        }
    }
    
    private function getProducts($conn) {
        try {
            $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 10");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get Products Error: " . $e->getMessage());
            return [];
        }
    }
    
    private function showDashboard($products, $base_url, $conn) {
        // Hitung statistik
        $totalProducts = count($products);
        $totalStock = 0;
        $totalValue = 0;
        $lowStockCount = 0;
        $categories = [];
        
        foreach ($products as $product) {
            $totalStock += $product['stock'];
            $totalValue += $product['price'] * $product['stock'];
            
            if ($product['stock'] < 10) {
                $lowStockCount++;
            }
            
            if (!in_array($product['category'], $categories)) {
                $categories[] = $product['category'];
            }
        }
        ?>
        
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard Admin - Kedai Kopi Jeje</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                :root {
                    --coffee-dark: #3E2723;
                    --coffee-medium: #6F4E37;
                    --coffee-light: #8B6B61;
                    --accent: #C9A66B;
                }
                body { background: #f5f5f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                .sidebar { background: linear-gradient(180deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%); 
                          color: white; min-height: 100vh; }
                .stat-card { border-radius: 15px; border: none; color: white; transition: transform 0.3s; }
                .stat-card:hover { transform: translateY(-5px); }
                .coffee-bg { background: linear-gradient(135deg, var(--coffee-medium) 0%, var(--coffee-light) 100%); }
                .success-bg { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
                .warning-bg { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }
                .info-bg { background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); }
            </style>
        </head>
        <body>
            <div class="container-fluid p-0">
                <div class="row g-0">
                    <!-- Sidebar -->
                    <div class="col-lg-2 sidebar p-4">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-secondary d-inline-flex p-3 mb-3">
                                <i class="fas fa-crown fa-2x text-warning"></i>
                            </div>
                            <h4 class="mb-1">Kedai Kopi Jeje</h4>
                            <small class="text-accent">Admin Panel</small>
                            <hr class="bg-light my-3">
                            <div class="bg-dark rounded-pill py-2 px-3">
                                <i class="fas fa-user me-2"></i>
                                <span>Administrator</span>
                            </div>
                        </div>
                        
                        <nav class="nav flex-column">
                            <a class="nav-link active bg-dark rounded py-3 mb-2" href="#">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                            <a class="nav-link text-white py-3 mb-2" href="#">
                                <i class="fas fa-boxes me-2"></i>Produk
                            </a>
                            <a class="nav-link text-white py-3 mb-2" href="#">
                                <i class="fas fa-receipt me-2"></i>Pesanan
                            </a>
                            <a class="nav-link text-white py-3 mb-2" href="#">
                                <i class="fas fa-chart-bar me-2"></i>Laporan
                            </a>
                            <div class="mt-5 pt-4 border-top">
                                <a href="<?php echo $base_url; ?>" class="btn btn-outline-light w-100">
                                    <i class="fas fa-store me-2"></i>Lihat Toko
                                </a>
                            </div>
                        </nav>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="col-lg-10 p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h1 class="h3 mb-1 text-coffee">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                                </h1>
                                <p class="text-muted mb-0">Selamat datang di panel admin Kedai Kopi Jeje</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-3">
                                    <i class="fas fa-database me-1"></i>Database Active
                                </span>
                                <span class="badge bg-info">
                                    <i class="fas fa-check-circle me-1"></i><?php echo $totalProducts; ?> Produk
                                </span>
                            </div>
                        </div>
                        
                        <!-- Stats Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-xl-3 col-lg-6">
                                <div class="stat-card coffee-bg p-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Total Produk</h6>
                                            <h2 class="mb-0"><?php echo $totalProducts; ?></h2>
                                            <small class="opacity-75">Item tersedia</small>
                                        </div>
                                        <i class="fas fa-coffee fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-6">
                                <div class="stat-card success-bg p-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Total Stok</h6>
                                            <h2 class="mb-0"><?php echo $totalStock; ?></h2>
                                            <small class="opacity-75">Unit tersedia</small>
                                        </div>
                                        <i class="fas fa-boxes fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-6">
                                <div class="stat-card warning-bg p-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Stok Rendah</h6>
                                            <h2 class="mb-0"><?php echo $lowStockCount; ?></h2>
                                            <small class="opacity-75">< 10 unit</small>
                                        </div>
                                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-6">
                                <div class="stat-card info-bg p-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Nilai Stok</h6>
                                            <h4 class="mb-0">Rp <?php echo number_format($totalValue, 0, ',', '.'); ?></h4>
                                            <small class="opacity-75">Total nilai</small>
                                        </div>
                                        <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Products Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Daftar Produk</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($totalProducts > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">#</th>
                                                    <th>Nama Produk</th>
                                                    <th>Kategori</th>
                                                    <th>Harga</th>
                                                    <th>Stok</th>
                                                    <th>Status</th>
                                                    <th width="120">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $counter = 1; ?>
                                                <?php foreach($products as $product): ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                                        <small class="text-muted"><?php echo substr($product['description'] ?? '', 0, 50); ?>...</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($product['category']); ?></span>
                                                    </td>
                                                    <td class="fw-bold text-primary">
                                                        Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?php echo ($product['stock'] < 10) ? 'bg-danger' : 'bg-success'; ?> p-2">
                                                            <?php echo $product['stock']; ?> unit
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($product['stock'] > 20): ?>
                                                            <span class="badge bg-success">Aman</span>
                                                        <?php elseif ($product['stock'] > 5): ?>
                                                            <span class="badge bg-warning">Sedikit</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Habis</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="alert alert-success mt-3">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Database berhasil dibuat otomatis!</strong> 
                                        Sistem telah membuat database 'kedai_kopi' dan menambahkan <?php echo $totalProducts; ?> produk contoh.
                                    </div>
                                    
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-database fa-4x text-muted mb-3"></i>
                                        <h4 class="text-muted">Belum ada data produk</h4>
                                        <p class="text-muted">Database berhasil dibuat, tapi tabel products masih kosong.</p>
                                        <button onclick="location.reload()" class="btn btn-primary">
                                            <i class="fas fa-redo me-2"></i>Refresh Halaman
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                                        <h5>Tambah Produk</h5>
                                        <p class="text-muted small">Tambahkan produk baru ke katalog</p>
                                        <button class="btn btn-outline-primary w-100">Tambah Baru</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                        <h5>Lihat Laporan</h5>
                                        <p class="text-muted small">Analisis penjualan dan stok</p>
                                        <button class="btn btn-outline-success w-100">Buka Laporan</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-cog fa-3x text-warning mb-3"></i>
                                        <h5>Pengaturan</h5>
                                        <p class="text-muted small">Kelola pengaturan toko</p>
                                        <button class="btn btn-outline-warning w-100">Pengaturan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="mt-4 pt-3 border-top text-center text-muted">
                            <small>
                                <i class="fas fa-coffee me-1"></i>
                                Kedai Kopi Jeje Admin Panel © <?php echo date('Y'); ?>
                                | Database: kedai_kopi | Produk: <?php echo $totalProducts; ?>
                                | <a href="<?php echo $base_url; ?>" class="text-decoration-none">Kunjungi Toko</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                // Auto refresh setiap 30 detik untuk update stok
                setTimeout(function() {
                    window.location.reload();
                }, 30000);
            </script>
        </body>
        </html>
        <?php
    }
    
    private function showDatabaseSetupPage($base_url) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Setup Database - Kedai Kopi Jeje</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0"><i class="fas fa-database me-2"></i>Setup Database</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Database Tidak Ditemukan</h5>
                                    <p>Sistem akan mencoba membuat database otomatis.</p>
                                </div>
                                
                                <div class="text-center my-4">
                                    <i class="fas fa-cogs fa-4x text-muted"></i>
                                </div>
                                
                                <p>Klik tombol di bawah untuk:</p>
                                <ul>
                                    <li>Membuat database 'kedai_kopi'</li>
                                    <li>Membuat tabel 'products'</li>
                                    <li>Menambahkan data contoh</li>
                                </ul>
                                
                                <form method="POST" action="<?php echo $base_url; ?>admin/dashboard">
                                    <input type="hidden" name="setup_database" value="1">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-play me-2"></i>Jalankan Setup Database
                                        </button>
                                        <a href="<?php echo $base_url; ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-home me-2"></i>Kembali ke Home
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
?>