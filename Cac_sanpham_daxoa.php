<?php
session_start();
require 'db_config.php';

// Kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'QuanLy') {
    die("<h1>Bạn không có quyền truy cập vào trang này!</h1><a href='home.php'>Quay lại</a>");
}

// Lấy danh sách sản phẩm đã xóa
$query = "SELECT * FROM SanPhamDaXoa ORDER BY NgayXoa DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$deletedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm đã xóa</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Danh sách sản phẩm đã xóa</h1>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Hình ảnh</th>
                    <th>Ngày xóa</th>
                    <th>Người xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($deletedProducts)): ?>
                    <?php foreach ($deletedProducts as $product): ?>
                        <tr>
                            <td><?php echo $product['SanPhamID']; ?></td>
                            <td><?php echo htmlspecialchars($product['TenSanPham']); ?></td>
                            <td><?php echo number_format($product['Gia'], 0, ',', '.'); ?> VND</td>
                            <td>
                                <?php if ($product['HinhAnh']): ?>
                                    <img src="/img/<?php echo htmlspecialchars($product['HinhAnh']); ?>" alt="Hình ảnh">
                                <?php else: ?>
                                    Không có ảnh
                                <?php endif; ?>
                            </td>
                            <td><?php echo $product['NgayXoa']; ?></td>
                            <td><?php echo $product['QuanLyXoa']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Không có sản phẩm đã xóa!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="Sanpham_Ad.php">Quay lại</a>
    </div>
</body>
</html>
