<?php
session_start();

$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());

$notif_count    = 0;
$order_notifs   = [];
$general_notifs = [];

if (isset($_SESSION['state_login']) && $_SESSION['state_login'] === true) {

    // اطلاعیه‌های سفارش — آخرین ۵ سفارش کاربر
    $username  = mysqli_real_escape_string($link, $_SESSION['username']);
    $order_res = mysqli_query($link,
        "SELECT trackcode, orderdate, state, SUM(pro_price*pro_qty) as total
         FROM `order`
         WHERE username='$username'
         GROUP BY trackcode
         ORDER BY MAX(id) DESC
         LIMIT 5");
    if ($order_res) {
        while ($row = mysqli_fetch_assoc($order_res)) {
            $order_notifs[] = $row;
            $notif_count++;
        }
    }

    // اطلاعیه‌های عمومی — فقط اگه جدول notification وجود داشت
    $table_check = mysqli_query($link, "SHOW TABLES LIKE 'notification'");
    if ($table_check && mysqli_num_rows($table_check) > 0) {
        $general_res = mysqli_query($link,
            "SELECT * FROM notification ORDER BY created_at DESC LIMIT 5");
        if ($general_res) {
            while ($row = mysqli_fetch_assoc($general_res)) {
                $general_notifs[] = $row;
                $notif_count++;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/boostrap.min.css">
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <link rel="stylesheet"type="text/css" href="css/styles.css"/>
    <!-- Icons -->
    <link rel="stylesheet" href="fonts/font-icons.css">
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="images/logo/48.png" />
    <link rel="apple-touch-icon-precomposed" href="images/logo/48.png" />
        
    <title>پروفایل</title>
</head>

<body class="appProfile">
     <!-- preloade -->
     <div class="preload preload-container">
        <div class="preload-logo">
          <div class="spinner"></div>
        </div>
      </div>
    <!-- /preload -->
    <div class="app profile pb-90">
        <div class="title-header-bar fixed-top bg-white">
            <a href="#" class="back-btn"><i class="icon-right"></i></a>
            <h1>پروفایل</h1>

            
        </div>
        <div class="tf-container pt-80">
            <div class="box-profile">
                <div class="img" style="width: 76px;height: 76px; border-radius: 100%;object-fit: cover;">
                    <img src="images/avt/user.jpg" alt="تصویر">
                </div>
                <div class="info">

<h3>
<?php
if (isset($_SESSION['username']) && isset($_SESSION['mobile'])) {
    echo  $_SESSION['username'];
} else {
    echo "نام کاربری";
}
?>
</h3>
<span>
<?php
if (isset($_SESSION['mobile'])) {
    echo  $_SESSION['mobile'];
} else {
    echo "شماره موبایل";
}
?>
</span>

                </div>
            </div>
            <ul class="mt-30">
                <li>
                    <a href="account-information.php" class="list-view line pb-16">
                        <i class="icon icon-profile"></i>
                        <span>اطلاعات حساب</span>
                        <i class="icon-left"></i>
                    </a>
                </li>
                <li>
                    <a href="addresses.php" class="list-view pt-16">
                        <i class="icon icon-location"></i>
                        <span>مکان‌های تحویل</span>
                        <i class="icon-left"></i>
                    </a>
                </li>
            </ul>
            <ul class="mt-50">
                <li>
                    <a href="my_orders.php" class="list-view line pb-16">
                        <i class="icon icon-buy"></i>
                        <span>پیگیری سفارش</span>
                        <i class="icon-left"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="list-view line pt-16 pb-16" onclick="openNotifModal(); return false;">
                        <i class="icon icon-notification"></i>
                        <span>اطلاعیه‌ها</span>
                        <?php if ($notif_count > 0): ?>
                        <span style="margin-right:auto;background:#033f38;color:#fff;font-size:11px;padding:2px 8px;border-radius:20px;font-weight:600;"><?= $notif_count ?></span>
                        <?php endif; ?>
                        <i class="icon-left"></i>
                    </a>
                </li>
                <li>
                    <a href="reviews.php" class="list-view pt-16">
                        <i class="icon icon-star"></i>
                        <span>امتیازدهی</span>
                        <i class="icon-left"></i>
                    </a>
                </li>
            </ul>
            <a href="logout.php" class="list-view mt-50">
                <i class="icon icon-logout"></i>
                <span>خروج</span>
                <i class="icon-left"></i>
            </a>
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

<!-- مودال -->
<div id="modalRightFull" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;">
    <!-- overlay -->
    <div onclick="closeNotifModal()" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);"></div>
    <!-- panel — از سمت راست باز میشه -->
    <div style="position:absolute;top:0;right:0;bottom:0;width:85%;max-width:400px;background:#fff;overflow-y:auto;box-shadow:-4px 0 20px rgba(0,0,0,.2);" id="notif-panel">
        <div style="padding:20px 16px;border-bottom:1px solid #eee;display:flex;align-items:center;gap:12px;direction:rtl;">
            <a href="#" onclick="closeNotifModal();return false;" style="color:#333;font-size:22px;text-decoration:none;line-height:1;">&#8592;</a>
            <h1 style="font-size:18px;font-weight:700;margin:0;">اطلاعیه‌ها</h1>
        </div>
        <div style="padding:20px 16px;direction:rtl;">



            <?php if (empty($order_notifs) && empty($general_notifs)): ?>
                <div style="text-align:center;padding:60px 20px;color:#aaa;">
                    <i class="icon icon-notification" style="font-size:40px;display:block;margin-bottom:12px;opacity:.3;"></i>
                    <p style="font-size:14px;">اطلاعیه‌ای وجود ندارد</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($order_notifs)): ?>
                <h5 style="font-size:14px;font-weight:700;margin-bottom:12px;">سفارشات اخیر</h5>
                <?php
                $state_labels = [
                    0 => ['ثبت شده',           '#f39c12'],
                    1 => ['در حال آماده‌سازی', '#3498db'],
                    2 => ['آماده تحویل',        '#e67e22'],
                    3 => ['تحویل داده شده',     '#2ecc71'],
                ];
                foreach ($order_notifs as $notif):
                    $state = intval($notif['state']);
                    [$label, $color] = $state_labels[$state] ?? ['نامشخص','#999'];
                ?>
                <a href="my_orders.php" style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0;text-decoration:none;color:inherit;">
                    <div style="width:44px;height:44px;border-radius:10px;background:#e8f2f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="icon icon-buy" style="color:#033f38;font-size:20px;"></i>
                    </div>
                    <div>
                        <p style="margin:0;font-size:13px;font-weight:600;">سفارش #<?= $notif['trackcode'] ?></p>
                        <p style="margin:4px 0 0;font-size:12px;color:#666;">مبلغ <?= number_format($notif['total']) ?> تومان — <span style="color:<?= $color ?>;font-weight:600;"><?= $label ?></span></p>
                        <p style="margin:4px 0 0;font-size:11px;color:#aaa;"><?= $notif['orderdate'] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($general_notifs)): ?>
                <h5 style="font-size:14px;font-weight:700;margin:20px 0 12px;">اطلاعیه‌های کافه</h5>
                <?php foreach ($general_notifs as $notif):
                    $diff = time() - strtotime($notif['created_at']);
                    if      ($diff < 3600)  $time_ago = intval($diff/60)   . ' دقیقه پیش';
                    elseif  ($diff < 86400) $time_ago = intval($diff/3600) . ' ساعت پیش';
                    else                    $time_ago = intval($diff/86400) . ' روز پیش';
                ?>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0;">
                    <div style="width:44px;height:44px;border-radius:10px;background:#e8f2f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="icon icon-notification" style="color:#033f38;font-size:20px;"></i>
                    </div>
                    <div>
                        <p style="margin:0;font-size:13px;font-weight:600;"><?= htmlspecialchars($notif['title']) ?></p>
                        <p style="margin:4px 0 0;font-size:12px;color:#666;"><?= htmlspecialchars($notif['message']) ?></p>
                        <p style="margin:4px 0 0;font-size:11px;color:#aaa;"><?= $time_ago ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
function openNotifModal() {
    document.getElementById('modalRightFull').style.display = 'block';
    document.body.style.overflow = 'hidden';
}
function closeNotifModal() {
    document.getElementById('modalRightFull').style.display = 'none';
    document.body.style.overflow = '';
}
// بستن با کلید Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeNotifModal();
});
</script>


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/swiper-bundle.min.js"></script>
<script src="js/carousel.js"></script>
<script src="js/sidebar.js"></script>
<script src="js/main.js"></script>
</body>

</html>