<?php
session_start();
require 'db_config.php';

// Kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'QuanLy') {
    die("<h1>Bạn không có quyền truy cập vào trang này!</h1><a href='home.php'>Quay lại</a>");
}

// Số lượng sản phẩm trên mỗi trang
$productsPerPage = 6;

// Xác định trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính toán từ sản phẩm bắt đầu
$offset = ($page - 1) * $productsPerPage;

// Lấy danh sách sản phẩm đã xóa với phân trang
$query = "SELECT * FROM SanPhamDaXoa ORDER BY NgayXoa DESC LIMIT :offset, :productsPerPage";
$stmt = $conn->prepare($query);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':productsPerPage', $productsPerPage, PDO::PARAM_INT);
$stmt->execute();
$deletedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng số sản phẩm
$totalProductsQuery = "SELECT COUNT(*) FROM SanPhamDaXoa";
$totalProductsStmt = $conn->prepare($totalProductsQuery);
$totalProductsStmt->execute();
$totalProducts = (int)$totalProductsStmt->fetchColumn();

// Tính số trang
$totalPages = ceil($totalProducts / $productsPerPage);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm đã xóa</title>
    <link rel="stylesheet" href="css/Sanpham_daxoa_ad.css">
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
                    <th>Hành Động</th>
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
                            <td>
                                <a href="Hoantacsanpham_ad.php?id=<?php echo $product['SanPhamID']; ?>" class="btn btn-sm btn-success">Hoàn tác</a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Không có sản phẩm đã xóa!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Trang trước</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>"<?php if ($i === $page) echo ' class="active"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Trang sau</a>
            <?php endif; ?>
        </div>

        <a href="Sanpham_Ad.php">Quay lại</a>
    </div>
</body>
</html>
