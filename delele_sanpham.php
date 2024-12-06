<?php
// Kết nối cơ sở dữ liệu
require 'db_config.php';

// Kiểm tra nếu session chưa được bắt đầu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền người dùng, chỉ cho phép Quản lý xóa sản phẩm
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'QuanLy') {
    die("<h1>Bạn không có quyền xóa sản phẩm!</h1><a href='Sanpham_Ad.php'>Quay lại</a>");
}

// Lấy ID sản phẩm cần xóa
$productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra ID sản phẩm hợp lệ
if ($productID <= 0) {
    die("<h1>ID sản phẩm không hợp lệ!</h1><a href='Sanpham_Ad.php'>Quay lại</a>");
}

// Xóa sản phẩm khỏi cơ sở dữ liệu
$query = "DELETE FROM SanPham WHERE SanPhamID = ?";
$stmt = $conn->prepare($query);

if ($stmt->execute([$productID])) {
    header("Location: Sanpham_Ad.php?success=Xóa sản phẩm thành công!"); // Redirect với thông báo thành công
    exit();
} else {
    die("<h1>Xóa sản phẩm thất bại!</h1><a href='Sanpham_Ad.php'>Quay lại</a>");
}
?>
