<?php
session_start();
require 'db_config.php';

// Kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'QuanLy') {
    die("<h1>Bạn không có quyền truy cập vào trang này!</h1><a href='home.php'>Quay lại</a>");
}

// Lấy ID sản phẩm cần khôi phục
$productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productID <= 0) {
    die("ID sản phẩm không hợp lệ!");
}

// Lấy thông tin sản phẩm đã xóa
$query = "SELECT * FROM SanPhamDaXoa WHERE SanPhamID = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$productID]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // Thực hiện việc khôi phục
    $restoreQuery = "INSERT INTO SanPham (SanPhamID, TenSanPham, Gia, HinhAnh, MoTa, DanhMucID, TrangThai) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = [
        $product['SanPhamID'],
        $product['TenSanPham'],
        $product['Gia'],
        $product['HinhAnh'],
        $product['MoTa'],
        $product['DanhMucID'],
        $product['TrangThai']
    ];
    
    $restoreStmt = $conn->prepare($restoreQuery);
    if ($restoreStmt->execute($params)) {
        // Xóa khỏi bảng SanPhamDaXoa sau khi khôi phục
        $deleteQuery = "DELETE FROM SanPhamDaXoa WHERE SanPhamID = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->execute([$productID]);

        $_SESSION['message'] = "Sản phẩm đã được khôi phục thành công!";
        header('Location: Sanpham_Ad.php');
    } else {
        $_SESSION['message'] = "Đã xảy ra lỗi khi khôi phục sản phẩm!";
        header('Location: Hoantacsanpham_ad.php?id=' . $productID);
    }
} else {
    $_SESSION['message'] = "Sản phẩm không tìm thấy hoặc đã bị xóa hoàn toàn!";
    header('Location: Hoantacsanpham_ad.php');
}
?>
