<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());

if (!isset($_SESSION['state_login']) || $_SESSION['state_login'] !== true) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];
$error   = '';
$ok      = false;

// حذف آدرس
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    mysqli_query($link, "DELETE FROM address WHERE id=$del_id AND user_id=$user_id");
    header("Location: addresses.php?deleted=1");
    exit();
}

// افزودن آدرس جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim(mysqli_real_escape_string($link, $_POST['title']   ?? ''));
    $address = trim(mysqli_real_escape_string($link, $_POST['address'] ?? ''));
    $mobile  = trim(mysqli_real_escape_string($link, $_POST['mobile']  ?? ''));

    if (!$title)                                                    $error = 'عنوان آدرس را وارد کنید.';
    elseif (!$address)                                              $error = 'آدرس را وارد کنید.';
    elseif (strlen($mobile) !== 11 || substr($mobile,0,2) !== '09') $error = 'شماره موبایل معتبر نیست.';
    else {
        $sql = "INSERT INTO address (user_id, title, address, mobile)
                VALUES ($user_id, '$title', '$address', '$mobile')";
        if (mysqli_query($link, $sql)) {
            $ok = true;
        } else {
            $error = 'خطا در ذخیره: ' . mysqli_error($link);
        }
    }
}

// دریافت آدرس‌های کاربر
$addresses = mysqli_query($link, "SELECT * FROM address WHERE user_id=$user_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مکان‌های تحویل</title>
<link rel="stylesheet" href="css/boostrap.min.css">
<link rel="stylesheet" href="css/swiper-bundle.min.css">
<link rel="stylesheet" type="text/css" href="css/styles.css"/>
<link rel="stylesheet" href="fonts/font-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="shortcut icon" href="images/logo/48.png"/>
<style>
.addr-card {
    background: #fff;
    border: 0.5px solid #e5e5e5;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 12px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.addr-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    background: #e8f2f0;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.addr-icon i { font-size: 20px; color: #033f38; }
.addr-info { flex: 1; }
.addr-title { font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0 0 4px; }
.addr-text  { font-size: 12px; color: #666; margin: 0 0 4px; line-height: 1.6; }
.addr-mobile{ font-size: 12px; color: #999; margin: 0; direction: ltr; text-align: right; }
.addr-delete {
    background: #fff0f0; border: none; border-radius: 8px;
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0; text-decoration: none;
}
.addr-delete i { font-size: 16px; color: #e05c5c; }

.form-card {
    background: #fff;
    border: 0.5px solid #e5e5e5;
    border-radius: 14px;
    padding: 20px 16px;
    margin-bottom: 80px;
}
.form-card h3 {
    font-size: 15px; font-weight: 600;
    color: #033f38; margin: 0 0 16px;
    display: flex; align-items: center; gap: 8px;
}
.field { margin-bottom: 14px; }
.field label { display: block; font-size: 12px; color: #888; margin-bottom: 6px; }
.field input,
.field textarea {
    width: 100%;
    background: #f8f8f8;
    border: 0.5px solid #e5e5e5;
    border-radius: 10px;
    padding: 11px 14px;
    font-size: 14px;
    color: #1a1a1a;
    font-family: inherit;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.field input:focus,
.field textarea:focus { border-color: #033f38; }
.field textarea { resize: vertical; min-height: 80px; }

.alert-ok  { background:#e8f2f0; border:0.5px solid #b2d8d0; color:#033f38; padding:11px 14px; border-radius:10px; font-size:13px; margin-bottom:14px; }
.alert-err { background:#fff0f0; border:0.5px solid #f5c1c1; color:#c0392b; padding:11px 14px; border-radius:10px; font-size:13px; margin-bottom:14px; }
.alert-del { background:#fff8e6; border:0.5px solid #f5dfa0; color:#856404; padding:11px 14px; border-radius:10px; font-size:13px; margin-bottom:14px; }

.empty-state {
    text-align: center; padding: 40px 20px;
    color: #aaa; font-size: 13px;
}
.empty-state i { font-size: 40px; display: block; margin-bottom: 10px; color: #ccc; }

.btn-submit {
    width: 100%;
    background: #033f38;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 14px;
    font-size: 15px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: background .2s;
}
.btn-submit:hover { background: #055248; }
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
            <h1>مکان‌های تحویل</h1>
        </div>
    </div>
</div>

<div class="app pt-70 pb-20">
    <div class="tf-container">

        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert-del">🗑 آدرس با موفقیت حذف شد.</div>
        <?php endif; ?>

        <?php if ($ok): ?>
        <div class="alert-ok">✅ آدرس جدید با موفقیت ذخیره شد.</div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <!-- لیست آدرس‌ها -->
        <?php
        $count = 0;
        while ($row = mysqli_fetch_assoc($addresses)):
            $count++;
            $icons = ['خانه'=>'ti-home','محل کار'=>'ti-building','دانشگاه'=>'ti-school','مدرسه'=>'ti-school'];
            $icon  = $icons[$row['title']] ?? 'ti-map-pin';
        ?>
        <div class="addr-card">
            <div class="addr-icon">
                <i class="ti <?= $icon ?>"></i>
            </div>
            <div class="addr-info">
                <p class="addr-title"><?= htmlspecialchars($row['title']) ?></p>
                <p class="addr-text"><?= htmlspecialchars($row['address']) ?></p>
                <p class="addr-mobile"><?= htmlspecialchars($row['mobile']) ?></p>
            </div>
            <a href="addresses.php?delete=<?= $row['id'] ?>"
               class="addr-delete"
               onclick="return confirm('این آدرس حذف شود؟')">
                <i class="ti ti-trash"></i>
            </a>
        </div>
        <?php endwhile; ?>

        <?php if ($count === 0): ?>
        <div class="empty-state">
            <i class="ti ti-map-off"></i>
            هنوز آدرسی ثبت نکرده‌اید
        </div>
        <?php endif; ?>

        <!-- فرم افزودن آدرس -->
        <div class="form-card">
            <h3><i class="ti ti-plus" style="font-size:18px;"></i> افزودن آدرس جدید</h3>
            <form method="POST" action="">
                <div class="field">
                    <label>عنوان آدرس</label>
                    <input type="text" name="title" placeholder="مثال: خانه، محل کار، دانشگاه"
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>آدرس کامل</label>
                    <textarea name="address" placeholder="آدرس دقیق خود را وارد کنید..."><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
                <div class="field">
                    <label>شماره موبایل گیرنده</label>
                    <input type="tel" name="mobile" maxlength="11"
                           placeholder="09xxxxxxxxx"
                           value="<?= htmlspecialchars($_POST['mobile'] ?? $_SESSION['mobile']) ?>"
                           style="direction:ltr;text-align:right;">
                </div>
                <button type="submit" class="btn-submit">ذخیره آدرس</button>
            </form>
        </div>

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
</body>
</html>
