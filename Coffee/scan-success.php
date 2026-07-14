<!DOCTYPE html>
<html lang="en">

<head>
<?php
session_start();
// بررسی لاگین بودن و اینکه از مسیر پرداخت اومده
if (!isset($_SESSION['state_login']) || $_SESSION['state_login'] !== true) {
    header("Location: sign-in.php");
    exit();
}
if (!isset($_SESSION['last_order'])) {
    header("Location: index.php");
    exit();
}
// بعد از نمایش، trackcode رو پاک می‌کنیم تا رفرش صفحه دوباره کار نکنه
$trackcode = $_SESSION['last_order'];
unset($_SESSION['last_order']);
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/boostrap.min.css">
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <link rel="stylesheet"type="text/css" href="css/styles.css"/>
    <link rel="stylesheet" href="fonts/font-icons.css">
    <link rel="shortcut icon" href="images/logo/48.png" />
    <link rel="apple-touch-icon-precomposed" href="images/logo/48.png" />
        
    <title>موفقیت آمیز</title>
</head>

<body>
     <!-- preloade -->
     <div class="preload preload-container">
        <div class="preload-logo">
          <div class="spinner"></div>
        </div>
      </div>
    <!-- /preload -->
    <div class="header">
        <div class="title-header-bar fixed-top bg-white">
            <a href="#" class="back-btn"><i class="icon-right"></i></a>
            <h1>اسکن کارت</h1>
             </div>
    </div>
    <div class="app pt-80">  
        <div class="scan-section">
            <div class="tf-container">
                <div class="b-success">
                    <img src="images/background/img-4.jpg" alt="تصویر">
                </div>
                <h1 class="title-success">پرداخت با موفقیت انجام شد،
                    سفارش شما تأیید گردید</h1>
                <p style="text-align:center; color:#888; margin-top:10px;">
                    کد پیگیری: <strong style="color:#c8a96e"><?php echo $trackcode; ?></strong>
                </p>
                <div class="mt-60 mb-20">
                    <a href="my_orders.php" class="tf-btn large primary mb-15">پیگیری سفارش</a>              
                    <a href="index.php" class="tf-btn large white outline">بازگشت به خانه</a>              
                </div>
            </div>
        </div>
    </div>
    
    
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/swiper-bundle.min.js"></script>
    <script type="text/javascript" src="js/carousel.js"></script>
    <script type="text/javascript" src="js/sidebar.js"></script>
    <script type="text/javascript" src="js/main.js"></script>

</body>

</html>