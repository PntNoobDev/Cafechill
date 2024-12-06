<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// echo "<h1>Chào mừng, " . htmlspecialchars($_SESSION['username']) . "!</h1>";
// echo "<p>Vai trò của bạn: " . htmlspecialchars($_SESSION['role']) . "</p>";
// echo '<a href="logout.php">Đăng xuất</a>';
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
            <?php
            include('header_home.php');
            ?>
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
            </div>

            <!-- Cart icon -->
            <div class="cart-wrap">
                <div class="cart">
                    <img src="/img/cart_add.png" alt="cart" class="cart-icon">
                </div>
            </div>
            <!-- Banner Right  -->
            <div class="banner">

            </div>
            <!-- Footer  -->
            <?php
                include('Footer.php');
            ?>
        </div>

    </div>
       


            <!-- JavaScript -->
           

     </script>
</body>
</html>

