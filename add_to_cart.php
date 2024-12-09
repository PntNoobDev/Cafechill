<?php
session_start();
include('db_config.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra phương thức yêu cầu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Kiểm tra tính hợp lệ của dữ liệu
    if ($productId <= 0 || $quantity <= 0) {
        $_SESSION['cart_message'] = "Thông tin sản phẩm không hợp lệ!";
        header("Location: Products.php");
        exit();
    }

    // Khởi tạo giỏ hàng nếu chưa tồn tại
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Thêm sản phẩm vào giỏ hàng hoặc cập nhật số lượng
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity; // Tăng số lượng nếu sản phẩm đã tồn tại
    } else {
        $_SESSION['cart'][$productId] = $quantity; // Thêm sản phẩm mới
    }

    // Kết nối với cơ sở dữ liệu
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy thông tin tài khoản người dùng từ session
    $accountId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    if ($accountId <= 0) {
        $_SESSION['cart_message'] = "Không tìm thấy tài khoản!";
        header("Location: Products.php");
        exit();
    }

    // Tính tổng tiền sản phẩm
    $stmt = $conn->prepare("SELECT Gia FROM SanPham WHERE SanPhamID = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    if (!$product) {
        $_SESSION['cart_message'] = "Sản phẩm không tồn tại!";
        header("Location: Products.php");
        exit();
    }

    $productPrice = $product['Gia'];
    $totalPrice = $productPrice * $quantity;

    // Kiểm tra số dư tài khoản
    $stmtCheckBalance = $conn->prepare("SELECT SoDu FROM TaiKhoanKhachHang WHERE AccountID = ?");
    $stmtCheckBalance->bind_param("i", $accountId);
    $stmtCheckBalance->execute();
    $balanceResult = $stmtCheckBalance->get_result();
    $balanceData = $balanceResult->fetch_assoc();

    if ($balanceData['SoDu'] < $totalPrice) {
        $_SESSION['cart_message'] = "Số dư không đủ để thực hiện đơn hàng!";
        header("Location: Products.php");
        exit();
    }

    // Trừ số dư tài khoản
    $newBalance = $balanceData['SoDu'] - $totalPrice;
    $stmtUpdateBalance = $conn->prepare("UPDATE TaiKhoanKhachHang SET SoDu = ? WHERE AccountID = ?");
    $stmtUpdateBalance->bind_param("di", $newBalance, $accountId);

    if ($stmtUpdateBalance->execute()) {
        // Thêm vào bảng `DatHang`
        $orderQuery = "
            INSERT INTO DatHang (AccountID, TongTien) 
            VALUES (?, ?)";
        $stmtOrder = $conn->prepare($orderQuery);
        $stmtOrder->bind_param("id", $accountId, $totalPrice);

        if ($stmtOrder->execute()) {
            $_SESSION['cart_message'] = "Sản phẩm đã được thêm vào giỏ hàng và lưu đơn hàng thành công!";
        } else {
            $_SESSION['cart_message'] = "Lỗi khi lưu đơn hàng!";
        }

        $stmtOrder->close();
    } else {
        $_SESSION['cart_message'] = "Lỗi khi cập nhật số dư tài khoản!";
    }

    $stmtCheckBalance->close();
    $stmtUpdateBalance->close();
    $stmt->close();
    $conn->close();

    header("Location: Products.php");
    exit();
} else {
    // Nếu không phải yêu cầu POST, chuyển hướng về trang sản phẩm
    header("Location: Products.php");
    exit();
}
