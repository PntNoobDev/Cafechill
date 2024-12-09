<?php
session_start(); // Khởi động session

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Kết nối cơ sở dữ liệu
require 'db_config.php';

// Lấy AccountID từ session
$accountID = $_SESSION['user_id'];
$message = ""; // Thông báo trạng thái giao dịch

// Xử lý khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    // Kiểm tra số tiền hợp lệ
    if ($amount > 0) {
        try {
            // Bắt đầu giao dịch
            $conn->beginTransaction();

            // Lấy số dư hiện tại
            $query = "SELECT SoDu FROM TaiKhoanKhachHang WHERE AccountID = :accountID LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':accountID', $accountID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $currentBalance = $result['SoDu'];
                $newBalance = $currentBalance + $amount;

                // Cập nhật số dư
                $updateQuery = "UPDATE TaiKhoanKhachHang SET SoDu = :newBalance WHERE AccountID = :accountID";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':newBalance', $newBalance);
                $updateStmt->bindParam(':accountID', $accountID, PDO::PARAM_INT);
                $updateStmt->execute();

                // Hoàn tất giao dịch
                $conn->commit();

                $message = "Nạp tiền thành công! Số dư mới của bạn là " . number_format($newBalance, 2) . " VND.";
            } else {
                $message = "Tài khoản không tồn tại.";
            }
        } catch (PDOException $e) {
            $conn->rollBack(); // Rollback nếu có lỗi
            $message = "Lỗi khi nạp tiền: " . $e->getMessage();
        }
    } else {
        $message = "Số tiền nạp phải lớn hơn 0.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nạp Tiền</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Nạp Tiền Vào Tài Khoản</h1>
        <div class="card mx-auto mt-4" style="max-width: 500px;">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Số Tiền Cần Nạp (VND):</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1" placeholder="Nhập số tiền" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Nạp Tiền</button>
                </form>
                <?php if ($message): ?>
                <div class="alert alert-info mt-3"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="home.php" class="btn btn-secondary">Quay Lại Trang Chủ</a>
        </div>
    </div>
</body>
</html>
