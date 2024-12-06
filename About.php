<?php
        include('db_config.php');
        session_start();     
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
            <link rel="stylesheet" href="css/About.css">
        </head>
        <body>
            <!-- Header -->
            <?php 
                include('header_about.php');
            ?>
             <!-- about -->
         <!-- about -->
        <div class="about-wrap">
            <!-- info -->
            <div class="about-left">
                <!-- header -->
                <div>
                    <p class="about-slogan">About Coffee</p>
                    <h1>Lorem ipsum dolor sit amet consectetur.</h1>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur sed saepe eligendi amet quae reiciendis sunt beatae quod! Maiores vel odit tempore accusantium corporis reiciendis sunt molestias excepturi nam nesciunt?</p>
                </div>
                <!-- body -->
                <div class="about-wrapper">
                    <!-- item 1 -->
                    <div class="about-item">
                        <img src="/img/choose_icon1.png" alt="" class="about-icon">
                        <div class="about-info">
                            <h2>Lorem ipsum dolor sit.</h2>
                            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Accusantium, qui?</p>
                        </div>
                    </div>
                    <!-- item 2 -->
                    <div class="about-item">
                        <img src="/img/choose_icon2.png" alt="" class="about-icon">
                        <div class="about-info">
                            <h2>Lorem ipsum dolor sit.</h2>
                            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Accusantium, qui?</p>
                        </div>
                    </div>
                </div>
                <!-- btn -->
                <div class="about-btn">
                    <button>Our Coffee</button>
                </div>
            </div>
            <!-- img -->
            <div class="about-right">
                <img src="/img/banner_home.png" alt="">
            </div>
        </div>

         </div>
            <!-- show_case -->
                <div class="showtime">
                    
                </div>
            
            <!-- Cart icon -->
            <div class="cart-wrap">
                <div class="cart">
                    <img src="/img/cart_add.png" alt="cart" class="cart-icon">
                </div>
            </div>
            <!-- Banner Right  -->
           <!-- Footer -->
               <!-- Footer  -->
            <?php
                include('Footer.php');
            ?>
        </div>

    </div>
</body>
</html>
