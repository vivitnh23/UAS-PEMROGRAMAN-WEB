<?php
class Product {
    private $conn;
    private $table = 'products';
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $category;
    public $image;
    public $stock;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    // Tambahkan di dalam class Product, setelah method getAll() atau getProducts()
    public function readAll() {
    // Query untuk mengambil produk
    $query = "SELECT id, name, description, price, category, image, stock, created_at 
              FROM products 
              ORDER BY created_at DESC 
              LIMIT 8";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    // Return hasil sebagai array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    // Get all products with filters and pagination
    public function getProducts($search = '', $category = '', $sort = 'newest', $limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];
        
        // Add search filter
        if (!empty($search)) {
            $query .= " AND (name LIKE ? OR description LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        // Add category filter
        if (!empty($category)) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        // Add sorting
        switch ($sort) {
            case 'oldest':
                $query .= " ORDER BY created_at ASC";
                break;
            case 'price_asc':
                $query .= " ORDER BY price ASC";
                break;
            case 'price_desc':
                $query .= " ORDER BY price DESC";
                break;
            default: // newest
                $query .= " ORDER BY created_at DESC";
                break;
        }
        
        // Add pagination
            $query .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->conn->prepare($query);

            // Bind parameters dengan tipe data yang tepat
            foreach ($params as $key => $value) {
                // Cek apakah ini parameter untuk LIMIT/OFFSET
                if ($key >= count($params) - 2) { // 2 parameter terakhir adalah LIMIT dan OFFSET
                    $stmt->bindValue($key + 1, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key + 1, $value);
                }
}
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Count total products with filters
    public function countProducts($search = '', $category = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (name LIKE ? OR description LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        if (!empty($category)) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Get unique categories
    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table . " ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $row['category'];
        }
        return $categories;
    }
    
    // Get single product by ID
    public function getById() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }
    
    // Create new product
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name = :name, 
                      description = :description, 
                      price = :price,
                      category = :category,
                      image = :image,
                      stock = :stock";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        
        // Bind
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':stock', $this->stock);
        
        return $stmt->execute();
    }

    
    // Update product
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, 
                      description = :description, 
                      price = :price,
                      category = :category,
                      image = :image,
                      stock = :stock,
                      created_at = created_at
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        
        // Bind
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    // Delete product
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>