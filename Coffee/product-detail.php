<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) {
    die("خطای اتصال به پایگاه داده: " . mysqli_connect_error());
}
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $query = "SELECT * FROM product WHERE pro_code = $product_id";
    $result = mysqli_query($link, $query);
    $product = mysqli_fetch_assoc($result);

    // تعداد خریداران این محصول
    $oc = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM `order` WHERE pro_code=$product_id"));
    $buyer_count = $oc[0] ?? 0;

    // امتیاز واقعی از جدول review
    $rating_check = mysqli_query($link, "SHOW TABLES LIKE 'review'");
    if ($rating_check && mysqli_num_rows($rating_check) > 0) {
        $rating_res = mysqli_fetch_row(mysqli_query($link,
            "SELECT ROUND(AVG(rating),1), COUNT(*) FROM review WHERE pro_code=$product_id"));
        $avg_rating   = $rating_res[0] ?? null;
        $review_count = $rating_res[1] ?? 0;

        // دریافت نظرات کاربران
        $reviews = mysqli_query($link,
            "SELECT username, rating, comment, created_at
             FROM review
             WHERE pro_code=$product_id AND comment != ''
             ORDER BY created_at DESC");
    } else {
        $avg_rating   = null;
        $review_count = 0;
        $reviews      = null;
    }
} else {
    // اگر شناسه محصول وارد نشده باشد، به صفحه اصلی برگردید
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>جزئیات محصول</title>
    <link rel="stylesheet" href="css/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <!-- فونت‌ها و آیکون‌ها -->
    <link rel="stylesheet" href="fonts/font-icons.css"/>
    <!-- ................................ -->
    <style>
        .btn-quantity{
    background: #ffffff !important;
    border: 1px solid #ddd;
    color: #000;
}

.btn-quantity:focus,
.btn-quantity:active{
    background: #ffffff !important;
    color: #000 !important;
    box-shadow: none !important;
    outline: none !important;
}
    </style>
    <!-- ....................................... -->
</head>
<body class="appDetails">
    <!-- هدر -->
    <div class="header absolute">
        <div class="tf-container">
            <div class="d-flex justify-between mt-15 align-center">
                <a href="index.php" class="back-btn primary"><i class="icon-right"></i></a>
            </div>
        </div>
    </div>

    <!-- جزئیات محصول -->
    <div class="app">
        <div class="banner-wrapper mb--30">
            <img src="images/products/<?php echo $product['pro_image']; ?>" alt="تصویر" class="banner-img1"/>
        </div>
        <div class="bg-white relative lr-top-radius">
            <div class="tf-container">
                <div class="title-detail pt-20">
                    <div class="content">
                        <h1><a href="#" class="text-primary"><?php echo $product['pro_name']; ?></a></h1>
                        <ul class="review">
                            <li class="text">
                                <i class="icon-star"></i>
                                <span class="text-primary"><?= $avg_rating ?? '—' ?></span>&nbsp;(<?= $review_count > 0 ? $review_count . ' نظر' : $buyer_count . ' خریدار' ?>)
                            </li>
                            <li class="dot-icon st1"></li>
                            <li class="text">18 دقیقه</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

       <!-- فرم افزودن به سبد -->
<form method="POST" action="cart.php">
    <input type="hidden" name="pro_code" value="<?php echo $product['pro_code']; ?>"/>
    <!-- قسمت‌های قبلی و استایل‌های موجود -->
    <div class="section pb-70">
        <div class="tf-container">
            <!-- بخش تعداد -->
            <h4 class="pt-24">تعداد</h4>
            <div class="sec-qty mt-12">
                <button type="button" class="btn-quantity minus-btn">
                    <i class="icon-minus"></i>
                </button>
                <input type="number" name="quantity" value="1" min="1">
                <button type="button" class="btn-quantity plus-btn">
                    <i class="icon-plus"></i>
                </button>
            </div>
          <div class="sec-size mt-30 mb-30">
                    <h4 class="mb-12">اندازه *</h4>
                    <ul class="mt-12">
                        <li class="size-item pb-12">
                            <input class="form-check-input" type="radio" name="checkSize" id="checkSize1" value="small">
                            <label class="form-check-label st2" for="checkSize1"></label>
                            <p>کوچک</p>
                            <a href="#" class="text">+ 0 تومان</a>
                        </li>
                        <li class="size-item pt-12 pb-12">
                            <input class="form-check-input" type="radio" name="checkSize" id="checkSize2" value="medium" checked>
                            <label class="form-check-label st2" for="checkSize2"></label>
                            <p>متوسط</p>
                            <a href="#">+ ۵ تومان</a>
                        </li>
                        <li class="size-item pt-12">
                            <input class="form-check-input" type="radio" name="checkSize" id="checkSize3" value="large">
                            <label class="form-check-label st2" for="checkSize3"></label>
                            <p>بزرگ</p>
                            <a href="#">+ ۸ تومان</a>  
                        </li>
                    </ul>







                </div>
            <!-- قیمت و دکمه افزودن -->
            <div class="bottom-fixed bg-white">
    <div class="inner mb-20 box-total flex">

        <div class="total">
            <p>قیمت کل:</p>
            <span id="total-price"><?php echo $product['pro_price']; ?></span>تومان
        </div>

        <button type="submit" class="tf-btn large primary">
            <i class="icon-buy"></i>
            افزودن به سبد خرید
        </button>

    </div>
</div>
</div></form>

<?php if ($reviews && mysqli_num_rows($reviews) > 0): ?>
<div class="tf-container" style="margin-top:20px;padding-bottom:100px;">
    <h4 style="font-size:16px;font-weight:700;margin-bottom:14px;">
        نظرات کاربران
        <span style="font-size:13px;font-weight:400;color:#888;">(<?= $review_count ?>)</span>
    </h4>

    <?php while ($rev = mysqli_fetch_assoc($reviews)): ?>
    <div style="background:#fff;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px 16px;margin-bottom:12px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:34px;height:34px;border-radius:50%;background:#e8f2f0;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#033f38;">
                    <?= mb_substr($rev['username'], 0, 1, 'UTF-8') ?>
                </div>
                <span style="font-size:13px;font-weight:600;color:#1a1a1a;"><?= htmlspecialchars($rev['username']) ?></span>
            </div>
            <div style="display:flex;gap:2px;">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                <span style="color:<?= $s <= $rev['rating'] ? '#f39c12' : '#ddd' ?>;font-size:14px;">★</span>
                <?php endfor; ?>
            </div>
        </div>
        <p style="font-size:13px;color:#444;line-height:1.7;margin:0 0 6px;"><?= htmlspecialchars($rev['comment']) ?></p>
        <span style="font-size:11px;color:#bbb;"><?= $rev['created_at'] ?></span>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>
    

    <script src="js/jquery.min.js"></script>
    <script src="js/swiper-bundle.min.js"></script>
    <script src="js/carousel.js"></script>
    <script src="js/sidebar.js"></script>
    <script src="js/main.js"></script>

    <script>
// سایز محصول
const basePrice = <?php echo $product['pro_price']; ?>;

const qtyInput = document.querySelector('input[name="quantity"]');
const priceBox = document.getElementById('total-price');

function updatePrice() {

    let qty = parseInt(qtyInput.value);

    let sizeExtra = 0;

    let selectedSize =
        document.querySelector('input[name="checkSize"]:checked').value;

    if(selectedSize == "medium"){
        sizeExtra = 5000;
    }

    if(selectedSize == "large"){
        sizeExtra = 8000;
    }

    let total = (basePrice + sizeExtra) * qty;

    priceBox.innerText = total.toLocaleString();
}

qtyInput.addEventListener('input', updatePrice);

document.querySelectorAll('input[name="checkSize"]').forEach(function(item){
    item.addEventListener('change', updatePrice);
});

updatePrice();
// تعداد محصول
$('.plus-btn, .minus-btn').on('click', function(){

    setTimeout(function(){updatePrice();}, 50);

});


</script>
</body>
</html>