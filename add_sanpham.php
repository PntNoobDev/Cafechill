<?php
// Kết nối cơ sở dữ liệu
require 'db_config.php';

// Bắt đầu phiên nếu chưa bắt đầu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền người dùng
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'QuanLy') {
    die("<h1>Bạn không có quyền thêm sản phẩm!</h1><a href='Sanpham_Ad.php'>Quay lại</a>");
}

$error = '';
$success = '';

// Lấy danh sách danh mục từ cơ sở dữ liệu
$query = "SELECT DanhMucID, TenDanhMuc FROM DanhMuc";
$stmt = $conn->prepare($query);
$stmt->execute();
$danhMucs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenSanPham = $_POST['TenSanPham'];
    $gia = $_POST['Gia'];
    $moTa = $_POST['MoTa'];
    $danhMucID = $_POST['DanhMucID'];
    $trangThai = $_POST['TrangThai'];
    $hinhAnh = $_FILES['HinhAnh']['name'];

    // Kiểm tra các trường nhập liệu
    if (empty($tenSanPham) || empty($gia) || empty($danhMucID) || empty($trangThai)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Xử lý upload hình ảnh
        $targetDir = "img/";
        $targetFile = $targetDir . basename($hinhAnh);
        if (!empty($hinhAnh)) {
            move_uploaded_file($_FILES['HinhAnh']['tmp_name'], $targetFile);
        } else {
            $hinhAnh = null;
        }

        // Thêm sản phẩm vào cơ sở dữ liệu
        $query = "INSERT INTO SanPham (TenSanPham, Gia, MoTa, DanhMucID, HinhAnh, TrangThai) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt->execute([$tenSanPham, $gia, $moTa, $danhMucID, $hinhAnh, $trangThai])) {
            $_SESSION['message'] = "Thêm sản phẩm thành công!";
            header("Location: Sanpham_Ad.php"); // Chuyển hướng người dùng về trang danh sách sản phẩm
            exit;
        } else {
            $error = "Thêm sản phẩm thất bại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Thêm sản phẩm</h1>

    <!-- Hiển thị thông báo nếu có lỗi -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Hiển thị thông báo thành công nếu có -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message']); // Xóa thông báo sau khi đã hiển thị ?>
    <?php endif; ?>

    <!-- Form thêm sản phẩm -->
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="TenSanPham" class="form-label">Tên sản phẩm</label>
            <input type="text" class="form-control" id="TenSanPham" name="TenSanPham" required>
        </div>
        <div class="mb-3">
            <label for="Gia" class="form-label">Giá</label>
            <input type="number" step="0.01" class="form-control" id="Gia" name="Gia" required>
        </div>
        <div class="mb-3">
            <label for="MoTa" class="form-label">Mô tả</label>
            <textarea class="form-control" id="MoTa" name="MoTa" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="DanhMucID" class="form-label">Danh mục</label>
            <select class="form-control" id="DanhMucID" name="DanhMucID" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($danhMucs as $danhMuc): ?>
                    <option value="<?php echo $danhMuc['DanhMucID']; ?>">
                        <?php echo htmlspecialchars($danhMuc['TenDanhMuc']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="HinhAnh" class="form-label">Hình ảnh</label>
            <input type="file" class="form-control" id="HinhAnh" name="HinhAnh">
        </div>
        <div class="mb-3">
            <label for="TrangThai" class="form-label">Trạng thái</label>
            <select class="form-control" id="TrangThai" name="TrangThai">
                <option value="ConHang">Còn hàng</option>
                <option value="HetHang">Hết hàng</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
        <a href="Sanpham_Ad.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
</body>
</html>
