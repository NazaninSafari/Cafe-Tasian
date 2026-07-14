<?php
session_start();
$link = mysqli_connect('localhost','root','','Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());

if (!isset($_SESSION['state_login']) || $_SESSION['state_login'] !== true) {
    header("Location: sign-in.php");
    exit();
}

$username = mysqli_real_escape_string($link, $_SESSION['username']);
$error    = '';
$ok       = false;

// ثبت امتیاز
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pro_code = intval($_POST['pro_code'] ?? 0);
    $rating   = intval($_POST['rating']   ?? 0);
    $comment  = trim(mysqli_real_escape_string($link, $_POST['comment'] ?? ''));

    if ($rating < 1 || $rating > 5) {
        $error = 'لطفاً یک امتیاز بین ۱ تا ۵ انتخاب کنید.';
    } else {
        // بررسی اینکه کاربر این محصول رو خریده
        $check = mysqli_query($link,
            "SELECT id FROM `order` WHERE username='$username' AND pro_code='$pro_code' LIMIT 1");
        if (!$check || mysqli_num_rows($check) === 0) {
            $error = 'شما این محصول را خریداری نکرده‌اید.';
        } else {
            // ثبت یا آپدیت امتیاز
            $sql = "INSERT INTO review (pro_code, username, rating, comment)
                    VALUES ('$pro_code', '$username', '$rating', '$comment')
                    ON DUPLICATE KEY UPDATE rating='$rating', comment='$comment'";
            if (mysqli_query($link, $sql)) {
                $ok = true;
            } else {
                $error = 'خطا در ثبت امتیاز: ' . mysqli_error($link);
            }
        }
    }
}

