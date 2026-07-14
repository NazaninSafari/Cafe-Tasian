<?php

session_start();



$link = mysqli_connect('localhost','root','','Coffee_db');



$order_counts = [];

$oc_result = mysqli_query($link, "SELECT pro_code, COUNT(*) as cnt FROM order GROUP BY pro_code");

while ($oc_row = mysqli_fetch_assoc($oc_result)) {

    $order_counts[$oc_row['pro_code']] = $oc_row['cnt'];

}



// امتیاز واقعی

$ratings = [];

$review_table = mysqli_query($link, "SHOW TABLES LIKE 'review'");

if ($review_table && mysqli_num_rows($review_table) > 0) {

    $rating_res = mysqli_query($link,

        "SELECT pro_code, ROUND(AVG(rating),1) as avg_r, COUNT(*) as cnt FROM review GROUP BY pro_code");

    while ($r = mysqli_fetch_assoc($rating_res)) {

        $ratings[$r['pro_code']] = ['avg' => $r['avg_r'], 'cnt' => $r['cnt']];

    }

}



$result = null;

$search_term = '';



if(isset($_GET['search']) && !empty(trim($_GET['search']))){



    $search_term = trim($_GET['search']);

    $search = mysqli_real_escape_string($link, $search_term);



    $query = "SELECT * FROM product

              WHERE pro_name LIKE '%$search%'

              ORDER BY pro_code DESC";



    $result = mysqli_query($link,$query);

}

?>

<!DOCTYPE html>

<html lang="fa">

<head>

    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>جستجو</title>

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

        <h1><?= $search_term ? 'نتایج: ' . htmlspecialchars($search_term) : 'جستجو' ?></h1>

    </div>

</div>



<div class="app pt-80">

    <div class="tf-container">



        <ul class="tf-box-column sm d-flex flex-wrap justify-content-start">



        <?php

        if ($result === null): ?>

            <div style="text-align:center; padding:60px 20px; color:#888;">

                <p style="font-size:1.1rem;">:mag: عبارتی برای جستجو وارد کنید</p>

            </div>

        <?php elseif(mysqli_num_rows($result) > 0):

            while ($row = mysqli_fetch_assoc($result)): ?>



            <li class="tf-box-column sm mx-2 mb-4" style="width:200px;">



                <div class="img-box mb-2 text-center">

                    <img src="images/products/<?php echo $row['pro_image']; ?>"

                         alt="تصویر"

                         class="img-fluid"

                         style="max-width:100%; height:auto;">

                </div>



                <div class="content-box">



                    <h3>

                        <a href="product-detail.php?id=<?php echo $row['pro_code']; ?>">

                            <?php echo $row['pro_name']; ?>

                        </a>

                    </h3>



                    <ul class="review mb-2">

                        <li>

                            <i class="icon-star"></i>

                            <?php

                            $avg_r   = $ratings[$row['pro_code']]['avg'] ?? null;

                            $rev_cnt = $ratings[$row['pro_code']]['cnt'] ?? 0;

                            $buy_cnt = $order_counts[$row['pro_code']]   ?? 0;

                            if ($avg_r) echo "<span>$avg_r</span> ($rev_cnt نظر)";
else        echo "<span>—</span> ($buy_cnt خریدار)";

                            ?>

                        </li>

                        <li class="dot-icon mx-2">•</li>

                        <li>16 دقیقه</li>

                    </ul>



                    <div class="box-price d-flex justify-content-between align-items-center">



                        <ul class="price mb-0">

                            <li class="accent">

                                <?php echo number_format($row['pro_price']); ?>

                                تومان

                            </li>

                        </ul>



                        <a href="product-detail.php?id=<?php echo $row['pro_code']; ?>"

                           class="btn-add">+</a>



                    </div>



                </div>



            </li>



        <?php

            endwhile;

        else: ?>

            <div class='alert alert-warning text-center'>

                محصولی با نام «<?= htmlspecialchars($search_term) ?>» پیدا نشد

            </div>

        <?php endif; ?>



        </ul>



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