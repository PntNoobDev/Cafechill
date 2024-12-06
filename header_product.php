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
                        <a class="nav-link " href="/home.php">Home</a>
                        <a class="nav-link" href="/About.php">About</a>
                        <a class="nav-link " href="#">Page</a>
                        <a class="nav-link active " href="/Products.php">Products</a>
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
                            <p>
                                  <?php if ($_SESSION['role'] === 'QuanLy' || $_SESSION['role'] === 'NhanVien'): ?>
                            <a href="/login_admin.php" class="btn btn-danger btn-sm w-100">Đến Trang Quản Lí</a>
                            <?php endif; ?>
                            </p>
                            <a href="logout.php" class="btn btn-danger btn-sm w-100">Đăng xuất</a>
                        </div>
                    </div>
                </div>
            </header>

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

            <script>
                // Lấy tất cả các thẻ <a> trong nav
        const navLinks = document.querySelectorAll('.nav-link');

        // Lặp qua từng thẻ <a> và gắn sự kiện click
        navLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                // Ngăn chặn hành vi mặc định của thẻ <a>
                // event.preventDefault();

                // Xóa class 'active' khỏi tất cả các thẻ <a>
                navLinks.forEach(nav => nav.classList.remove('active'));

                // Thêm class 'active' vào thẻ <a> được nhấn
                this.classList.add('active');
            });
        });

     </script>