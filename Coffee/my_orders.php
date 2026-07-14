<?php
session_start();

$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');

if (mysqli_connect_errno()) {
    die("خطای اتصال به پایگاه داده");
}

if (!isset($_SESSION["state_login"]) || $_SESSION["state_login"] !== true) {
    header("Location: profile.php");
    exit();
}

$username = $_SESSION['username'];
$query = "SELECT trackcode,orderdate,state,COUNT(*) as items_count,SUM(pro_price * pro_qty) as total_price
          FROM `order`
          WHERE username='$username'
          GROUP BY trackcode
          ORDER BY MAX(id) DESC";
$result = mysqli_query($link, $query);
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>سفارشات من</title>
    <link rel="stylesheet" href="css/boostrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="css/swiper-bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <link rel="stylesheet" href="fonts/font-icons.css" />
    <link rel="shortcut icon" href="images/logo/48.png" />
    <link rel="apple-touch-icon-precomposed" href="images/logo/48.png" />
</head>
<body>
<div class="preload preload-container">
    <div class="preload-logo"><div class="spinner"></div></div>
</div>

<div class="header">
    <div class="title-header-bar fixed-top bg-white">
        <a href="#" class="back-btn"><i class="icon-right"></i></a>
        <h1>سفارشات من</h1>
    </div>
</div>

<div class="app pt-80 pb-70">
    <div class="tf-container">

        <?php while($row = mysqli_fetch_assoc($result)) { ?>

<li class="tf-box-row mb-12" style="padding:15px; margin-bottom:15px;">

    <div class="content-box">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <h5 style="margin:0;">
                سفارش #<?php echo $row['trackcode']; ?>
            </h5>

            <?php
            if($row['state']==0){
                echo "<span style='background:#f39c12;color:#fff;padding:5px 10px;border-radius:20px;font-size:12px;'>ثبت شده</span>";
            }
            elseif($row['state']==1){
                echo "<span style='background:#3498db;color:#fff;padding:5px 10px;border-radius:20px;font-size:12px;'>در حال آماده‌سازی</span>";
            }
            elseif($row['state']==2){
                echo "<span style='background:#e67e22;color:#fff;padding:5px 10px;border-radius:20px;font-size:12px;'>آماده تحویل</span>";
            }
            elseif($row['state']==3){
                echo "<span style='background:#2ecc71;color:#fff;padding:5px 10px;border-radius:20px;font-size:12px;'>تحویل داده شده</span>";
            }
            ?>
        </div>

        <ul style="list-style:none; padding:0; margin:0; line-height:32px;">

            <li>
                <strong>تاریخ سفارش:</strong>
                <?php echo $row['orderdate']; ?>
            </li>

            <li>
                <strong>تعداد محصولات:</strong>
                <?php echo $row['items_count']; ?>
            </li>

            <li>
                <strong>مبلغ کل سفارش:</strong>
                <?php echo number_format($row['total_price']); ?>
                تومان
            </li>

        </ul>
    </div>

</li>

<?php } ?>

    </div>
</div>

 <div class="menubar-footer footer-fixed">
        <ul class="inner">
          <li id="a">
            <a href="index.php"><span class="icon-home" ></span> خانه</a>
          </li>
          <li>
            <a  href="cart.php"><span class="icon-buy"></span> سفارش</a>
          </li>
           <li>
            <a href="about-us.html"><span class="icon-heart"></span> درباره ما</a>
          </li>
          <li>
            <a  href="profile.php"><span class="icon-profile"></span> پروفایل‌ها</a>
          </li>
        </ul>
      </div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/swiper-bundle.min.js"></script>
<script src="js/carousel.js"></script>
<script src="js/sidebar.js"></script>
<script src="js/main.js"></script>
</body>
</html>