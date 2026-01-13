<?php
class Database {
    // SESUAIKAN NAMA DATABASE!
    private $host = "localhost";
    private $db_name = "kedai_kopi_uas";  // ← INI YANG PERLU DIPERBAIKI
    private $username = "root";
    private $password = "";  // Untuk XAMPP biasanya kosong
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            echo "<!-- DEBUG: Database connected successfully -->";
        } catch(PDOException $exception) {
            // Tampilkan error lebih detail
            echo "<!-- DEBUG: Database ERROR: " . $exception->getMessage() . " -->";
            die("Database connection failed. Please check your configuration.");
        }
        return $this->conn;
    }
}
?>