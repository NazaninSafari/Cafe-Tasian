<?php

session_start();

// اتصال به پایگاه داده
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');

if (mysqli_connect_errno()) {
    die("خطای اتصال به پایگاه داده: " . mysqli_connect_error());
}

if (isset($_POST['signup'])) {

    if (
        isset($_POST['username']) && !empty($_POST['username']) &&
        isset($_POST['mobile']) && !empty($_POST['mobile']) &&
        isset($_POST['password']) && !empty($_POST['password']) &&
        isset($_POST['repassword']) && !empty($_POST['repassword'])
    ) {

        $username = trim($_POST['username']);
        $mobile_up = trim($_POST['mobile']);
        $pass_up = $_POST['password'];
        $repass_up = $_POST['repassword'];

    } else {
        exit("همه فیلدها را پر کنید!");
    }

    // اعتبارسنجی شماره موبایل
    if (strlen($mobile_up) !== 11 || substr($mobile_up, 0, 2) !== '09') {
        die('شماره موبایل را درست وارد کنید (مثال: 09121234567)');
    }

    // اعتبارسنجی نام کاربری
    if (strlen($username) < 3 || strlen($username) > 40) {
        die('نام کاربری باید بین 3 تا 40 کاراکتر باشد');
    }

    // اعتبارسنجی رمز عبور
    if (strlen($pass_up) < 4) {
        die('کلمه عبور باید حداقل دارای 4 کاراکتر باشد');
    }

    // بررسی تطابق رمز عبور
    if ($pass_up !== $repass_up) {
        die('کلمه عبور و تکرار آن مشابه نیست');
    }

    // جلوگیری از SQL Injection
    $username = mysqli_real_escape_string($link, $username);
    $mobile_up = mysqli_real_escape_string($link, $mobile_up);

    // هش کردن رمز عبور
    $hashed_password = password_hash($pass_up, PASSWORD_DEFAULT);

    // ثبت اطلاعات کاربر
    $query = "INSERT INTO `user` (`username`, `mobile`, `password`)
              VALUES ('$username', '$mobile_up', '$hashed_password')";

    if (mysqli_query($link, $query)) {

        // گرفتن id کاربر تازه ثبت‌شده
        $new_id = mysqli_insert_id($link);

        // لاگین خودکار بعد از ثبت‌نام
        $_SESSION['id']           = $new_id;
        $_SESSION['username']     = $username;
        $_SESSION['mobile']       = $mobile_up;
        $_SESSION['state_login']  = true;

        echo "
        <div class='alert alert-success text-center'>
            <b>عضویت شما با نام کاربری {$username} در کافه تاسیان با موفقیت انجام شد</b>
        </div>
        <meta http-equiv='refresh' content='2; url=profile.php'>
        ";

    } else {

        echo "
        <div class='alert alert-danger text-center'>
            <b>عضویت شما در کافه تاسیان انجام نشد</b>
        </div>
        ";

    }
}

?>


<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/boostrap.min.css">
    
    <link rel="stylesheet"type="text/css" href="css/styles.css"/>
    <!-- Icons -->
    <link rel="stylesheet" href="fonts/font-icons.css">
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="images/logo/48.png" />
    <link rel="apple-touch-icon-precomposed" href="images/logo/48.png" />
    
    <title>ورود</title>
</head>

<body>
     <div class="preload preload-container">
        <div class="preload-logo">
          <div class="spinner"></div>
        </div>
      </div>
    <div class="account-area" style="background-image: url('images/background/bg-1.png')">
        <div class="tf-container">
            <div class="logo-app pt-20">
                <img src="images/logo/logo-1.png" alt="تصویر لوگو">
            </div>
            <div class="tf-title pt-16">
                <h1>خوش آمدید</h1>
                <p>سلام، لطفاً وارد شوید تا ادامه دهید</p>
            </div>
            <div class="acount-box">
                <ul class="nav nav-tabs mb-23" id="account-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="auth-tab" data-bs-toggle="tab" data-bs-target="#signin" role="tab" aria-controls="signin" aria-selected="true">ورود</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#signup" role="tab" aria-controls="signup" aria-selected="false">ثبت نام</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="signin" role="tabpanel">
                        <?php
                        if(isset($_SESSION["state_login"]) && $_SESSION["state_login"]===true){
                       ?>
                        <script type="text/javascript">
                             location.replace("profile.php");
                        </script>
                          <?php
                       }
                            ?>
                        <br>
                        <form action="" method="post"  class="tf-form">
                            <div class="group-input mb-15">
                                <i class="icon icon-phone"></i>
                                <input style="direction: rtl;" name="Mobile" type="tel" maxlength="11" placeholder="شماره تلفن خود را وارد کنید">
                            </div>
                            <div class="group-input mb-15 group-ip-password">
                                <i class="icon icon-lock"></i>
                                <input required class="password-field" name="pass" id="pass" type="password" placeholder="رمز عبور خود را وارد کنید">
                                <div class="box-auth-show">
                                    <span class="show-pass">
                                        <i class="icon-eye-hide"></i>
                                        <i class="icon-eye-show"></i>
                                    </span>
                                </div>
                            </div><br><br><br><br>
                          <?php
