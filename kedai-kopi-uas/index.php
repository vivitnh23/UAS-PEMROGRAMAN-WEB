<?php
// index.php - SIMPLE ROUTING VERSION
session_start();

// Base URL untuk semua link
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/kedai-kopi-uas/';

// Get requested path
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Remove base path
$path = str_replace('/kedai-kopi-uas/', '', $request_uri);
$path = trim($path, '/');

// Default to home
if (empty($path)) {
    $path = 'home';
}

// Simple routing table
$routes = [
    'home' => ['controller' => 'HomeController', 'method' => 'index'],
    'auth/login' => ['controller' => 'AuthController', 'method' => 'login'],
    'auth/register' => ['controller' => 'AuthController', 'method' => 'register'],
    'auth/logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    'admin/dashboard' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/products' => ['controller' => 'AdminController', 'method' => 'products'],
];

// Find matching route
$controller_name = 'HomeController';
$method_name = 'index';

if (isset($routes[$path])) {
    $controller_name = $routes[$path]['controller'];
    $method_name = $routes[$path]['method'];
}

// Include required files
require_once 'app/config/Database.php';

// Include the controller
$controller_file = 'app/controllers/' . $controller_name . '.php';
if (file_exists($controller_file)) {
    require_once $controller_file;
    
    // Create controller instance
    $controller = new $controller_name();
    
    // Check if method exists
    if (method_exists($controller, $method_name)) {
        // Call the method
        $controller->$method_name();
    } else {
        show_error_404();
    }
} else {
    show_error_404();
}

function show_error_404() {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>404 Not Found - Kedai Kopi Jeje</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: #f8f9fa; }
            .error-container { margin-top: 100px; }
        </style>
    </head>
    <body>
        <div class="container error-container text-center">
            <h1 class="display-1">404</h1>
            <h2 class="mb-4">Halaman Tidak Ditemukan</h2>
            <p class="lead mb-4">Maaf, halaman yang Anda cari tidak ditemukan.</p>
            <a href="/kedai-kopi-uas/" class="btn btn-primary btn-lg">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
            
            <div class="mt-5">
                <h5>Debug Info:</h5>
                <p>Requested Path: <?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></p>
                <p>Available Routes:</p>
                <ul class="list-inline">
                    <li class="list-inline-item"><a href="/kedai-kopi-uas/">Home</a></li>
                    <li class="list-inline-item"><a href="/kedai-kopi-uas/auth/login">Login</a></li>
                    <li class="list-inline-item"><a href="/kedai-kopi-uas/auth/register">Register</a></li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>