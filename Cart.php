<?php
include('db_config.php');
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kết nối cơ sở dữ liệu
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách đơn đặt hàng theo AccountID
$userId = $_SESSION['user_id'];
$query = "
    SELECT 
        DatHangID, NgayDat, TrangThai, TongTien 
    FROM 
        DatHang 
    WHERE 
        AccountID = ?
    ORDER BY 
        NgayDat DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orderList = [];
while ($row = $result->fetch_assoc()) {
    $orderList[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đơn đặt hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Danh sách đơn đặt hàng</h1>
        <?php if (empty($orderList)): ?>
            <p>Bạn chưa có đơn đặt hàng nào!</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mã đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderList as $index => $order): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($order['DatHangID']); ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($order['NgayDat'])); ?></td>
                            <td>
                                <?php 
                                    switch ($order['TrangThai']) {
                                        case 'DangXuLy': echo 'Đang xử lý'; break;
                                        case 'DangGiao': echo 'Đang giao'; break;
                                        case 'HoanThanh': echo 'Hoàn thành'; break;
                                        case 'Huy': echo 'Hủy'; break;
                                        default: echo 'Không xác định';
                                    }
                                ?>
                            </td>
                            <td><?php echo number_format($order['TongTien'], 0, ',', '.'); ?> VND</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="products.php" class="btn btn-primary">Tiếp tục mua sắm</a>
    </div>
</body>
</html>
