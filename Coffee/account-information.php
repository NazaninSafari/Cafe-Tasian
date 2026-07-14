<?php
$error = '-';
$ok = false;
session_start();

if (isset($_SESSION["state_login"]) && $_SESSION["state_login"] === true) {

    $link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
    if (mysqli_connect_errno()) {
        die("خطای اتصال به پایگاه داده: " . mysqli_connect_error());
    }

    // گرفتن شناسه کاربر از سشن
    $user_id = $_SESSION['id']; 
    // گرفتن اطلاعات فعلی کاربر
    $result = $link->query("SELECT username, email, mobile FROM user WHERE id = $user_id");
    if ($result) {
        $current = $result->fetch_assoc();
    } else {
        die("خطا در دریافت اطلاعات کاربر: " . $link->error);
    }

    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['mobile'])) {
        $new_username = trim($_POST['username']);
        $new_email = trim($_POST['email']);
        $new_mobile = trim($_POST['mobile']);

        $update_fields = [];

        // بروزرسانی نام کاربری
        if (!empty($new_username) && ($new_username !== $current['username'])) {
            if (strlen($new_username) < 3 || strlen($new_username) > 40) {
                $error = 'نام کاربری را درست وارد کنید';
            } else {
                $update_fields[] = "username = '" . $link->real_escape_string($new_username) . "'";
            }
        }

        // بروزرسانی ایمیل
        if (!empty($new_email) && $new_email !== $current['email']) {
            if (filter_var($new_email, FILTER_VALIDATE_EMAIL) === false) {
                $error = "ایمیل وارد شده معتبر نیست :)";
            } else {
                $update_fields[] = "email = '" . $link->real_escape_string($new_email) . "'";
            }
        }

        // بروزرسانی موبایل
        if (!empty($new_mobile) && $new_mobile !== $current['mobile']) {
            if (strlen($new_mobile) !== 11 || substr($new_mobile, 0, 2) !== '09') {
                $error = 'شماره موبایل را درست وارد کنید (مثال: 09121234567)';
            } else {
                $update_fields[] = "mobile = '" . $link->real_escape_string($new_mobile) . "'";
            }
        }

        // اگر خطایی نبود و فیلدهای تغییر یافته وجود داشت
        if (($error == '-') && !empty($update_fields)) {
            $sql = "UPDATE user SET " . implode(', ', $update_fields) . " WHERE id = $user_id";
            if ($link->query($sql)) {
                $ok = true;
               
            } else {
                $error = 'خطا در بروزرسانی اطلاعات: ' . $link->error;
            }
        } elseif (($error === '-') && empty($update_fields)) {
            $error = 'هیچ تغییری اعمال نشد';
        }
    }
    $link->close();
} else {
    $error = 'لطفاً وارد حساب کاربری خود شوید';
    ?>
    <meta http-equiv="refresh" content="2; url=profile.php">
    <?php
}
?>
       

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/boostrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <link rel="stylesheet"type="text/css" href="css/styles.css"/>
    <!-- Icons -->
    <link rel="stylesheet" href="fonts/font-icons.css">
    
    <link rel="shortcut icon" href="images/logo/48.png" />
    <link rel="apple-touch-icon-precomposed" href="images/logo/48.png" />
    
    <title>اطلاعات حساب</title>
</head>

