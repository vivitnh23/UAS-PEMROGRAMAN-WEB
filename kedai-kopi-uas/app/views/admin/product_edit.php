<?php
$title = "Edit Produk";
include 'app/views/layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-edit me-2"></i>Edit Produk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/products" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Form Edit Produk</h5>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="/admin/products?action=edit&id=<?php echo $productData['id']; ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $productData['name']; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Hot Coffee" <?php echo ($productData['category'] === 'Hot Coffee') ? 'selected' : ''; ?>>Hot Coffee</option>
                                <option value="Cold Coffee" <?php echo ($productData['category'] === 'Cold Coffee') ? 'selected' : ''; ?>>Cold Coffee</option>
                                <option value="Tea" <?php echo ($productData['category'] === 'Tea') ? 'selected' : ''; ?>>Tea</option>
                                <option value="Dessert" <?php echo ($productData['category'] === 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                                <option value="Food" <?php echo ($productData['category'] === 'Food') ? 'selected' : ''; ?>>Food</option>
                                <option value="Snack" <?php echo ($productData['category'] === 'Snack') ? 'selected' : ''; ?>>Snack</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $productData['description']; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="price" name="price" value="<?php echo $productData['price']; ?>" min="0" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $productData['stock']; ?>" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Produk</label>
                        <?php if($productData['image']): ?>
                        <div class="mb-2">
                            <img src="/assets/images/products/<?php echo $productData['image']; ?>" 
                                 alt="<?php echo $productData['name']; ?>" 
                                 width="150" 
                                 height="150" 
                                 style="object-fit: cover; border-radius: 5px;"
                                 onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'">
                            <p class="text-muted mt-1">Gambar saat ini</p>
                        </div>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar</div>
                        <div class="mt-2" id="imagePreview"></div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary me-md-2">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.style.borderRadius = '5px';
            img.style.marginTop = '10px';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(this.files[0]);
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const price = document.getElementById('price').value;
    const stock = document.getElementById('stock').value;
    
    if (price < 0) {
        e.preventDefault();
        alert('Harga tidak boleh negatif!');
        return false;
    }
    
    if (stock < 0) {
        e.preventDefault();
        alert('Stok tidak boleh negatif!');
        return false;
    }
});
</script>

<?php include 'app/views/layout/footer.php'; ?>