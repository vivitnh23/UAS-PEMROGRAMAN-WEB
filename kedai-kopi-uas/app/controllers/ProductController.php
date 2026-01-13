<?php
class ProductController {
    
    public function index() {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: /kedai-kopi-uas/auth/login");
            exit();
        }
        
        require_once 'app/config/Database.php';
        require_once 'app/models/Product.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        $productModel = new Product($db);
        
        // Pagination settings
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Filter parameters
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        
        // Get products with filters
        $result = $productModel->getProducts($search, $category, $sort, $limit, $offset);
        $total_products = $productModel->countProducts($search, $category);
        
        // Prepare data for view
        $data = [
            'products' => $result,
            'total' => $total_products,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total_products / $limit),
            'categories' => $productModel->getCategories()
        ];
        
        // Include view and pass data
        extract($data); // Creates variables: $products, $total, $page, etc.
        include 'app/views/admin/products/index.php';
    }
    
    public function create() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: /kedai-kopi-uas/auth/login");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'app/config/Database.php';
            require_once 'app/models/Product.php';
            
            $database = new Database();
            $db = $database->getConnection();
            
            $product = new Product($db);
            
            $product->name = $_POST['name'];
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            $product->category = $_POST['category'];
            $product->stock = $_POST['stock'];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/kedai-kopi-uas/assets/images/products/';
                $target_file = $target_dir . $image_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $product->image = $image_name;
                }
            } else {
                $product->image = 'default.jpg';
            }
            
            if ($product->create()) {
                $_SESSION['success'] = "Produk berhasil ditambahkan!";
                header("Location: /kedai-kopi-uas/admin/products");
                exit();
            } else {
                $_SESSION['error'] = "Gagal menambahkan produk!";
            }
        }
        
        include 'app/views/admin/products/create.php';
    }
    
    public function edit($id) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: /kedai-kopi-uas/auth/login");
            exit();
        }
        
        require_once 'app/config/Database.php';
        require_once 'app/models/Product.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        $product = new Product($db);
        $product->id = $id;
        
        // Get product data
        $stmt = $product->getById();
        $product_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product_data) {
            $_SESSION['error'] = "Produk tidak ditemukan!";
            header("Location: /kedai-kopi-uas/admin/products");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product->name = $_POST['name'];
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            $product->category = $_POST['category'];
            $product->stock = $_POST['stock'];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/kedai-kopi-uas/assets/images/products/';
                $target_file = $target_dir . $image_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Delete old image if exists
                    if ($product_data['image'] && $product_data['image'] !== 'default.jpg') {
                        $old_image = $target_dir . $product_data['image'];
                        if (file_exists($old_image)) {
                            unlink($old_image);
                        }
                    }
                    $product->image = $image_name;
                }
            } else {
                $product->image = $product_data['image'];
            }
            
            if ($product->update()) {
                $_SESSION['success'] = "Produk berhasil diperbarui!";
                header("Location: /kedai-kopi-uas/admin/products");
                exit();
            } else {
                $_SESSION['error'] = "Gagal memperbarui produk!";
            }
        }
        
        $data['product'] = $product_data;
        extract($data);
        include 'app/views/admin/products/edit.php';
    }
    
    public function delete($id) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: /kedai-kopi-uas/auth/login");
            exit();
        }
        
        require_once 'app/config/Database.php';
        require_once 'app/models/Product.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        $product = new Product($db);
        $product->id = $id;
        
        // Get product image first
        $stmt = $product->getById();
        $product_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product->delete()) {
            // Delete image file if exists
            if ($product_data['image'] && $product_data['image'] !== 'default.jpg') {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/kedai-kopi-uas/assets/images/products/' . $product_data['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            $_SESSION['success'] = "Produk berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus produk!";
        }
        
        header("Location: /kedai-kopi-uas/admin/products");
        exit();
    }
}
?>