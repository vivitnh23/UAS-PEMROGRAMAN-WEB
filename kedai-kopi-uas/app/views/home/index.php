<?php
$title = "Beranda";
include 'app/views/layout/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center py-5 mb-5" style="background: linear-gradient(rgba(111, 78, 55, 0.9), rgba(111, 78, 55, 0.8)), url('https://images.unsplash.com/photo-1498804103079-a6351b050096?ixlib=rb-4.0.3') center/cover; color: white; border-radius: 15px;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Selamat Datang di Kedai Kopi Jeje</h1>
        <p class="lead mb-4">Rasakan kenikmatan kopi terbaik dengan racikan spesial dari barista profesional kami.</p>
        <a href="#products" class="btn btn-light btn-lg px-4">
            <i class="fas fa-coffee me-2"></i>Lihat Menu
        </a>
    </div>
</section>

<!-- Products Section -->
<section id="products" class="mb-5">
    <h2 class="text-center mb-4" style="color: var(--primary-color);">Menu Kami</h2>
    
    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" action="/" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari produk..." value="<?php echo $search ?? ''; ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <div class="col-md-4">
            <form method="GET" action="/" class="d-flex">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="row g-4">
        <?php foreach($result['products'] as $product): ?>
        <div class="col-md-4 col-lg-4">
            <div class="card h-100">
                <img src="/assets/images/products/<?php echo $product['image']; ?>" 
                     class="card-img-top product-img" 
                     alt="<?php echo $product['name']; ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                <div class="card-body">
                    <span class="badge category-badge mb-2"><?php echo $product['category']; ?></span>
                    <h5 class="card-title"><?php echo $product['name']; ?></h5>
                    <p class="card-text"><?php echo substr($product['description'], 0, 100) . '...'; ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 text-primary">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                        <small class="text-muted">Stok: <?php echo $product['stock']; ?></small>
                    </div>
                </div>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="card-footer bg-transparent">
                    <a href="/admin/products?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="/admin/products?action=delete&id=<?php echo $product['id']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Yakin hapus produk?')">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if($result['total_pages'] > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <?php for($i = 1; $i <= $result['total_pages']; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="/?page=<?php echo $i; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.$search : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</section>

<!-- About Section -->
<section id="about" class="py-5" style="background-color: var(--accent-color); border-radius: 15px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 style="color: var(--dark-color);">Tentang Kedai Kopi Jeje</h2>
                <p class="lead">Kami adalah kedai kopi yang berdiri sejak 2015 dengan komitmen menyajikan kopi terbaik dari biji kopi pilihan.</p>
                <p>Dengan barista berpengalaman dan mesin kopi modern, kami mengolah setiap cangkir kopi dengan penuh cinta dan perhatian terhadap detail.</p>
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-clock me-3" style="color: var(--primary-color);"></i>
                        <div>
                            <h5 class="mb-0">Jam Operasional</h5>
                            <p class="mb-0">Senin - Minggu: 07:00 - 22:00 WIB</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-3" style="color: var(--primary-color);"></i>
                        <div>
                            <h5 class="mb-0">Lokasi</h5>
                            <p class="mb-0">Jl. Kopi Raya No. 123, Kota Jeje</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=700&q=80" 
                     alt="Kedai Kopi" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/layout/footer.php'; ?>