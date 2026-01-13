<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login user - FIXED VERSION
    public function login() {
    $query = "SELECT id, username, password, role, email, created_at 
              FROM " . $this->table . " 
              WHERE username = :username 
              LIMIT 1";
    
    $stmt = $this->conn->prepare($query);
    
    // Pastikan $this->username sudah di-set dari controller
    $stmt->bindParam(':username', $this->username);
    
    $stmt->execute();
    return $stmt;
}

    // Register user
    public function register($username, $email, $password) {
        $query = "INSERT INTO " . $this->table . " 
                  SET username = :username, email = :email, 
                  password = :password, role = 'user'";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        
        return $stmt->execute();
    }

    // Get user by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>