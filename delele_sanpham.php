<?php
require 'db_config.php';

// Kiểm tra session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền người dùng
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'QuanLy') {
    $_SESSION['message'] = "<h1>Bạn không có quyền xóa sản phẩm!</h1><a href='Sanpham_Ad.php'>Quay lại</a>";
    header("Location: Sanpham_Ad.php");
    exit();
}

// Lấy ID sản phẩm cần xóa
$productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra ID sản phẩm hợp lệ
if ($productID <= 0) {
    $_SESSION['message'] = "<h1>ID sản phẩm không hợp lệ!</h1><a href='Sanpham_Ad.php'>Quay lại</a>";
    header("Location: Sanpham_Ad.php");
    exit();
}

// Lấy thông tin sản phẩm từ bảng SanPham
$querySelect = "SELECT * FROM SanPham WHERE SanPhamID = ?";
$stmtSelect = $conn->prepare($querySelect);
$stmtSelect->execute([$productID]);
$product = $stmtSelect->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = "<h1>Sản phẩm không tồn tại!</h1><a href='Sanpham_Ad.php'>Quay lại</a>";
    header("Location: Sanpham_Ad.php");
    exit();
}

// Chuyển sản phẩm vào bảng SanPhamDaXoa
$queryInsert = "INSERT INTO SanPhamDaXoa (SanPhamID, TenSanPham, Gia, HinhAnh, TrangThai, QuanLyXoa)
                VALUES (:id, :ten, :gia, :hinhAnh, :trangThai, :quanLy)";
$stmtInsert = $conn->prepare($queryInsert);
$stmtInsert->bindParam(':id', $product['SanPhamID']);
$stmtInsert->bindParam(':ten', $product['TenSanPham']);
$stmtInsert->bindParam(':gia', $product['Gia']);
$stmtInsert->bindParam(':hinhAnh', $product['HinhAnh']);
$stmtInsert->bindParam(':trangThai', $product['TrangThai']);
$stmtInsert->bindParam(':quanLy', $_SESSION['username']); // Ghi lại người đã xóa

if ($stmtInsert->execute()) {
    // Xóa sản phẩm khỏi bảng SanPham
    $queryDelete = "DELETE FROM SanPham WHERE SanPhamID = ?";
    $stmtDelete = $conn->prepare($queryDelete);
    $stmtDelete->execute([$productID]);

    $_SESSION['message'] = "<h1>Sản phẩm đã được chuyển vào danh sách đã xóa thành công!</h1>";
    header("Location: Sanpham_Ad.php");
    exit();
} else {
    $_SESSION['message'] = "<h1>Chuyển sản phẩm vào danh sách đã xóa thất bại!</h1>";
    header("Location: Sanpham_Ad.php");
    exit();
}
?>
