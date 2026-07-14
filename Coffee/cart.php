 <?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) {
    die("خطای اتصال به پایگاه داده: " . mysqli_connect_error());
}

// حذف محصول از سبد
if (isset($_GET['action']) && $_GET['action'] == 'DELETE' && isset($_GET['pro_code'])) {
    $pro_code = $_GET['pro_code'];
    if (isset($_SESSION['cart'][$pro_code])) {
        unset($_SESSION['cart'][$pro_code]);
        header('Location: cart.php');
        exit;
    }
}

// افزودن محصول به سبد
if (isset($_GET['id'])) {
    $pro_code = $_GET['id'];
    $query = "SELECT * FROM product WHERE pro_code='$pro_code'";
    $result = mysqli_query($link, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        // اگر سبد وجود ندارد، ایجاد می‌کنیم
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // اگر محصول قبلاً در سبد است، تعداد رو افزایش می‌دیم
        if (isset($_SESSION['cart'][$pro_code])) {
            $_SESSION['cart'][$pro_code]['quantity'] += 1;
        } else {
            // ذخیره اطلاعات کامل محصول در سبد
            $_SESSION['cart'][$pro_code] = [
                'pro_code' => $row['pro_code'],
                'pro_name' => $row['pro_name'],
                'pro_price' => $row['pro_price'],
                'pro_image' => $row['pro_image'],
                'quantity' => 1
            ];
        }
        header("Location: cart.php");
        exit;
    }
}
// .....................................
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $pro_code = $_POST['pro_code'];
    $quantity = intval($_POST['quantity']);
    $size = $_POST['checkSize'];

    $query = "SELECT * FROM product WHERE pro_code='$pro_code'";
    $result = mysqli_query($link,$query);

    if($row = mysqli_fetch_assoc($result)){

        $_SESSION['cart'][$pro_code] = [

            'pro_code' => $row['pro_code'],
            'pro_name' => $row['pro_name'],
            'pro_price' => $row['pro_price'],
            'pro_image' => $row['pro_image'],
            'quantity' => $quantity,
            'size' => $size

        ];

        header("Location: cart.php");
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>سبد خرید</title>
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
        <h1>سبد خرید</h1>
    </div>
</div>

<div class="app pt-80 pb-70">
    <div class="tf-container">
        <?php
        $total_price = 0;
        $total_quantity = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $pro_code => $product) {
                $price = floatval($product['pro_price']);
                $quantity = intval($product['quantity']);
                $subtotal = $price * $quantity;
                $total_price += $subtotal;
                $total_quantity += $quantity;
                ?>
                <li class="tf-box-row mb-12" style="display:flex; align-items:center; margin-bottom:20px;">
                    <a href="product-detail.php" class="img-box" style="margin-left:20px;">
                        <img src="images/products/<?php echo $product['pro_image']; ?>" alt="تصویر" style="width:100px; height:auto;">
                    </a>
                    <div class="content-box">
                        <h5><a href="product-detail.php"><?php echo $product['pro_name']; ?></a></h5>
                        <ul class="review" style="list-style:none; padding:0; display:flex; align-items:center; font-size:14px; color:#555;">
                            <li style="margin-right:10px;">
                                <i class="icon-star"></i> 4.8 (125)
                            </li>
                            <li class="dot-icon" style="margin:0 10px;">•</li>
                            <li>16 دقیقه</li>&nbsp;&nbsp;
                            <li >سایز:<?php echo $product['size']; ?></li>
                        </ul>
                        
                        <div class="box-price" style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                            <ul class="price" style="list-style:none; margin:0; padding:0;">
                                <li class="accent" style="color:#e67e22; font-weight:bold;"><?php echo number_format($product['pro_price'], 2); ?> تومان</li>
                            </ul>
                            <a href="cart.php?pro_code=<?php echo $product['pro_code']; ?>&action=DELETE" onclick="return confirm('آیا مطمئن هستید؟')" style="background:#e74c3c; color:#fff; padding:5px 10px; border-radius:4px; text-decoration:none;">حذف</a>
                        </div>
                    </div>
                </li>
                <?php
            }
        }
        ?>
        <div class="total">
            <p>مجموع قیمت: <?php echo number_format($total_price, 2); ?> تومان</p>
            <p>تعداد کل: <?php echo $total_quantity; ?></p>
        </div>
<?php

if ($total_price > 0) {
    echo '<a href="payment.php" class="tf-btn large primary">تسویه حساب</a>';
} else {
    echo '<button disabled class="tf-btn large primary">سبد خرید خالی است</button>';
}
?>

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