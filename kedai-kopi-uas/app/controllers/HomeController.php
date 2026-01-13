<?php
class HomeController {
    public function index() {
        // Base URL
        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';
        
        // Inisialisasi variabel
        $result = ['products' => [], 'total' => 0, 'total_pages' => 1, 'page' => 1, 'limit' => 6];
        $categories = [];
        
        try {
            // Database connection
            require_once 'app/config/Database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            if (!$db) {
                throw new Exception("Database connection failed");
            }
            
            // Get products model
            require_once 'app/models/Product.php';
            $productModel = new Product($db);
            
            // Get parameters - FIX: Pastikan page minimal 1
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $limit = 6;
            $offset = ($page - 1) * $limit;
            
            // OPTION 1: Coba dengan metode Model
            $products = [];
            if (method_exists($productModel, 'getProducts')) {
                try {
                    // Coba 4 parameter
                    $products = $productModel->getProducts($search, $category, $limit, $offset);
                } catch (Exception $e) {
                    // Coba 5 parameter (jika ada sorting)
                    try {
                        $products = $productModel->getProducts($search, $category, 'newest', $limit, $offset);
                    } catch (Exception $e2) {
                        // Direct query jika semua gagal
                        $products = $this->getProductsDirect($db, $search, $category, $limit, $offset);
                    }
                }
            } else {
                // Direct query
                $products = $this->getProductsDirect($db, $search, $category, $limit, $offset);
            }
            
            // FIX KHUSUS: Jika halaman 1 kosong, coba ambil tanpa filter
            if (empty($products) && $page == 1) {
                // Coba ambil 6 produk pertama saja (tanpa OFFSET)
                $query = "SELECT * FROM products ORDER BY id ASC LIMIT :limit";
                $stmt = $db->prepare($query);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Get total count
            $total = 0;
            if (method_exists($productModel, 'countProducts')) {
                $total = $productModel->countProducts($search, $category);
            } else {
                $total = $this->countProductsDirect($db, $search, $category);
            }
            
            // Get categories
            $categories = $this->getCategoriesDirect($db);
            
            // FIX: Pastikan total_pages minimal 1
            $total_pages = max(1, ceil($total / $limit));
            
            // Build result array
            $result = [
                'products' => $products ?: [],
                'total' => $total,
                'total_pages' => $total_pages,
                'page' => $page,
                'limit' => $limit
            ];
            
        } catch (Exception $e) {
            // Log error
            error_log("HomeController Error: " . $e->getMessage());
            
            // Tetap tampilkan halaman dengan data kosong
            $result = ['products' => [], 'total' => 0, 'total_pages' => 1, 'page' => 1, 'limit' => 6];
            $categories = [];
        }
        
        // Tampilkan halaman tanpa debug output
        $this->showHomePage($base_url, $result, $categories);
    }
    
    // ===== HELPER METHODS =====
    
