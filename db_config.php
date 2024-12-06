<?php
$host = 'localhost';
$dbname = 'Restaurants'; // Tên database của bạn
$username = 'root';  // Tài khoản MySQL
$password = '';      // Mật khẩu MySQL

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>
