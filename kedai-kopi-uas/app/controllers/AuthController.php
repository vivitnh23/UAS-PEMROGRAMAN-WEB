<?php
// PASTIKAN ADA DI BARIS PALING ATAS
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class AuthController {
    
    // ===== METHOD LOGIN YANG BARU (DENGAN DEBUG) =====
    public function login() {
        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                echo "<div style='background: #f0f0f0; padding: 20px; margin: 20px; border: 1px solid red;'>";
                echo "<h3>=== DEBUG LOGIN ===</h3>";
                
                echo "<strong>POST Data:</strong><br>";
                echo "Username: " . $_POST['username'] . "<br>";
                echo "Password: " . $_POST['password'] . "<br><br>";
                
                require_once 'app/config/Database.php';
                require_once 'app/models/User.php';
                
                $database = new Database();
                $db = $database->getConnection();
                
                echo "<strong>Database Connection:</strong> " . ($db ? "SUCCESS" : "FAILED") . "<br>";
                
                $user = new User($db);
                $user->username = $_POST['username'];
                
                echo "<strong>Username ke model:</strong> " . $user->username . "<br><br>";
                
                $stmt = $user->login();
                
                echo "<strong>Row Count:</strong> " . $stmt->rowCount() . "<br>";
                
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    echo "<strong>Data dari DB:</strong><br>";
                    echo "ID: " . $row['id'] . "<br>";
                    echo "Username: " . $row['username'] . "<br>";
                    echo "Password Hash: " . $row['password'] . "<br>";
                    echo "Role: " . $row['role'] . "<br><br>";
                    
                    $verify = password_verify($_POST['password'], $row['password']);
                    echo "<strong>Password Verify Result:</strong> " . ($verify ? "TRUE" : "FALSE") . "<br>";
                    
                    if ($verify) {
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['role'] = $row['role'];
                        
                        echo "<strong>Session Set:</strong><br>";
                        echo "user_id: " . $_SESSION['user_id'] . "<br>";
                        echo "username: " . $_SESSION['username'] . "<br>";
                        echo "role: " . $_SESSION['role'] . "<br>";
                        
                        echo "<br><strong>Redirecting...</strong>";
                        echo "</div>";
                        
                        // Redirect based on role
                        if ($row['role'] === 'admin') {
                            header("Location: " . $base_url . "admin/dashboard");
                        } else {
                            header("Location: " . $base_url);
                        }
                        exit();
                    } else {
                        $error = "Password salah!";
                    }
                } else {
                    $error = "Username tidak ditemukan!";
                }
                
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<strong>ERROR:</strong> " . $e->getMessage() . "<br>";
                $error = "System error: " . $e->getMessage();
            }
        }
        
        // Show login form
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login - Kedai Kopi Jeje</title>
            
            <!-- Bootstrap 5 -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            
            <!-- Font Awesome -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            
            <!-- Google Fonts -->
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
            
            <!-- Custom CSS -->
            <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
            
            <style>
                .login-page {
                    background: linear-gradient(135deg, #6F4E37 0%, #3E2723 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                
                .login-card {
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                    overflow: hidden;
                    max-width: 450px;
                    width: 100%;
                }
                
                .login-header {
                    background: linear-gradient(135deg, #6F4E37 0%, #8B6B61 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                
                .login-body {
                    padding: 30px;
                }
                
                .coffee-icon {
                    font-size: 3rem;
                    color: #C9A66B;
                    margin-bottom: 15px;
                }
            </style>
        </head>
        <body class="login-page">
            <div class="login-card">
                <div class="login-header">
                    <div class="coffee-icon">
                        <i class="fas fa-coffee"></i>
                    </div>
                    <h2>Masuk ke Akun Anda</h2>
                    <p class="mb-0">Selamat datang kembali di Kedai Kopi Jeje</p>
                </div>
                
                <div class="login-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Masukkan username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>Default password: 123456
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-2">Belum punya akun? 
                            <a href="<?php echo $base_url; ?>auth/register" class="text-primary fw-bold">
                                Daftar disini
                            </a>
                        </p>
                        
                        <div class="card bg-light mt-3">
                            <div class="card-body py-2">
                                <small class="text-muted">
                                    <i class="fas fa-key me-1"></i>Akun Test:<br>
                                    <strong>Admin:</strong> admin / 123456<br>
                                    <strong>User:</strong> user / 123456
                                </small>
                            </div>
                        </div>
                        
                        <a href="<?php echo $base_url; ?>" class="btn btn-outline-secondary btn-sm mt-3">
                            <i class="fas fa-home me-1"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    public function register() {
        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                require_once 'app/config/Database.php';
                require_once 'app/models/User.php';
                
                $database = new Database();
                $db = $database->getConnection();
                
                $user = new User($db);
                $user->username = $_POST['username'];
                $user->email = $_POST['email'];
                $user->password = $_POST['password'];
                
                // Validation
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    $error = "Password tidak sama!";
                } elseif (strlen($_POST['password']) < 6) {
                    $error = "Password minimal 6 karakter!";
                } else {
                    // Check if user exists
                    if ($user->checkExists()) {
                        $error = "Username atau email sudah terdaftar!";
                    } else {
                        if ($user->register()) {
                            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
                            header("Location: " . $base_url . "auth/login");
                            exit();
                        } else {
                            $error = "Registrasi gagal!";
                        }
                    }
                }
            } catch (Exception $e) {
                $error = "System error: " . $e->getMessage();
            }
        }
        
        // Show registration form with coffee theme
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Daftar - Kedai Kopi Jeje</title>
            
            <!-- Bootstrap 5 -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            
            <!-- Font Awesome -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            
            <!-- Google Fonts -->
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
            
            <!-- Custom CSS -->
            <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
            
            <style>
                .register-page {
                    background: linear-gradient(135deg, #6F4E37 0%, #3E2723 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                
                .register-card {
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                    overflow: hidden;
                    max-width: 500px;
                    width: 100%;
                }
                
                .register-header {
                    background: linear-gradient(135deg, #8B6B61 0%, #6F4E37 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                
                .register-body {
                    padding: 30px;
                }
                
                .coffee-icon {
                    font-size: 3rem;
                    color: #C9A66B;
                    margin-bottom: 15px;
                }
            </style>
        </head>
        <body class="register-page">
            <div class="register-card">
                <div class="register-header">
                    <div class="coffee-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h2>Buat Akun Baru</h2>
                    <p class="mb-0">Bergabunglah dengan komunitas pecinta kopi</p>
                </div>
                
                <div class="register-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Masukkan username" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="nama@email.com" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Minimal 6 karakter" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Konfirmasi Password
                                </label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-3">Sudah punya akun? 
                            <a href="<?php echo $base_url; ?>auth/login" class="text-primary fw-bold">
                                Login disini
                            </a>
                        </p>
                        
                        <a href="<?php echo $base_url; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-1"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    public function logout() {
        session_destroy();
        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';
        header("Location: " . $base_url);
        exit();
    }
}
?>