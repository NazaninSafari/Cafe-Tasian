<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) {
    die("خطای اتصال به پایگاه داده: " . mysqli_connect_error());
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>location.href='cart.php';</script>";
    exit;
}

if (isset($_SESSION["state_login"]) && $_SESSION["state_login"] === true) {
    $subtotal = 0;
    $delivery_fee = 50000;
    $tax = 3000;
    $trackcode = time() . rand(1000,9999);
    foreach ($_SESSION['cart'] as $pro_code => $product) {
        $pro_query = "SELECT * FROM product WHERE pro_code='$pro_code'";
        $pro_result = mysqli_query($link, $pro_query);
        if ($pro_result && $pro_row = mysqli_fetch_assoc($pro_result)) {
            $pro_price = $pro_row['pro_price'];
            $quantity = intval($product['quantity']);
            $subtotal += $pro_price * $quantity;
        }
    }
    $total = $subtotal + $delivery_fee + $tax;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // بررسی روش پرداخت
        $payment_method = $_POST['payment_method'] ?? 'cash';
        if ($payment_method === 'online' || $payment_method === 'subscription') {
            $error_message = "این روش پرداخت به زودی فعال می‌شود. لطفاً پرداخت در محل را انتخاب کنید.";
        }

        if (!isset($error_message)) {
            if (isset($_POST['address']) && !empty(trim($_POST['address']))) {
                $address = mysqli_real_escape_string($link, $_POST['address']);
            } else {
                $error_message = "لطفاً آدرس خود را وارد کنید.";
            }
        }

        if (!isset($error_message)) {
            if (isset($_POST['mobile']) && !empty(trim($_POST['mobile']))) {
                $mobile = mysqli_real_escape_string($link, $_POST['mobile']);
            } else {
                $error_message = "لطفاً شماره موبایل خود را وارد کنید.";
            }
        }

        if (!isset($error_message)) {
            // دریافت اطلاعات کاربر
            $user_query = "SELECT * FROM user WHERE username='{$_SESSION['username']}'";
            $user_result = mysqli_query($link, $user_query);
            $user_row = mysqli_fetch_assoc($user_result);
// بررسی همه محصولات
            foreach ($_SESSION['cart'] as $pro_code => $product) {

    $check_query = "SELECT pro_name, pro_qty FROM product WHERE pro_code='$pro_code'";
    $check_result = mysqli_query($link, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if (!$check_row) {
        die("محصول یافت نشد");
    }

    if ($product['quantity'] > $check_row['pro_qty']) {

        echo "<div class='alert alert-danger text-center'>
                موجودی محصول {$check_row['pro_name']} کافی نیست
              </div>";

        exit;
    }
}      
            // ثبت سفارش برای هر محصول در سبد
            foreach ($_SESSION['cart'] as $pro_code => $product) {
                $pro_query = "SELECT * FROM product WHERE pro_code='$pro_code'";
                $pro_result = mysqli_query($link, $pro_query);
                $pro_row = mysqli_fetch_assoc($pro_result);
                $pro_price = $pro_row['pro_price'];
                $pro_qty = intval($product['quantity']);
                $stock_qty = intval($pro_row['pro_qty']);

                if ($pro_qty > $stock_qty) {

                   echo "<div class='alert alert-danger text-center'>
                    موجودی محصول {$pro_row['pro_name']} کافی نیست
                    </div>";

                 exit;
                }

                $query = "INSERT INTO `order` (
                    username,
                    orderdate,
                    pro_code,
                    pro_qty,
                    pro_price,
                    mobile,
                    address,
                    trackcode,
                    state
                ) VALUES (
                    '{$user_row['username']}',
                    '".date('Y-m-d')."',
                    '$pro_code',
                    '$pro_qty',
                    '$pro_price',
                    '$mobile',
                    '$address',
                    '$trackcode',
                    '0'
                )";

                mysqli_query($link, $query);
                // بروزرسانی موجودی
                $update_query = "UPDATE product SET pro_qty=pro_qty-$pro_qty WHERE pro_code='$pro_code'";
                mysqli_query($link, $update_query);
            }
            // پاک کردن سبد خرید بعد از ثبت موفق سفارش
            unset($_SESSION['cart']);
            // ذخیره trackcode در session برای جلوگیری از ثبت تکراری
            $_SESSION['last_order'] = $trackcode;
            // هدایت به صفحه موفقیت
            header("Location: scan-success.php");
            exit;
        }
    }
} else {
    echo "<div class='alert alert-danger text-center'>لطفاً وارد حساب کاربری خود شوید</div>
    <meta http-equiv='refresh' content='2; url=profile.php'>";
    exit;
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
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="fonts/font-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="images/logo/48.png" />
    <link rel="apple-touch-icon-precomposed" href="images/logo/48.png" />

    <title>پرداخت</title>
</head>

<body class="appPayment">

<div class="header">
    <div class="title-header-bar fixed-top bg-white">
        <a href="cart.php" class="back-btn"><i class="icon-right"></i></a>
        <h1>پرداخت</h1>
    </div>
</div>

<div class="app pt-80">
<div class="tf-container">

<form action="" method="post" class="tf-form">

<?php if (isset($error_message)) { ?>
<div style="color:red; margin-bottom:15px;"><?php echo $error_message; ?></div>
<?php } ?>

<h3>مشخصات تحویل</h3><br>

<?php
// دریافت آدرس‌های ذخیره‌شده کاربر
$saved_addresses = mysqli_query($link, "SELECT * FROM address WHERE user_id='{$_SESSION['id']}' ORDER BY id DESC");
$has_addresses   = mysqli_num_rows($saved_addresses) > 0;
?>

<?php if ($has_addresses): ?>
<p style="font-size:12px;color:#888;margin:0 0 10px;">آدرس ذخیره‌شده را انتخاب کنید یا آدرس جدید وارد کنید:</p>
<div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px;" id="saved-addr-list">
    <?php while ($addr = mysqli_fetch_assoc($saved_addresses)): ?>
    <label onclick="selectAddress('<?= addslashes($addr['address']) ?>','<?= $addr['mobile'] ?>')"
           style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:#fff;border:0.5px solid #e5e5e5;border-radius:12px;cursor:pointer;transition:all .15s;">
        <div style="width:36px;height:36px;border-radius:9px;background:#e8f2f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="ti ti-map-pin" style="font-size:18px;color:#033f38;"></i>
        </div>
        <div style="flex:1;">
            <p style="margin:0;font-size:13px;font-weight:600;color:#1a1a1a;"><?= htmlspecialchars($addr['title']) ?></p>
            <p style="margin:3px 0 0;font-size:11px;color:#888;"><?= htmlspecialchars($addr['address']) ?></p>
        </div>
        <i class="ti ti-chevron-left" style="font-size:16px;color:#ccc;"></i>
    </label>
    <?php endwhile; ?>
</div>
<p style="font-size:12px;color:#888;margin:0 0 10px;">یا آدرس جدید وارد کنید:</p>
<?php endif; ?>

<div class="group-input mb-15">
    <input type="text" name="address" id="address-input"
           placeholder="آدرس خود را وارد کنید" required>
</div>
<div class="group-input mb-15">
    <input type="text" name="mobile" id="mobile-input"
           placeholder="موبایل" value="<?= $_SESSION['mobile']; ?>" required>
</div>

<h3>روش پرداخت</h3>
<p style="font-size:13px;color:#888;margin:6px 0 14px;">روش پرداخت را انتخاب کنید</p>

<div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">

  <label id="opt-cash" onclick="selectMethod('cash')" style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:#fff;border:2px solid #033f38;border-radius:12px;cursor:pointer;transition:all .15s;">
    <input type="radio" name="payment_method" value="cash" checked style="display:none;">
    <div style="width:40px;height:40px;border-radius:10px;background:#e8f2f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <i class="ti ti-cash" style="font-size:20px;color:#033f38;" aria-hidden="true"></i>
    </div>
    <div style="flex:1;">
      <p style="margin:0;font-size:14px;font-weight:500;color:#1a1a1a;">پرداخت در محل</p>
      <p style="margin:4px 0 0;font-size:12px;color:#888;">هنگام تحویل سفارش پرداخت کنید</p>
    </div>
    <div id="check-cash" style="width:20px;height:20px;border-radius:50%;background:#033f38;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <i class="ti ti-check" style="font-size:12px;color:#fff;" aria-hidden="true"></i>
    </div>
  </label>

  <label id="opt-online" onclick="selectMethod('online')" style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:#fff;border:0.5px solid #ddd;border-radius:12px;cursor:pointer;transition:all .15s;">
    <input type="radio" name="payment_method" value="online" disabled style="display:none;">
    <div style="width:40px;height:40px;border-radius:10px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <i class="ti ti-credit-card" style="font-size:20px;color:#aaa;" aria-hidden="true"></i>
    </div>
    <div style="flex:1;">
      <div style="display:flex;align-items:center;gap:8px;">
        <p style="margin:0;font-size:14px;font-weight:500;color:#1a1a1a;">پرداخت آنلاین</p>
        <span style="font-size:10px;padding:2px 8px;border-radius:20px;background:#e8f2f0;color:#033f38;font-weight:500;">به زودی</span>
      </div>
      <p style="margin:4px 0 0;font-size:12px;color:#888;">پرداخت از طریق درگاه بانکی</p>
    </div>
    <div id="check-online" style="width:20px;height:20px;border-radius:50%;border:0.5px solid #ddd;flex-shrink:0;"></div>
  </label>

  <label id="opt-subscription" onclick="selectMethod('subscription')" style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:#fff;border:0.5px solid #ddd;border-radius:12px;cursor:pointer;transition:all .15s;">
    <input type="radio" name="payment_method" value="subscription" disabled style="display:none;">
    <div style="width:40px;height:40px;border-radius:10px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <i class="ti ti-star" style="font-size:20px;color:#aaa;" aria-hidden="true"></i>
    </div>
    <div style="flex:1;">
      <div style="display:flex;align-items:center;gap:8px;">
        <p style="margin:0;font-size:14px;font-weight:500;color:#1a1a1a;">اشتراک ویژه</p>
        <span style="font-size:10px;padding:2px 8px;border-radius:20px;background:#e8f2f0;color:#033f38;font-weight:500;">به زودی</span>
      </div>
      <p style="margin:4px 0 0;font-size:12px;color:#888;">تخفیف و ارسال رایگان برای اعضا</p>
    </div>
    <div id="check-subscription" style="width:20px;height:20px;border-radius:50%;border:0.5px solid #ddd;flex-shrink:0;"></div>
  </label>

</div>

<script>
function selectMethod(method) {
  const methods = ['cash','online','subscription'];
  methods.forEach(m => {
    const opt = document.getElementById('opt-' + m);
    const chk = document.getElementById('check-' + m);
    if (m === method) {
      opt.style.border = '2px solid #033f38';
      chk.style.background = '#033f38';
      chk.style.border = 'none';
      chk.innerHTML = '<i class="ti ti-check" style="font-size:12px;color:#fff;"></i>';
    } else {
      opt.style.border = '0.5px solid #ddd';
      chk.style.background = 'transparent';
      chk.style.border = '0.5px solid #ddd';
      chk.innerHTML = '';
    }
  });
}
</script>

<div class="box-total-order st1 pt-70 mt-30">
    <ul>
        <li class="list-order">مجموع آیتم‌ها: <span><?php echo number_format($subtotal); ?> تومان</span></li>
        <li class="list-order">هزینه تحویل: <span><?php echo number_format($delivery_fee); ?> تومان</span></li>
        <li class="list-order line">مالیات: <span><?php echo number_format($tax); ?> تومان</span></li>
    </ul>
    <p class="mt-15 list-order-total mb-30">مجموع: <span><?php echo number_format($total); ?> تومان</span></p>
    <button type="submit" class="tf-btn large primary mb-20">ثبت نهایی سفارش</button>
</div>

</form>


    
    
    
    <div class="modal fade" id="addVoucher">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content center">
                <div class="modal-header">
                    <h2>افزودن کد تخفیف</h2>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-30">
                    <input type="text" placeholder="کد تخفیف">
                    <a class="tf-btn primary large mt-30" href="#" data-bs-dismiss="modal">اعمال</a>
                </div>
            </div>
        </div>
    </div>
    


    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/swiper-bundle.min.js"></script>
    <script src="js/carousel.js"></script>
    <script src="js/sidebar.js"></script>
    <script src="js/main.js"></script>
    <script>
    function selectAddress(address, mobile) {
        document.getElementById('address-input').value = address;
        document.getElementById('mobile-input').value  = mobile;
        // هایلایت کارت انتخاب‌شده
        document.querySelectorAll('#saved-addr-list label').forEach(el => {
            el.style.border = '0.5px solid #e5e5e5';
        });
        event.currentTarget.style.border = '1.5px solid #033f38';
    }
    </script>

   
</body>

</html>