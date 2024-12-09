<?php
session_start();
require 'db_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        // Lấy thông tin tài khoản từ cơ sở dữ liệu
        $query = "SELECT * FROM Accounts WHERE UserName = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Kiểm tra mật khẩu đã mã hóa hay chưa
            if (password_verify($password, $user['PasswordHash'])) {
                // Mật khẩu chính xác
                $_SESSION['user_id'] = $user['AccountID'];
                $_SESSION['username'] = $user['UserName'];
                $_SESSION['role'] = $user['Role'];
                header('Location: home.php');
                exit();
            } else {
                // Nếu mật khẩu chưa được mã hóa, kiểm tra chuỗi thô
                if ($password === $user['PasswordHash']) {
                    // Mật khẩu đúng nhưng chưa mã hóa -> Mã hóa và cập nhật
                    $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
                    $updateQuery = "UPDATE Accounts SET PasswordHash = :newPasswordHash WHERE AccountID = :id";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindParam(':newPasswordHash', $newPasswordHash);
                    $updateStmt->bindParam(':id', $user['AccountID']);
                    $updateStmt->execute();

                    // Đăng nhập thành công sau khi mã hóa
                    $_SESSION['user_id'] = $user['AccountID'];
                    $_SESSION['username'] = $user['UserName'];
                    $_SESSION['role'] = $user['Role'];
                    header('Location: home.php');
                    exit();
                } else {
                    // Mật khẩu sai
                    $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
                }
            }
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        }
    } else {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Đăng Nhập</h2>
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<p style="color:red;">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']); // Xóa thông báo sau khi hiển thị
        }
        ?>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" class="input-field" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" class="input-field" placeholder="Mật khẩu" required>
            <button type="submit" name="login" class="btn">Đăng nhập</button>
        </form>
        <div class="forgot-password">
            <a href="#">Quên mật khẩu?</a>
        </div>
    </div>
</body>
</html>
