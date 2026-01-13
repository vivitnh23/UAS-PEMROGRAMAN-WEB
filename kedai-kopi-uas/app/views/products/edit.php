<?php
$title = "Edit Produk";
include 'app/views/layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-edit me-2"></i>Edit Produk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/kedai-kopi-uas/admin/products" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/kedai-kopi-uas/admin/products/edit/<?php echo $product['id']; ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="Hot Coffee" <?php echo $product['category'] == 'Hot Coffee' ? 'selected' : ''; ?>>Hot Coffee</option>
                            <option value="Cold Coffee" <?php echo $product['category'] == 'Cold Coffee' ? 'selected' : ''; ?>>Cold Coffee</option>
                            <option value="Tea" <?php echo $product['category'] == 'Tea' ? 'selected' : ''; ?>>Tea</option>
                            <option value="Food" <?php echo $product['category'] == 'Food' ? 'selected' : ''; ?>>Food</option>
                            <option value="Dessert" <?php echo $product['category'] == 'Dessert' ? 'selected' : ''; ?>>Dessert</option>
                            <option value="Snack" <?php echo $product['category'] == 'Snack' ? 'selected' : ''; ?>>Snack</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="<?php echo $product['price']; ?>" min="0" step="100" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stock" name="stock" 
                               value="<?php echo $product['stock']; ?>" min="0" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Produk</label>
                        <?php if($product['image']): ?>
                            <div class="mb-2">
                                <img src="/kedai-kopi-uas/assets/images/products/<?php echo $product['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     width="100" style="border-radius: 5px;">
                                <br>
                                <small class="text-muted">Gambar saat ini</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary me-2">
                    <i class="fas fa-undo me-1"></i>Reset
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Update Produk
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'app/views/layout/footer.php'; ?>