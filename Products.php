<?php
include('db_config.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="alert alert-success">
        <?php
        echo $_SESSION['cart_message'];
        unset($_SESSION['cart_message']);
        ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/Products.css">
</head>
<body>
    <!-- Header -->
<?php

include('header_product.php');

?>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1 class="mb-4">Danh sách sản phẩm</h1>

        <!-- Filter Categories -->
        <form method="GET" class="mb-4">
            <label class="form-label">Chọn danh mục:</label>
            <div>
                <?php
                $conn = new mysqli($host, $username, $password, $dbname);
                $categoryQuery = "SELECT DanhMucID, TenDanhMuc FROM DanhMuc";
                $categoryResult = $conn->query($categoryQuery);
                ?>
                <label class="me-3">
                    <input type="radio" name="category" value="all" <?php echo !isset($_GET['category']) || $_GET['category'] === 'all' ? 'checked' : ''; ?>> Tất cả
                </label>
                <?php while ($category = $categoryResult->fetch_assoc()) { ?>
                    <label class="me-3">
                        <input type="radio" name="category" value="<?php echo $category['DanhMucID']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $category['DanhMucID'] ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($category['TenDanhMuc']); ?>
                    </label>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Tìm Kiếm</button>
        </form>

        <!-- Product List -->
        <div class="row">
            <?php
            // Category filter logic
            $categoryFilter = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;

            // Pagination setup
            $productsPerPage = 6;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $startIndex = ($page - 1) * $productsPerPage;

            // Product query
            $query = "
                SELECT 
                    sp.SanPhamID, sp.TenSanPham, sp.Gia, sp.MoTa, sp.HinhAnh, sp.TrangThai, dm.TenDanhMuc 
                FROM 
                    SanPham sp 
                LEFT JOIN 
                    DanhMuc dm ON sp.DanhMucID = dm.DanhMucID
            ";
            if ($categoryFilter) {
                $query .= " WHERE sp.DanhMucID = ?";
            }
            $query .= " LIMIT ?, ?";

            $stmt = $conn->prepare($query);
            if ($categoryFilter) {
                $stmt->bind_param("iii", $categoryFilter, $startIndex, $productsPerPage);
            } else {
                $stmt->bind_param("ii", $startIndex, $productsPerPage);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $productName = htmlspecialchars($row['TenSanPham']);
                    $productPrice = number_format($row['Gia'], 0, ',', '.');
                    $productImage = !empty($row['HinhAnh']) ? $row['HinhAnh'] : 'default.jpg';
                    $productCategory = htmlspecialchars($row['TenDanhMuc']);
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="img/<?php echo $productImage; ?>" class="card-img-top" alt="<?php echo $productName; ?>">
                            <div class="card-body"> 
                                <h5 class="card-title"><?php echo $productName; ?></h5>
                                <p class="card-text"><strong>Danh mục:</strong> <?php echo $productCategory; ?></p>
                                <p class="card-text"><strong>Giá:</strong> <?php echo $productPrice; ?> VND</p>
                                <form action="add_to_cart.php" method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="product_id" value="<?php echo $row['SanPhamID']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" class="form-control me-2" style="width: 70px;">
                                    <button type="submit" class="btn btn-success">Thêm vào giỏ hàng</button>
                                </form>
                            </div>  
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>Không tìm thấy sản phẩm nào.</p>";
            }

            // Pagination
            $totalQuery = "SELECT COUNT(*) AS total FROM SanPham";
            if ($categoryFilter) {
                $totalQuery .= " WHERE DanhMucID = ?";
            }
            $stmtTotal = $conn->prepare($totalQuery);
            if ($categoryFilter) {
                $stmtTotal->bind_param("i", $categoryFilter);
            }
            $stmtTotal->execute();
            $totalProducts = $stmtTotal->get_result()->fetch_assoc()['total'];
            $totalPages = ceil($totalProducts / $productsPerPage);

            $stmt->close();
            $stmtTotal->close();
            $conn->close();
            ?>
        </div>

        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Trước</a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Tiếp theo</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="cart-wrap">
                <div class="cart">
                <a href="cart.php">
                    <img src="/img/cart_add.png" alt="cart" class="cart-icon">
                </a>

                </div>
    </div>
    
    <!-- Tìm Kiếm -->
    <script>
            document.getElementById('searchBox').addEventListener('input', function () {
                const searchText = this.value.toLowerCase();
                const products = document.querySelectorAll('.product-item');

                products.forEach(product => {
                    const name = product.querySelector('h3').textContent.toLowerCase();
                    const description = product.querySelector('p').textContent.toLowerCase();
                    if (name.includes(searchText) || description.includes(searchText)) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        </script>
</body>
</html>