// محصولات خریداری‌شده توسط کاربر
$purchased = mysqli_query($link,
    "SELECT DISTINCT p.pro_code, p.pro_name, p.pro_image, p.pro_price,
            ROUND(AVG(r.rating), 1) as avg_rating,
            COUNT(r.id) as review_count,
            my_r.rating as my_rating,
            my_r.comment as my_comment
     FROM `order` o
     JOIN product p ON o.pro_code = p.pro_code
     LEFT JOIN review r ON r.pro_code = p.pro_code
     LEFT JOIN review my_r ON my_r.pro_code = p.pro_code AND my_r.username = '$username'
     WHERE o.username = '$username'
     GROUP BY p.pro_code");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>امتیازدهی</title>
<link rel="stylesheet" href="css/boostrap.min.css">
<link rel="stylesheet" href="css/swiper-bundle.min.css">
<link rel="stylesheet" type="text/css" href="css/styles.css"/>
<link rel="stylesheet" href="fonts/font-icons.css">
<link rel="shortcut icon" href="images/logo/48.png"/>
<style>
.review-card {
    background: #fff;
    border: 0.5px solid #e5e5e5;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 14px;
}
.review-card-top {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
}
.review-product-img {
    width: 56px; height: 56px;
    border-radius: 10px;
    object-fit: cover;
    border: 0.5px solid #eee;
    flex-shrink: 0;
}
.review-product-name {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 4px;
}
.review-product-price {
    font-size: 12px;
    color: #888;
    margin: 0;
}
.avg-rating {
    margin-right: auto;
    text-align: center;
}
.avg-rating .score {
    font-size: 20px;
    font-weight: 800;
    color: #f39c12;
    line-height: 1;
}
.avg-rating .count {
    font-size: 11px;
    color: #aaa;
}

/* ستاره‌ها */
.star-group {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 4px;
    margin-bottom: 12px;
}
.star-group input { display: none; }
.star-group label {
    font-size: 28px;
    color: #ddd;
    cursor: pointer;
    transition: color .15s;
    line-height: 1;
}
.star-group input:checked ~ label,
.star-group label:hover,
.star-group label:hover ~ label {
    color: #f39c12;
}

.comment-input {
    width: 100%;
    background: #f8f8f8;
    border: 0.5px solid #e5e5e5;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    font-family: inherit;
    color: #1a1a1a;
    resize: vertical;
    min-height: 70px;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s;
    margin-bottom: 10px;
}
.comment-input:focus { border-color: #033f38; }

.btn-submit {
    background: #033f38;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: background .2s;
}
.btn-submit:hover { background: #055248; }

.my-review-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e8f2f0;
    color: #033f38;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 10px;
}
.edit-review-btn {
    background: none;
    border: none;
    color: #033f38;
    font-size: 12px;
    text-decoration: underline;
    cursor: pointer;
    font-family: inherit;
    margin-right: 8px;
}

.alert-ok  { background:#e8f2f0;border:0.5px solid #b2d8d0;color:#033f38;padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:14px; }
.alert-err { background:#fff0f0;border:0.5px solid #f5c1c1;color:#c0392b;padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:14px; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #aaa;
}
.empty-state i { font-size: 40px; display: block; margin-bottom: 12px; opacity: .3; }
</style>
</head>
<body>

<div class="preload preload-container">
    <div class="preload-logo"><div class="spinner"></div></div>
</div>

<div class="header">
    <div class="tf-container">
        <div class="title-header-bar pt-20">
            <a href="profile.php" class="back-btn"><i class="icon-right"></i></a>
            <h1>امتیازدهی</h1>
        </div>
    </div>
</div>

<div class="app pt-70 pb-80">
    <div class="tf-container">

        <?php if ($ok): ?>
        <div class="alert-ok">✅ امتیاز شما با موفقیت ثبت شد.</div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <?php
        $count = 0;
        while ($row = mysqli_fetch_assoc($purchased)):
            $count++;
            $has_reviewed = !is_null($row['my_rating']);
        ?>
        <div class="review-card">
            <!-- اطلاعات محصول -->
            <div class="review-card-top">
                <?php if ($row['pro_image']): ?>
                <img src="images/products/<?= htmlspecialchars($row['pro_image']) ?>"
                     class="review-product-img" alt="">
                <?php else: ?>
                <div class="review-product-img" style="background:#e8f2f0;display:flex;align-items:center;justify-content:center;font-size:24px;">☕</div>
                <?php endif; ?>
                <div style="flex:1;">
                    <p class="review-product-name"><?= htmlspecialchars($row['pro_name']) ?></p>
                    <p class="review-product-price"><?= number_format($row['pro_price']) ?> تومان</p>
                </div>
                <?php if ($row['avg_rating']): ?>
                <div class="avg-rating">
                    <div class="score">⭐ <?= $row['avg_rating'] ?></div>
                    <div class="count"><?= $row['review_count'] ?> نظر</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- اگه قبلاً امتیاز داده -->
            <?php if ($has_reviewed): ?>
            <div class="my-review-badge">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                <span style="color:<?= $s <= $row['my_rating'] ? '#f39c12' : '#ddd' ?>">★</span>
                <?php endfor; ?>
                امتیاز شما: <?= $row['my_rating'] ?> از ۵
            </div>
            <?php if ($row['my_comment']): ?>
            <p style="font-size:12px;color:#666;margin:0 0 10px;padding:8px 12px;background:#f8f8f8;border-radius:8px;"><?= htmlspecialchars($row['my_comment']) ?></p>
            <?php endif; ?>
            <button class="edit-review-btn" onclick="toggleForm('form-<?= $row['pro_code'] ?>')">ویرایش امتیاز</button>
            <?php endif; ?>

            <!-- فرم امتیازدهی -->
            <div id="form-<?= $row['pro_code'] ?>" <?= $has_reviewed ? 'style="display:none;"' : '' ?>>
                <form method="POST" action="">
                    <input type="hidden" name="pro_code" value="<?= $row['pro_code'] ?>">
                    <div class="star-group">
                        <?php for ($s = 5; $s >= 1; $s--): ?>
                        <input type="radio" name="rating" id="star<?= $s ?>-<?= $row['pro_code'] ?>"
                               value="<?= $s ?>"
                               <?= (isset($row['my_rating']) && $row['my_rating'] == $s) ? 'checked' : '' ?>>
                        <label for="star<?= $s ?>-<?= $row['pro_code'] ?>">★</label>
                        <?php endfor; ?>
                    </div>
                    <textarea class="comment-input" name="comment"
                              placeholder="نظر خود را بنویسید (اختیاری)..."><?= htmlspecialchars($row['my_comment'] ?? '') ?></textarea>
                    <button type="submit" class="btn-submit">
                        <?= $has_reviewed ? '💾 ذخیره تغییرات' : '✅ ثبت امتیاز' ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>

        <?php if ($count === 0): ?>
        <div class="empty-state">
            <i class="icon icon-buy"></i>
            <p style="font-size:14px;">هنوز محصولی خریداری نکرده‌اید</p>
            <a href="index.php" style="color:#033f38;font-size:13px;font-weight:600;">مشاهده منو ←</a>
        </div>
        <?php endif; ?>

    </div>
</div>

<div class="menubar-footer footer-fixed">
    <ul class="inner">
        <li><a href="index.php"><span class="icon-home"></span> خانه</a></li>
        <li><a href="cart.php"><span class="icon-buy"></span> سفارش</a></li>
        <li><a href="about-us.html"><span class="icon-heart"></span> درباره ما</a></li>
        <li><a href="profile.php"><span class="icon-profile"></span> پروفایل</a></li>
    </ul>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/swiper-bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
function toggleForm(id) {
    const form = document.getElementById(id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>
