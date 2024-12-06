<?php
// Kết nối cơ sở dữ liệu
require 'db_config.php';

// Kiểm tra quyền người dùng, chỉ cho phép Quản lý chỉnh sửa sản phẩm
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'QuanLy') {
    // Nếu không phải Quản lý, hiển thị thông báo và không cho phép chỉnh sửa
}

$error = '';
$success = '';
$productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra ID sản phẩm hợp lệ
if ($productID <= 0) {
    die("ID sản phẩm không hợp lệ!");
}

// Truy vấn thông tin sản phẩm hiện tại
$query = "SELECT * FROM SanPham WHERE SanPhamID = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$productID]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra nếu sản phẩm không tồn tại
if (!$product) {
    die("Không tìm thấy sản phẩm!");
}

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenSanPham = $_POST['TenSanPham'];
    $gia = $_POST['Gia'];
    $moTa = $_POST['MoTa'];
    $danhMucID = $_POST['DanhMucID'];
    $trangThai = $_POST['TrangThai'];
    $hinhAnh = $_FILES['HinhAnh']['name'];

    // Xử lý upload hình ảnh
    if (!empty($hinhAnh)) {
        $targetDir = "img/";
        $targetFile = $targetDir . basename($_FILES['HinhAnh']['name']);
        move_uploaded_file($_FILES['HinhAnh']['tmp_name'], $targetFile);
    } else {
        $hinhAnh = $product['HinhAnh'];
    }

    // Cập nhật dữ liệu vào cơ sở dữ liệu
    $updateQuery = "
        UPDATE SanPham 
        SET TenSanPham = ?, Gia = ?, MoTa = ?, DanhMucID = ?, HinhAnh = ?, TrangThai = ?
        WHERE SanPhamID = ?";
    $stmt = $conn->prepare($updateQuery);

    if ($stmt->execute([$tenSanPham, $gia, $moTa, $danhMucID, $hinhAnh, $trangThai, $productID])) {
        $success = "Cập nhật sản phẩm thành công!";
        // Lấy lại dữ liệu sản phẩm để hiển thị
        $stmt = $conn->prepare("SELECT * FROM SanPham WHERE SanPhamID = ?");
        $stmt->execute([$productID]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Cập nhật sản phẩm thất bại!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Chỉnh sửa sản phẩm</h1>

    <!-- Hiển thị thông báo nếu có lỗi -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Hiển thị thông báo thành công nếu có -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form chỉnh sửa sản phẩm -->
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="TenSanPham" class="form-label">Tên sản phẩm</label>
            <input type="text" class="form-control" id="TenSanPham" name="TenSanPham" value="<?php echo htmlspecialchars($product['TenSanPham']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="Gia" class="form-label">Giá</label>
            <input type="number" step="0.01" class="form-control" id="Gia" name="Gia" value="<?php echo htmlspecialchars($product['Gia']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="MoTa" class="form-label">Mô tả</label>
            <textarea class="form-control" id="MoTa" name="MoTa" rows="3"><?php echo htmlspecialchars($product['MoTa']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="DanhMucID" class="form-label">Danh mục</label>
            <input type="number" class="form-control" id="DanhMucID" name="DanhMucID" value="<?php echo htmlspecialchars($product['DanhMucID']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="HinhAnh" class="form-label">Hình ảnh</label>
            <?php if ($product['HinhAnh']): ?>
                <div>
                    <img src="img/<?php echo htmlspecialchars($product['HinhAnh']); ?>" alt="Hình ảnh hiện tại" style="width: 100px; height: 100px;">
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" id="HinhAnh" name="HinhAnh">
        </div>
        <div class="mb-3">
            <label for="TrangThai" class="form-label">Trạng thái</label>
            <select class="form-control" id="TrangThai" name="TrangThai">
                <option value="ConHang" <?php echo $product['TrangThai'] === 'ConHang' ? 'selected' : ''; ?>>Còn hàng</option>
                <option value="HetHang" <?php echo $product['TrangThai'] === 'HetHang' ? 'selected' : ''; ?>>Hết hàng</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="Sanpham_Ad.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
</body>
</html>
