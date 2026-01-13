<?php
// test_login.php
session_start();
echo "<pre>";

// 1. Test database connection
require_once 'app/config/Database.php';
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed!");
}
echo "1. Database: ✓ Connected\n";

// 2. Test query langsung
$username = 'admin';
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("User 'admin' not found in database!");
}
echo "2. Query: ✓ User found\n";

echo "3. User Data:\n";
print_r($row);

// 3. Test password
$password_test = '123456';
echo "\n4. Password Test:\n";
echo "Input: '{$password_test}'\n";
echo "Hash in DB: {$row['password']}\n";

$verify = password_verify($password_test, $row['password']);
echo "Verify Result: " . ($verify ? "✓ TRUE" : "✗ FALSE") . "\n";

// 4. Test alternative password
$test_hash = password_hash('123456', PASSWORD_BCRYPT);
echo "\n5. New Hash for '123456':\n";
echo $test_hash . "\n";
echo "Compare with DB: " . ($row['password'] === $test_hash ? "SAME" : "DIFFERENT") . "\n";

// 5. Manual verify
echo "\n6. Manual Check:\n";
echo "Hash starts with: " . substr($row['password'], 0, 7) . "\n";
echo "Hash length: " . strlen($row['password']) . " characters\n";

if (strlen($row['password']) < 50) {
    echo "⚠ WARNING: Hash too short! Should be 60 characters.\n";
}

echo "</pre>";
?>