<?php
session_start();
require 'db_config.php';

// Kết nối cơ sở dữ liệu
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($userName) && !empty($password)) {
        // Lấy thông tin tài khoản từ cơ sở dữ liệu
        $query = "SELECT AccountID, UserName, PasswordHash, Role FROM Accounts WHERE UserName = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Kiểm tra mật khẩu đã mã hóa hay chưa
            if (password_verify($password, $user['PasswordHash'])) {
                // Mật khẩu chính xác
                if (in_array($user['Role'], ['QuanLy', 'NhanVien'])) {
                    $_SESSION['user_id'] = $user['AccountID'];
                    $_SESSION['username'] = $user['UserName'];
                    $_SESSION['role'] = $user['Role'];

                    // Chuyển hướng đến trang admin dashboard
                    header('Location: home_db_ad.php');
                    exit();
                } else {
                    $error = "Bạn không có quyền truy cập!";
                }
            } else {
                // Nếu mật khẩu chưa được mã hóa, kiểm tra chuỗi thô
                if ($password === $user['PasswordHash']) {
                    // Mã hóa và cập nhật mật khẩu
                    $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
                    $updateQuery = "UPDATE Accounts SET PasswordHash = ? WHERE AccountID = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("si", $newPasswordHash, $user['AccountID']);
                    $updateStmt->execute();

                    // Đăng nhập thành công sau khi mã hóa
                    if (in_array($user['Role'], ['QuanLy', 'NhanVien'])) {
                        $_SESSION['user_id'] = $user['AccountID'];
                        $_SESSION['username'] = $user['UserName'];
                        $_SESSION['role'] = $user['Role'];

                        // Chuyển hướng đến trang admin dashboard
                        header('Location: home_db_ad.php');
                        exit();
                    } else {
                        $error = "Bạn không có quyền truy cập!";
                    }
                } else {
                    // Mật khẩu sai
                    $error = "Sai mật khẩu!";
                }
            }
        } else {
            $error = "Tên đăng nhập không tồn tại!";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>
<!-- Giao diện đăng nhập -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">

</head>
<body>
<div class="container mt-4">
    <h1>Đăng nhập</h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
    </form>
</div>
</body>
</html>
