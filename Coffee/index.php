<?php
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) {
    die("خطای اتصال به پایگاه داده: " . mysqli_connect_error());
}

// تعداد خریداران هر محصول — یک بار برای همه صفحه لود می‌شه
$order_counts = [];
$oc_result = mysqli_query($link, "SELECT pro_code, COUNT(*) as cnt FROM `order` GROUP BY pro_code");
while ($oc_row = mysqli_fetch_assoc($oc_result)) {
    $order_counts[$oc_row['pro_code']] = $oc_row['cnt'];
}

// امتیاز واقعی هر محصول از جدول review
$ratings = [];
$review_table = mysqli_query($link, "SHOW TABLES LIKE 'review'");
if ($review_table && mysqli_num_rows($review_table) > 0) {
    $rating_res = mysqli_query($link,
        "SELECT pro_code, ROUND(AVG(rating),1) as avg_r, COUNT(*) as cnt
         FROM review GROUP BY pro_code");
    while ($r = mysqli_fetch_assoc($rating_res)) {
        $ratings[$r['pro_code']] = ['avg' => $r['avg_r'], 'cnt' => $r['cnt']];
    }
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="css/boostrap.min.css">
    <link rel="stylesheet" href="css/swiper-bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="fonts/font-icons.css" />

    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="images/logo/48.png" />
    <link rel="apple-touch-icon-precomposed" href="images/logo/48.png" />

    <title>کافه تاسیان</title>
</head>

<body class="appHome4" dir="rtl">
    <div class="preload preload-container">
        <div class="preload-logo">
            <div class="spinner"></div>
        </div>
    </div>

    <div class="app home-4">
        <div class="inner-headerbar fixed-top bg-primary st1">
            <h1 style="padding-top: 19px;">کافه تاسیان  </h1>
            <a href="#page-top"><img src="images/logo/48.png" alt="..." class="Profile-picture"></a>
        </div>
<br><br>
    <!-- search box -->
        <div class="tf-container pt-90 pb-70">
            <form action="search.php" method="get">
                <div class="search-box">
                    <span class="icon icon-search"></span>
                    <input type="text"name="search"placeholder="قهوه خود را جستجو کنید...">
                </div>
            </form>
    <!-- until here -->
         <br><br><br><br>
            <div class="section bg">
                <div class="title-bar">
                    <h2>مجموعه‌ها</h2>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalRightFull">مشاهده همه <i
                            class="icon-left"></i></a>
                </div>
                <?php
                $count_hot  = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM product WHERE category=0"))[0];
                $count_cold = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM product WHERE category=1"))[0];
                ?>
                <div class="wrap-swiper">
                    <div class="swiper drink-swiper">
                        <div class="swiper-wrapper pb-30 pt-12">
                            <div class="swiper-slide ml-2">
                                <div class="box-collections">
                                    <div class="images">
                                        <a href="index.php#hot">
                                            <img src="images/food/collect-1.jpg" alt="تصاویر">
                                        </a>
                                    </div>
                                    <div class="content">
                                        <h3><a href="index.php#hot">نوشیدنی گرم</a></h3>
                                        <p><?php echo $count_hot; ?> محصول</p>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="box-collections">
                                    <div class="images">
                                        <a href="index.php#cold"><img src="images/products/mohito.jpg" alt="تصاویر"></a>
                                    </div>
                                    <div class="content">
                                        <h3><a href="index.php#cold">نوشیدنی سرد</a></h3>
                                        <p><?php echo $count_cold; ?> محصول</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ...................................................... -->
            <div class="section">
  <div class="title-bar">
    <h2>قهوه برای شما</h2>
    <a href="#" data-bs-toggle="modal" data-bs-target="#modalRightFull">مشاهده همه <i class="icon-left"></i></a>
  </div>
  <?php
  $query="SELECT * FROM product WHERE category=0";
 $result=mysqli_query($link,$query);
  ?>
    <div class="wrap-swiper">
        <div class="swiper drink-swiper">
    <div class="swiper-wrapper">

        <?php while ($row = mysqli_fetch_array($result)):
            $pro_code   = $row['pro_code'];
            $avg_r      = $ratings[$pro_code]['avg'] ?? null;
            $rev_cnt    = $ratings[$pro_code]['cnt'] ?? 0;
            $buy_cnt    = $order_counts[$pro_code]   ?? 0;
        ?>

        <div class="swiper-slide">

            <div class="tf-box-column lg">

                <div class="img-box">
                    <img src="images/products/<?php echo $row['pro_image']; ?>" alt="">
                </div>

                <div class="content-box">
                    <h3>
                        <a href="product-detail.php?id=<?php echo $pro_code; ?>">
                            <?php echo $row['pro_name']; ?>
                        </a>
                    </h3>

                    <ul class="review">
                        <li>
                            <i class="icon-star"></i>
                            <?php if ($avg_r): ?>
                            <span><?= $avg_r ?></span> (<?= $rev_cnt ?> نظر)
                            <?php else: ?>
                            <span>—</span> (<?= $buy_cnt ?> خریدار)
                            <?php endif; ?>
                        </li>
                    </ul>

                    <div class="box-price">
                        <span class="price">
                            <?php echo number_format($row['pro_price']); ?>
                        </span>

                        <a href="product-detail.php?id=<?php echo $row['pro_code']; ?>" class="btn-add">
                            +
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <?php endwhile; ?>

    </div>
</div>
</div>                
            <!-- ........................................................... -->
            <div class="section">
                <div class="title-bar">
                    <h2>پیشنهاد ویژه
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <g clip-path="url(#clip0_1105_10859)">
                                <path d="M19.0808 10.0007L19.9582 7.71211C20.0545 7.45962 19.9795 7.17339 19.7682 7.00216L17.8659 5.45976L17.4809 3.03868C17.4384 2.7712 17.2284 2.56246 16.9609 2.51996L14.5398 2.13499L12.9987 0.231369C12.8287 0.0201334 12.5362 -0.0548614 12.29 0.041382L10.0001 0.920072L7.71155 0.0426319C7.45782 -0.0548614 7.17409 0.0226333 7.00285 0.232619L5.46045 2.13624L3.03937 2.52121C2.77314 2.56371 2.56315 2.7737 2.52065 3.03993L2.13568 5.46101L0.23206 7.00341C0.0220746 7.17339 -0.0541702 7.45962 0.0420732 7.71211L0.919513 10.0007L0.0420732 12.2893C-0.0554201 12.5418 0.0220746 12.828 0.23206 12.998L2.13568 14.5391L2.52065 16.9602C2.56315 17.2277 2.77189 17.4377 3.03937 17.4802L5.46045 17.8652L7.00285 19.7675C7.17409 19.98 7.46032 20.055 7.7128 19.9575L10.0001 19.0813L12.2887 19.9588C12.3612 19.9863 12.4362 20 12.5125 20C12.6962 20 12.8774 19.9188 12.9987 19.7675L14.5398 17.8652L16.9609 17.4802C17.2284 17.4377 17.4384 17.2277 17.4809 16.9602L17.8659 14.5391L19.7682 12.998C19.9795 12.8268 20.0545 12.5418 19.9582 12.2893L19.0808 10.0007Z" fill="#FFC700"/>
                                <path d="M8.12524 8.75121C7.09156 8.75121 6.25037 7.91002 6.25037 6.87634C6.25037 5.84266 7.09156 5.00146 8.12524 5.00146C9.15892 5.00146 10.0001 5.84266 10.0001 6.87634C10.0001 7.91002 9.15892 8.75121 8.12524 8.75121ZM8.12524 6.25138C7.78026 6.25138 7.50028 6.53136 7.50028 6.87634C7.50028 7.22131 7.78026 7.50129 8.12524 7.50129C8.47021 7.50129 8.7502 7.22131 8.7502 6.87634C8.7502 6.53136 8.47021 6.25138 8.12524 6.25138Z" fill="#FAFAFA"/>
                                <path d="M11.875 15.0002C10.8413 15.0002 10.0001 14.159 10.0001 13.1254C10.0001 12.0917 10.8413 11.2505 11.875 11.2505C12.9087 11.2505 13.7499 12.0917 13.7499 13.1254C13.7499 14.159 12.9087 15.0002 11.875 15.0002ZM11.875 12.5004C11.5313 12.5004 11.25 12.7816 11.25 13.1254C11.25 13.4691 11.5313 13.7503 11.875 13.7503C12.2187 13.7503 12.5 13.4691 12.5 13.1254C12.5 12.7816 12.2187 12.5004 11.875 12.5004Z" fill="#FAFAFA"/>
                                <path d="M6.87526 15.0005C6.74901 15.0005 6.62277 14.963 6.51278 14.8843C6.23155 14.683 6.16655 14.293 6.36779 14.0118L12.6174 5.26241C12.8186 4.98118 13.2086 4.91619 13.4898 5.11742C13.771 5.31741 13.8348 5.70863 13.6348 5.98861L7.38522 14.738C7.26148 14.9093 7.07024 15.0005 6.87526 15.0005Z" fill="#FAFAFA"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_1105_10859">
                                    <rect width="20" height="20" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </h2>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalRightFull">مشاهده همه <i class="icon-left"></i></a>
                </div>
                 <?php
  $query="SELECT * FROM product WHERE category=1";
 $result=mysqli_query($link,$query);
  ?>
              <div class="wrap-swiper">
       <div class="swiper drink-swiper">
    <div class="swiper-wrapper">

        <?php while ($row = mysqli_fetch_array($result)) { ?>

        <div class="swiper-slide">

            <div class="tf-box-column lg">

                <div class="img-box">
                    <img src="images/products/<?php echo $row['pro_image']; ?>" alt="">
                </div>

                <div class="content-box">

                    <h3>
                        <a href="product-detail.php?id=<?php echo $row['pro_code']; ?>">
                            <?php echo $row['pro_name']; ?>
                        </a>
                    </h3>

                    <ul class="review">
                        <li>
                            <i class="icon-star"></i>
                            <span>4.8</span> (<?php echo $order_counts[$row['pro_code']] ?? 0; ?>)
                        </li>
                    </ul>

                    <div class="box-price">
                        <ul class="price">
                            <li class="accent">20% تخفیف</li>
                            <li class="del">
                                <?php echo number_format($row['pro_price']); ?>
                            </li>
                        </ul>

                        <a href="product-detail.php?id=<?php echo $row['pro_code']; ?>" class="btn-add">
                            +
                        </a>
                    </div>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>
</div>
    </div>
</div> 
 <div class="section recomand-sec">
                <div class="wrap-swiper">
                    <div class="swiper recomand-swiper">
                        <div class="swiper-wrapper pb-30">
                            <div class="swiper-slide ml-2">
                                <div class="recomand-box">
                                    <div class="img-box">
                                        <a href="product-detail.php" class="bg">
                                            <img src="images/food/recomand4.jpg" alt="تصویر">
                                        </a>
                                        <a href="product-detail.php" class="logo">
                                            <img src="images/food/recomand-logo-2.png" alt="لوگو">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="recomand-box">
                                    <div class="img-box">
                                        <a href="product-detail.php" class="bg">
                                            <img src="images/food/recomand2.jpg" alt="تصویر">
                                        </a>
                                        <a href="product-detail.php" class="logo">
                                            <img src="images/food/recomand-logo-2.png" alt="لوگو">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="recomand-box">
                                    <div class="img-box">
                                        <a href="product-detail.php" class="bg">
                                            <img src="images/food/recomand3.jpg" alt="تصویر">
                                        </a>
                                        <a href="product-detail.php" class="logo">
                                            <img src="images/food/recomand-logo-2.png" alt="لوگو">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>               

                          
                            <!-- ................................................... -->
        
<div class="section">
<div class="tabs-container">
<ul class="nav nav-tabs nav-justified mb-15" id="navtabs-drink1" role="tablist">
<li class="nav-item" role="presentation">
<a class="nav-link active" id="rating-tab" data-bs-toggle="tab" data-bs-target="#rating" role="tab">رتبه‌بندی</a>
</li>
<li class="nav-item" role="presentation">
<a class="nav-link" id="hot-tab" data-bs-toggle="tab" data-bs-target="#hot" role="tab">نوشیدنی گرم</a>
</li>
<li class="nav-item" role="presentation">
<a class="nav-link" id="cold-tab" data-bs-toggle="tab" data-bs-target="#cold" role="tab">نوشیدنی سرد</a>
</li>
</ul>
</div>
</div>
<!-- ................................................. -->
<?php
$q_rating = mysqli_query($link,
    "SELECT p.*, ROUND(AVG(r.rating),1) as avg_rating
     FROM product p
     LEFT JOIN review r ON r.pro_code = p.pro_code
     GROUP BY p.pro_code
     ORDER BY avg_rating DESC, p.pro_code DESC");
$q_hot    = mysqli_query($link, "SELECT * FROM product WHERE category=0");
$q_cold   = mysqli_query($link, "SELECT * FROM product WHERE category=1");

function render_products($result) {
    global $order_counts, $ratings;
    while($row = mysqli_fetch_assoc($result)) {
        $pro_code    = $row['pro_code'];
        $avg_rating  = $ratings[$pro_code]['avg'] ?? null;
        $review_cnt  = $ratings[$pro_code]['cnt'] ?? 0;
        $buyer_cnt   = $order_counts[$pro_code]   ?? 0;
        $rating_text = $avg_rating
            ? "<span>$avg_rating</span>&nbsp;($review_cnt نظر)"
            : "<span>—</span>&nbsp;($buyer_cnt خریدار)";
        ?>
      <li class="tf-box-row mb-12">
        <a href="product-detail.php?id=<?php echo $pro_code; ?>" class="img-box">
          <img src="images/products/<?php echo $row['pro_image']; ?>" alt="تصویر">
        </a>
        <div class="content-box">
          <h5><a href="product-detail.php?id=<?php echo $pro_code; ?>"><?php echo $row['pro_name']; ?></a></h5>
          <ul class="review">
            <li><i class="icon-star"></i><?php echo $rating_text; ?></li>
            <li class="dot-icon"></li>
            <li>16 دقیقه</li>
          </ul>
          <div class="box-price">
            <ul class="price">
              <li class="accent"><?php echo number_format($row['pro_price']); ?> تومان</li>
            </ul>
            <a href="product-detail.php?id=<?php echo $pro_code; ?>" class="btn-add">+</a>
          </div>
        </div>
      </li>
    <?php }
}
?>
<div class="tab-content">
  <ul class="tab-pane fade show active" id="rating" role="tabpanel" style="list-style:none; padding:0;">
    <?php render_products($q_rating); ?>
  </ul>
  <ul class="tab-pane fade" id="hot" role="tabpanel" style="list-style:none; padding:0;">
    <?php render_products($q_hot); ?>
  </ul>
  <ul class="tab-pane fade" id="cold" role="tabpanel" style="list-style:none; padding:0;">
    <?php render_products($q_cold); ?>
  </ul>
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
<!-- .............................. -->
<?php
$query = "SELECT * FROM product WHERE 1";
$result = mysqli_query($link, $query);
?>
<div class="modal fade modalRight" id="modalRightFull" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="header">
        <div class="tf-container">
          <div class="title-header-bar pt-20 pb-20 d-flex justify-content-between align-items-center">
            <h1 class="mb-0">کافه تاسیان</h1>
            <a href="#" class="btn-close-modal" data-bs-dismiss="modal"><i class="icon-right"></i></a>
          </div>
        </div>
      </div>
      
<ul class="tf-box-column sm d-flex flex-wrap justify-content-start">
<?php while ($row = mysqli_fetch_array($result)) { ?>
<li class="tf-box-column sm mx-2 mb-4" style="width:200px;">
  <div class="img-box mb-2 text-center">
    <img src="images/products/<?php echo ($row['pro_image']); ?>" alt="تصویر" class="img-fluid" style="max-width:100%; height:auto;">
  </div>
  <div class="content-box">
    <h3><a href="product-detail.php?id=<?php echo ($row['pro_code']); ?>"><?php echo ($row['pro_name']); ?></a></h3>
    <ul class="review mb-2">
      <li><i class="icon-star"></i> <span>4.8</span> (<?php echo $order_counts[$row['pro_code']] ?? 0; ?>)</li>
      <li class="dot-icon mx-2">•</li>
      <li>16 دقیقه</li>
    </ul>
    <div class="box-price d-flex justify-content-between align-items-center">
      <ul class="price mb-0">
        <li class="accent"><?php echo ($row['pro_price']); ?> تومان</li>
      <li class="del">150000 تومان</li> 
      </ul>
      <a href="product-detail.php?id=<?php echo ($row['pro_code']); ?>"class="btn-add">+</a>
    </div>
  </div>
</li>
<?php } ?>
</ul>
    </div>
  </div>
</div>

    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/swiper-bundle.min.js"></script>
    <script type="text/javascript" src="js/carousel.js"></script>
    <script type="text/javascript" src="js/sidebar.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
</body>
</html>