<body>

     <div class="preload preload-container">
        <div class="preload-logo">
          <div class="spinner"></div>
        </div>
      </div>


    <div class="header">
        <div class="tf-container">
            <div class="title-header-bar pt-20">
                <a href="#" class="back-btn"><i class="icon-right"></i></a>
                <h1>اطلاعات حساب</h1>
            </div> 
        </div>
    </div>
    <div class="mt-20">
        <div class="tf-container">
            <a class="box-profile col scan" href="#" >
                <div class="img"style="width: 100px;height: 100px; border-radius: 100%;object-fit: cover;">
                    <img src="images/avt/user.jpg" alt="تصویر" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 18 16" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.5337 3.197C13.567 3.25527 13.6253 3.29689 13.7003 3.29689C15.7003 3.29689 17.3337 4.92841 17.3337 6.92619V11.8707C17.3337 13.8685 15.7003 15.5 13.7003 15.5H4.30033C2.29199 15.5 0.666992 13.8685 0.666992 11.8707V6.92619C0.666992 4.92841 2.29199 3.29689 4.30033 3.29689C4.36699 3.29689 4.43366 3.2636 4.45866 3.197L4.50866 3.09711C4.53739 3.03665 4.56687 2.97455 4.5968 2.9115C4.80999 2.46246 5.04585 1.96567 5.19199 1.6737C5.57533 0.924528 6.22533 0.508324 7.03366 0.5H10.9587C11.767 0.508324 12.4253 0.924528 12.8087 1.6737C12.9399 1.93592 13.1399 2.35833 13.3326 2.76545C13.3724 2.84942 13.4119 2.93274 13.4503 3.01387L13.5337 3.197ZM12.9253 6.39345C12.9253 6.80966 13.2587 7.14262 13.6753 7.14262C14.092 7.14262 14.4337 6.80966 14.4337 6.39345C14.4337 5.97725 14.092 5.63596 13.6753 5.63596C13.2587 5.63596 12.9253 5.97725 12.9253 6.39345ZM7.55866 7.68368C7.95033 7.29245 8.45866 7.08435 9.00033 7.08435C9.54199 7.08435 10.0503 7.29245 10.4337 7.67536C10.817 8.05827 11.0253 8.56604 11.0253 9.1071C11.017 10.2225 10.117 11.1299 9.00033 11.1299C8.45866 11.1299 7.95033 10.9218 7.56699 10.5388C7.18366 10.1559 6.97533 9.64817 6.97533 9.1071V9.09878C6.96699 8.57436 7.17533 8.06659 7.55866 7.68368ZM11.3087 11.4212C10.717 12.0122 9.90033 12.3785 9.00033 12.3785C8.12533 12.3785 7.30866 12.0372 6.68366 11.4212C6.06699 10.7969 5.72533 9.98113 5.72533 9.1071C5.71699 8.2414 6.05866 7.42564 6.67533 6.80133C7.30033 6.17703 8.12533 5.83574 9.00033 5.83574C9.87533 5.83574 10.7003 6.17703 11.317 6.79301C11.9337 7.41731 12.2753 8.2414 12.2753 9.1071C12.267 10.0144 11.9003 10.8302 11.3087 11.4212Z" fill="white"/>
                    </svg>
                </div>  
            </a> 
            <br><h3 style="text-align: center;">پروفایل‌</h3>
            <br><br><br><br>
<form action="" method="post" >
    <div class="group-input mb-12">
        <i class="icon icon-profile"></i>
        <input type="text" name="username" id="username">
    </div>
    <div class="group-input mb-12">
        <i class="icon icon-message"></i>
        <input type="text" name="email" id="email">
    </div>
    <div class="group-input mb-12">
        <i class="icon icon-phone"></i>
        <input type="text" name="mobile" id="mobile" maxlength="11">
    </div><?php
 
    if ($error !== '-') {?>
<div class="alert alert-danger text-center">
      <?php echo $error; ?>
        </div>
       <?php   } ?>
       <!-- نمایش پیام موفق -->
       <?php if($ok){?>
       <div class="alert alert-success text-center">
       ویرایش با موفقیت انجام شد:)
        </div>
         <meta http-equiv="refresh" content="2; url=profile.php">
       <?php } ?>
        

                <div class="bottom-fixed btn-fixed pb">
                    <div class="inner">
                        <button type="submit">ذخیره</button> 
                    </div>
                </div>
            </form>     
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
    <script src="js/main.js"></script>

</body>

</html>