if (isset($_SESSION["state_login"]) && $_SESSION["state_login"] === true) {
    header("Location: profile.php");
    exit();
}

$error='-';

// بخش ورود
if (isset($_POST['login'])) {
    // بررسی فرم ورود
    if (isset($_POST['Mobile']) && !empty($_POST['Mobile']) && isset($_POST['pass']) && !empty($_POST['pass'])) {
        $mobile_in = $_POST['Mobile'];
        $pass_in = $_POST['pass'];
    } else {
        exit("همه فیلد ها پر نشده!");
    }

    // اتصال به پایگاه داده
    $link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
    if (mysqli_connect_errno()) {
        die("خطای اتصال به پایگاه داده: " . mysqli_connect_error());
    }

    // بررسی صحت شماره موبایل
    if (strlen($mobile_in) !== 11 || substr($mobile_in, 0, 2) !== '09') {
        die('شماره موبایل را درست وارد کنید (مثال: 09121234567)');
    }
    // بررسی طول پسورد
    if (strlen($pass_in) < 4) {
        die('کلمه عبور باید حداقل دارای 4 کاراکتر باشد');
    }

    // جلوگیری از SQL Injection
    $mobile_in = mysqli_real_escape_string($link, $mobile_in);
   
    // جستجو در دیتابیس
   $query = "SELECT * FROM user WHERE mobile='$mobile_in'";
$result = mysqli_query($link, $query);

if (!$result) {
    die("خطا در اجرای کوئری: " . mysqli_error($link));
}

$row = mysqli_fetch_assoc($result);

if ($row && password_verify($pass_in, $row['password'])) {
// ...............
session_regenerate_id(true);
// ...............
    $_SESSION['id'] = $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['mobile'] = $row['mobile'];
    $_SESSION["state_login"] = true;

    // if ($row["type"] == 0) {
    //     $_SESSION["user_type"] = "public";
    // } elseif ($row["type"] == 1) {
    //     $_SESSION["user_type"] = "admin";
    // }
    if ($row["type"] == 1) {
    header("Location: admin/index.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}

    echo "<div class='alert alert-success text-center'>
            <b>{$row['username']} به کافه تاسیان خوش آمدید</b>
          </div>
          <meta http-equiv='refresh' content='2; url=profile.php'>";

}else {

    echo "<div class='alert alert-danger text-center'>
            <b>شماره موبایل یا رمز عبور اشتباه است</b>
          </div>";
}
}
?> 
                            <a href="reset-pass.html" class="forgot-link mb-15">رمز عبور را فراموش کرده‌اید؟</a>
                            <button type="submit" name="login">ورود</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="signup" role="tabpanel">
                        <form action="" method="post"  class="tf-form">
                            <div class="group-input mb-12">
        <i class="icon icon-profile"></i>
        <input type="text" name="username" id="username" placeholder="نام کاربری خود را وارد کنید">
    </div>
                            <div class="group-input custom-select mb-15" dir="ltr">
                                 <i class="icon icon-phone"></i>
                                <input style="direction: rtl;" name="mobile" id="mobile" type="tel" maxlength="11" placeholder="شماره تلفن خود را وارد کنید">
                            </div>
                            <div class="group-input mb-15 group-ip-password pd1">
                                <i class="icon icon-lock"></i>
                                <input class="password-field" type="password" name="password"  placeholder="رمز عبور خود را وارد کنید">
                                <div class="box-auth-show">
                                    <span class="show-pass">
                                        <i class="icon-eye-hide"></i>
                                        <i class="icon-eye-show"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="group-input mb-20 group-ip-password pd1">
                                <i class="icon icon-lock"></i>
                                <input class="password-field2" type="password" name="repassword" placeholder="تکرار رمز عبور">
                                <div class="box-auth-show">
                                    <span class="show-pass2">
                                        <i class="icon-eye-hide"></i>
                                        <i class="icon-eye-show"></i>
                                    </span>
                                </div>
                            </div><br><br><br><br>
                            <button type="submit" name="signup">ادامه</button>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
</body>
</html>