    private function getProductsDirect($db, $search, $category, $limit, $offset) {
        try {
            $query = "SELECT * FROM products WHERE 1=1";
            $params = [];
            
            if (!empty($search)) {
                $query .= " AND (name LIKE :search OR description LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($category)) {
                $query .= " AND category = :category";
                $params[':category'] = $category;
            }
            
            // ORDER BY penting agar konsisten
            $query .= " ORDER BY id ASC";
            
            // LIMIT dan OFFSET
            $query .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
            
            $stmt = $db->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $paramType = ($key == ':limit' || $key == ':offset') ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $paramType);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Direct query error: " . $e->getMessage());
            
            // Fallback: query sederhana
            $query = "SELECT * FROM products LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    private function countProductsDirect($db, $search, $category) {
        try {
            $query = "SELECT COUNT(*) as total FROM products WHERE 1=1";
            $params = [];
            
            if (!empty($search)) {
                $query .= " AND (name LIKE :search OR description LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($category)) {
                $query .= " AND category = :category";
                $params[':category'] = $category;
            }
            
            $stmt = $db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }
    
    private function getCategoriesDirect($db) {
        try {
            $query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category";
            $stmt = $db->query($query);
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (Exception $e) {
            error_log("Categories error: " . $e->getMessage());
            return [];
        }
    }
    
    private function showHomePage($base_url, $result, $categories) {
        // Ekstrak variabel dari $result
        $products = $result['products'];
        $total_pages = $result['total_pages'];
        $page = $result['page'];
        $limit = $result['limit'];
        
        // Dapatkan parameter GET untuk form
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Kedai Kopi Titik Temu - Kopi Terbaik untuk Hari Anda</title>
            
            <!-- Bootstrap 5 -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            
            <!-- Favicon -->
            <link rel="icon" type="image/x-icon" href="<?php echo $base_url; ?>assets/favicon.ico">
            
            <style>
                /* Coffee Theme Colors */
                :root {
                    --coffee-dark: #3E2723;
                    --coffee-medium: #6F4E37;
                    --coffee-light: #8B6B61;
                    --coffee-gold: #C9A66B;
                    --coffee-cream: #F8F5F0;
                    --light-cream: #FFFBF5;
                }
                
                body {
                    background-color: var(--coffee-cream);
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    color: #333;
                }
                
                /* Navigation */
                .navbar {
                    background-color: white !important;
                    box-shadow: 0 2px 15px rgba(111, 78, 55, 0.1);
                    padding: 15px 0;
                }
                
                .navbar-brand {
                    color: var(--coffee-medium) !important;
                    font-weight: 700;
                    font-size: 1.5rem;
                }
                
                .navbar-brand i {
                    color: var(--coffee-gold);
                }
                
                .nav-link {
                    color: var(--coffee-medium) !important;
                    font-weight: 500;
                    padding: 8px 15px !important;
                    border-radius: 5px;
                    transition: all 0.3s;
                }
                
                .nav-link:hover, .nav-link.active {
                    background-color: rgba(201, 166, 107, 0.1);
                    color: var(--coffee-gold) !important;
                }
                
                /* Buttons */
                .btn-coffee {
                    background-color: var(--coffee-medium);
                    color: white;
                    border: none;
                    padding: 10px 25px;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: all 0.3s;
                }
                
                .btn-coffee:hover {
                    background-color: var(--coffee-dark);
                    color: white;
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(111, 78, 55, 0.3);
                }
                
                .btn-outline-coffee {
                    color: var(--coffee-medium);
                    border: 2px solid var(--coffee-medium);
                    background: transparent;
                    padding: 8px 20px;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: all 0.3s;
                }
                
                .btn-outline-coffee:hover {
                    background-color: var(--coffee-medium);
                    color: white;
                }
                
                /* Hero Section */
                .hero {
                    background: linear-gradient(rgba(62, 39, 35, 0.85), rgba(111, 78, 55, 0.85)), 
                                url('https://images.unsplash.com/photo-1498804103079-a6351b050096?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');
                    background-size: cover;
                    background-position: center;
                    background-attachment: fixed;
                    color: white;
                    padding: 100px 0;
                    margin-bottom: 60px;
                    border-radius: 0 0 20px 20px;
                    text-align: center;
                }
                
                .hero h1 {
                    font-size: 3rem;
                    font-weight: 800;
                    margin-bottom: 20px;
                    text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
                }
                
                .hero p {
                    font-size: 1.2rem;
                    max-width: 700px;
                    margin: 0 auto 30px;
                    opacity: 0.9;
                }
                
                /* Cards */
                .product-card {
                    background: white;
                    border: none;
                    border-radius: 15px;
                    overflow: hidden;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
                    transition: all 0.4s;
                    height: 100%;
                }
                
                .product-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 15px 30px rgba(111, 78, 55, 0.2);
                }
                
                .product-img-container {
                    height: 220px;
                    overflow: hidden;
                }
                
                .product-img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s;
                }
                
                .product-card:hover .product-img {
                    transform: scale(1.05);
                }
                
                .card-body {
                    padding: 25px;
                }
                
                .category-badge {
                    background-color: var(--coffee-gold);
                    color: var(--coffee-dark);
                    font-size: 0.8rem;
                    font-weight: 600;
                    padding: 5px 12px;
                    border-radius: 20px;
                    display: inline-block;
                    margin-bottom: 15px;
                }
                
                .product-title {
                    color: var(--coffee-dark);
                    font-weight: 700;
                    font-size: 1.3rem;
                    margin-bottom: 10px;
                    line-height: 1.4;
                }
                
                .product-description {
                    color: #666;
                    font-size: 0.95rem;
                    line-height: 1.6;
                    margin-bottom: 20px;
                }
                
                .price {
                    color: var(--coffee-medium);
                    font-weight: 800;
                    font-size: 1.4rem;
                    margin: 0;
                }
                
                .stock-badge {
                    font-size: 0.85rem;
                    padding: 5px 12px;
                    border-radius: 15px;
                }
                
                .stock-low {
                    background-color: #ffeaea;
                    color: #d32f2f;
                }
                
                .stock-ok {
                    background-color: #e8f5e9;
                    color: #2e7d32;
                }
                
                /* Search & Filter */
                .search-box {
                    max-width: 800px;
                    margin: 0 auto 40px;
                }
                
                .search-input {
                    border: 2px solid var(--coffee-light);
                    border-radius: 10px;
                    padding: 12px 20px;
                    font-size: 1rem;
                }
                
                .search-input:focus {
                    border-color: var(--coffee-gold);
                    box-shadow: 0 0 0 0.25rem rgba(201, 166, 107, 0.25);
                }
                
                .filter-select {
                    border: 2px solid var(--coffee-light);
                    border-radius: 10px;
                    padding: 12px 15px;
                    font-size: 1rem;
                    color: var(--coffee-medium);
                    background-color: white;
                }
                
                /* Section Titles */
                .section-title {
                    text-align: center;
                    color: var(--coffee-dark);
                    font-weight: 800;
                    font-size: 2.2rem;
                    margin-bottom: 50px;
                    position: relative;
                }
                
                .section-title:after {
                    content: '';
                    position: absolute;
                    width: 80px;
                    height: 4px;
                    background: var(--coffee-gold);
                    bottom: -15px;
                    left: 50%;
                    transform: translateX(-50%);
                    border-radius: 2px;
                }
                
                /* Pagination */
                .pagination {
                    margin-top: 50px;
                    justify-content: center;
                }
                
                .page-link {
                    color: var(--coffee-medium);
                    border: 1px solid #dee2e6;
                    padding: 10px 18px;
                    margin: 0 5px;
                    border-radius: 8px !important;
                    font-weight: 600;
                }
                
                .page-item.active .page-link {
                    background-color: var(--coffee-medium);
                    border-color: var(--coffee-medium);
                    color: white;
                }
                
                .page-link:hover {
                    color: var(--coffee-dark);
                    background-color: rgba(201, 166, 107, 0.1);
                    border-color: var(--coffee-gold);
                }
                
                /* Footer */
                footer {
                    background-color: var(--coffee-dark);
                    color: white;
                    margin-top: 80px;
                    padding: 60px 0 30px;
                }
                
                .footer-logo {
                    font-size: 1.8rem;
                    font-weight: 700;
                    color: var(--coffee-gold);
                    margin-bottom: 20px;
                }
                
                .footer-links h5 {
                    color: var(--coffee-gold);
                    margin-bottom: 20px;
                    font-size: 1.2rem;
                }
                
                .footer-links ul {
                    list-style: none;
                    padding: 0;
                }
                
                .footer-links li {
                    margin-bottom: 10px;
                }
                
                .footer-links a {
                    color: #ddd;
                    text-decoration: none;
                    transition: color 0.3s;
                }
                
                .footer-links a:hover {
                    color: var(--coffee-gold);
                }
                
                .copyright {
                    text-align: center;
                    padding-top: 30px;
                    margin-top: 30px;
                    border-top: 1px solid rgba(255,255,255,0.1);
                    color: #aaa;
                    font-size: 0.9rem;
                }
                
                /* Empty State */
                .empty-state {
                    text-align: center;
                    padding: 60px 20px;
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
                }
                
                .empty-state-icon {
                    font-size: 4rem;
                    color: var(--coffee-light);
                    margin-bottom: 20px;
                }
                
                /* Admin Badge */
                .admin-badge {
                    background: linear-gradient(135deg, #FFD700, #FFA500);
                    color: #5D4037;
                    font-weight: 700;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-size: 0.85rem;
                }
                
                /* Responsive */
                @media (max-width: 768px) {
                    .hero {
                        padding: 60px 20px;
                    }
                    
                    .hero h1 {
                        font-size: 2.2rem;
                    }
                    
                    .section-title {
                        font-size: 1.8rem;
                    }
                    
                    .product-img-container {
                        height: 180px;
                    }
                }
            </style>
        </head>
        <body>
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light sticky-top">
                <div class="container">
                    <a class="navbar-brand" href="<?php echo $base_url; ?>">
                        <i class="fas fa-coffee me-2"></i>Kedai Kopi Titik Temu
                    </a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto me-3">
                            <li class="nav-item">
                                <a class="nav-link active" href="<?php echo $base_url; ?>">Beranda</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#products">Menu</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#about">Tentang</a>
                            </li>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link admin-badge" href="<?php echo $base_url; ?>admin/dashboard">
                                    <i class="fas fa-crown me-1"></i>Admin Panel
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="d-flex align-items-center">
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <div class="me-3">
                                    <span class="text-coffee">
                                        <i class="fas fa-user-circle me-1"></i>
                                        <span class="fw-medium"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                                    </span>
                                </div>
                                <a href="<?php echo $base_url; ?>auth/logout" class="btn btn-outline-coffee">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a>
                            <?php else: ?>
                                <a href="<?php echo $base_url; ?>auth/login" class="btn btn-outline-coffee me-2">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                                <a href="<?php echo $base_url; ?>auth/register" class="btn btn-coffee">
                                    <i class="fas fa-user-plus me-1"></i>Daftar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Hero Section -->
            <section class="hero">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-4">Selamat Datang di Kedai Kopi Titik Temu</h1>
                    <p class="lead mb-5">Rasakan kenikmatan kopi terbaik dengan racikan spesial dari barista profesional kami. Setiap cangkir dibuat dengan penuh cinta dan dedikasi.</p>
                    <a href="#products" class="btn btn-coffee btn-lg">
                        <i class="fas fa-mug-hot me-2"></i>Jelajahi Menu Kami
                    </a>
                </div>
            </section>
            
            <!-- Main Content -->
            <main class="container">
                <!-- Products Section -->
                <section id="products" class="mb-5">
                    <h2 class="section-title">Menu Andalan Kami</h2>
                    
                    <!-- Search and Filter -->
                    <div class="row search-box">
                        <div class="col-md-6 mb-3">
                            <form method="GET" action="" class="d-flex">
                                <input type="text" name="search" class="form-control search-input me-2" 
                                       placeholder="Cari produk favorit Anda..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-coffee" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 mb-3">
                            <form method="GET" action="">
                                <select name="category" class="form-select filter-select" onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" 
                                                <?php echo ($category == $cat) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Products Grid -->
                    <div class="row g-4">
                        <?php if(empty($products)): ?>
                            <div class="col-12">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-coffee"></i>
                                    </div>
                                    <h3 class="mb-3">Produk Tidak Ditemukan</h3>
                                    <p class="text-muted mb-4">Maaf, tidak ada produk yang sesuai dengan pencarian Anda.</p>
                                    <a href="<?php echo $base_url; ?>" class="btn btn-coffee">
                                        <i class="fas fa-redo me-1"></i>Tampilkan Semua Produk
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach($products as $product): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-img-container">
                                        <?php if(isset($product['image']) && !empty($product['image'])): ?>
                                            <img src="<?php echo $base_url; ?>assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                                 class="product-img" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                            <img src="https://images.unsplash.com/photo-1498804103079-a6351b050096?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                                 class="product-img" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="category-badge">
                                            <?php echo htmlspecialchars($product['category'] ?? 'Uncategorized'); ?>
                                        </div>
                                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="product-description">
                                            <?php 
                                            $desc = $product['description'] ?? 'Minuman kopi berkualitas tinggi.';
                                            echo htmlspecialchars(substr($desc, 0, 100));
                                            if (strlen($desc) > 100) echo '...';
                                            ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div>
                                                <p class="price mb-0">Rp <?php echo number_format($product['price'] ?? 0, 0, ',', '.'); ?></p>
                                                <small class="text-muted">per porsi</small>
                                            </div>
                                            <div>
                                                <span class="stock-badge <?php echo (($product['stock'] ?? 0) < 10) ? 'stock-low' : 'stock-ok'; ?>">
                                                    <i class="fas fa-box me-1"></i>
                                                    Stok: <?php echo $product['stock'] ?? 0; ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Admin Actions -->
                                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <div class="mt-4 pt-3 border-top">
                                            <div class="d-flex gap-2">
                                                <a href="<?php echo $base_url; ?>admin/products?action=edit&id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-warning flex-fill">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>
                                                <a href="<?php echo $base_url; ?>admin/products?action=delete&id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-danger flex-fill"
                                                   onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </a>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination">
                            <?php if($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                    <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php 
                            // Tampilkan maksimal 5 nomor halaman
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            
                            for($i = $start; $i <= $end; $i++): 
                            ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                    Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <div class="text-center mt-2 text-muted">
                            Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?> 
                            (Total <?php echo $result['total']; ?> produk)
                        </div>
                    </nav>
                    <?php endif; ?>
                </section>
                
                <!-- About Section -->
                <section id="about" class="py-5 mt-5 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="section-title text-start">Tentang Kedai Kopi Titik Temu</h2>
                            <p class="lead text-coffee">
                                Kami adalah kedai kopi yang berkomitmen untuk menyajikan kopi terbaik dengan kualitas premium.
                            </p>
                            <p>
                                Setiap biji kopi dipilih dengan teliti dari perkebunan terbaik di Indonesia dan dipanggang dengan teknik khusus 
                                untuk menghasilkan cita rasa yang sempurna. Barista kami yang berpengalaman akan menyajikan kopi dengan penuh dedikasi.
                            </p>
                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-coffee-cream p-3 me-3">
                                                <i class="fas fa-coffee fa-lg text-coffee-medium"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">100+</h5>
                                                <small>Varietas Kopi</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-coffee-cream p-3 me-3">
                                                <i class="fas fa-users fa-lg text-coffee-medium"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">5,000+</h5>
                                                <small>Pelanggan Setia</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                 alt="Kedai Kopi Interior" class="img-fluid rounded-3 shadow">
                        </div>
                    </div>
                </section>
            </main>
            
            <!-- Footer -->
            <footer>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="footer-logo">
                                <i class="fas fa-coffee me-2"></i>Kedai Kopi Titik Temu
                            </div>
                            <p class="text-light">
                                Menyajikan kopi berkualitas tinggi dengan cita rasa autentik Indonesia. 
                                Setiap cangkir adalah karya seni.
                            </p>
                            <div class="mt-4">
                                <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                                <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                                <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#" class="text-light"><i class="fab fa-whatsapp fa-lg"></i></a>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-6 mb-4">
                            <div class="footer-links">
                                <h5>Menu</h5>
                                <ul>
                                    <li><a href="<?php echo $base_url; ?>">Beranda</a></li>
                                    <li><a href="#products">Produk</a></li>
                                    <li><a href="#about">Tentang Kami</a></li>
                                    <li><a href="#">Kontak</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="footer-links">
                                <h5>Kontak</h5>
                                <ul>
                                    <li><i class="fas fa-map-marker-alt me-2"></i>Jl. Kartini Raya No. 23, Bekasi</li>
                                    <li><i class="fas fa-phone me-2"></i>(021) 1234-5678</li>
                                    <li><i class="fas fa-envelope me-2"></i>info@kedaikopititiktemu.com</li>
                                    <li><i class="fas fa-clock me-2"></i>Buka: 08:00 - 22:00</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 mb-4">
                            <div class="footer-links">
                                <h5>Proyek UAS</h5>
                                <p class="text-light">
                                    <strong>Pemrograman Web</strong><br>
                                    Sistem Manajemen Kedai Kopi<br>
                                    Menggunakan PHP, MySQL, Bootstrap
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="copyright">
                        © <?php echo date('Y'); ?> Kedai Kopi Titik Temu. Semua hak dilindungi. | 
                        Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk UAS Pemrograman Web
                    </div>
                </div>
            </footer>
            
            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            
            <!-- Smooth Scroll -->
            <script>
                // Smooth scrolling for anchor links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href');
                        if(targetId === '#') return;
                        
                        const targetElement = document.querySelector(targetId);
                        if(targetElement) {
                            window.scrollTo({
                                top: targetElement.offsetTop - 80,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
                
                // Active nav link on scroll
                window.addEventListener('scroll', function() {
                    const sections = document.querySelectorAll('section[id]');
                    const navLinks = document.querySelectorAll('.nav-link');
                    
                    let current = '';
                    sections.forEach(section => {
                        const sectionTop = section.offsetTop;
                        const sectionHeight = section.clientHeight;
                        if(scrollY >= (sectionTop - 100)) {
                            current = section.getAttribute('id');
                        }
                    });
                    
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if(link.getAttribute('href') === `#${current}` || 
                           (current === '' && link.getAttribute('href') === '<?php echo $base_url; ?>')) {
                            link.classList.add('active');
                        }
                    });
                });
            </script>
        </body>
        </html>
        <?php
    }
}
?>