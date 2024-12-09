<?php
session_start(); // Khởi động session

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Chuyển hướng nếu chưa đăng nhập
    exit();
}

// Kết nối cơ sở dữ liệu
require 'db_config.php';

// Lấy AccountID từ session
$accountID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


$userBalance = 0.00;// Số dư mặc định
if ($accountID) {
    try {
        // Lấy số dư từ bảng TaiKhoanKhachHang
        $query = "SELECT SoDu FROM TaiKhoanKhachHang WHERE AccountID = :accountID LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':accountID', $accountID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Gán số dư nếu tồn tại
        if ($result) {
            $userBalance = $result['SoDu'];
        }
    } catch (PDOException $e) {
        error_log("Lỗi khi lấy số dư tài khoản: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- link của icon fa -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <!-- Header -->
    <header class="shadow-sm">
        <div class="container d-flex justify-content-between align-items-center py-3">
            <a href="/home.php">
                <div class="logo" id="logo">
                    <!-- Thêm logo nếu có -->
                    <h1 class="h5 mb-0"></h1>
                </div>
            </a>
            
            <nav class="nav">
                <a class="nav-link active" href="/home.php">Home</a>
                <a class="nav-link" href="/About.php">About</a>
                <a class="nav-link" href="#">Page</a>
                <a class="nav-link" href="/Products.php">Products</a>
                <!-- Dropdown Menu for Products -->
            </nav>

            <div class="looking input-group">
                <!-- Icon -->
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search"></i>
                </span>
                <!-- Input -->
                <input type="text" name="Search" class="form-control border-0" placeholder="Tìm kiếm..." aria-label="Search">
            </div>

            <div class="user d-flex align-items-center position-relative">
                <img src="img/user_avt.ico" alt="Avatar" id="avatar" class="avatar rounded-circle" style="width: 40px; height: 40px; cursor: pointer;">
                <div id="userInfo" class="user-info bg-white shadow rounded p-3 position-absolute" style="display: none; top: 60px; right: 0;">
                    <p class="mb-2"><strong>Tên người dùng:</strong> <?php echo $_SESSION['username']; ?></p>
                    <p class="mb-3"><strong>Quyền:</strong> <?php echo $_SESSION['role']; ?></p>
                    <p><strong>Số dư tài khoản:</strong> <?php echo number_format($userBalance, 2); ?> VND</p>
                    
                    <p>
                        <?php if ($_SESSION['role'] === 'QuanLy' || $_SESSION['role'] === 'NhanVien'): ?>
                        <a href="/login_admin.php" class="btn btn-danger btn-sm w-100">Đến Trang Quản Lí</a>
                        <?php endif; ?>
                    </p>
                    <p class="mb-1">
                        <a href="/naptien.php" class="btn btn-danger btn-sm w-100">Nạp tiền</a>
                    </p>
                    <a href="logout.php" class="btn btn-danger btn-sm w-100">Đăng xuất</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container1">
        <h1 class="home-title">Welcome To Cafe Neft!</h1>
        <!-- Tiêu đề Home -->
        <div class="Home_banner">
            <img src="/img/banner_home.png" alt="banner1">
            <img src="/img/banner_home1.png" alt="banner2">
        </div>
        <div class="Banner_sale">
            <img src="/img/banner_home2.png" alt="banner3">
            <img src="/img/banner_home3.png" alt="banner4">
        </div>
        <div class="Banner_sale1">
            <img src="/img/banner_home4.png" alt="banner5">
        </div>
        <div class="Banner_Food">
            <img src="/img/banner_home6.png" alt="banner7">
            <img src="/img/banner_home7.png" alt="banner8">
        </div>

        <!-- Display user's balance -->
      
    </div>

    <!-- Cart icon -->
    <div class="cart-wrap">
        <div class="cart">
            <img src="/img/cart_add.png" alt="cart" class="cart-icon">
        </div>
    </div>
    <!-- Banner Right  -->
    <div class="banner"></div>
    <!-- Footer  -->
    <?php include('Footer.php'); ?>
</div>
<script>
// Toggle user info box
document.getElementById('avatar').addEventListener('click', function(event) {
    const userInfo = document.getElementById('userInfo');
    userInfo.style.display = (userInfo.style.display === 'block') ? 'none' : 'block';
    event.stopPropagation();
});

// Close user info box when clicking outside
document.addEventListener('click', function(event) {
    const userInfo = document.getElementById('userInfo');
    const avatar = document.getElementById('avatar');
    if (!userInfo.contains(event.target) && event.target !== avatar) {
        userInfo.style.display = 'none';
    }
});
</script>
<!-- JavaScript -->
</body>
</html>
