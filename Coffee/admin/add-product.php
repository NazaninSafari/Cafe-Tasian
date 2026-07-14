<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());
require '_nav.php';

$error = '';
$ok    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim(mysqli_real_escape_string($link, $_POST['pro_name']  ?? ''));
    $price = intval($_POST['pro_price'] ?? 0);
    $qty   = intval($_POST['pro_qty']   ?? 0);
    $img   = '';

    if (!$name)   $error = 'نام محصول الزامی است.';
    elseif ($price <= 0) $error = 'قیمت باید بیشتر از صفر باشد.';
    elseif ($qty  < 0)  $error = 'موجودی نمی‌تواند منفی باشد.';
    else {
        // آپلود تصویر
        if (!empty($_FILES['pro_image']['name'])) {
            $allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['pro_image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'فرمت تصویر مجاز نیست (jpg, jpeg, png, webp).';
            } else {
                $img = 'product_' . time() . '.' . $ext;
                $dest = '../images/products/' . $img;
                if (!move_uploaded_file($_FILES['pro_image']['tmp_name'], $dest)) {
                    $error = 'آپلود تصویر ناموفق بود. مطمئن شوید پوشه images/products وجود دارد.';
                    $img = '';
                }
            }
        }

        if (!$error) {
            $sql = "INSERT INTO product (pro_name, pro_price, pro_qty, pro_image)
                    VALUES ('$name', '$price', '$qty', '$img')";
            if (mysqli_query($link, $sql)) {
                header("Location: products.php?saved=1");
                exit();
            } else {
                $error = 'خطا در ذخیره: ' . mysqli_error($link);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>افزودن محصول – ادمین</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{--bg:#0d0f12;--surface:#151820;--surface2:#1c2030;--border:rgba(255,255,255,.07);--accent:#c8a96e;--accent2:#7eb89a;--danger:#e05c5c;--text:#e8e8e8;--muted:#6b7280;--sidebar-w:240px;}
body{font-family:'Vazirmatn',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;direction:rtl;}
.sidebar{width:var(--sidebar-w);background:var(--surface);border-left:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;right:0;bottom:0;z-index:100;}
.sidebar-logo{padding:28px 24px 20px;border-bottom:1px solid var(--border);}
.brand{font-size:1.25rem;font-weight:800;color:var(--accent);}
.sub{font-size:.72rem;color:var(--muted);margin-top:2px;}
.sidebar nav{flex:1;padding:20px 12px;display:flex;flex-direction:column;gap:4px;}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;color:var(--muted);text-decoration:none;font-size:.875rem;font-weight:500;transition:all .2s;}
.nav-item:hover{background:var(--surface2);color:var(--text);}
.nav-item.active{background:linear-gradient(135deg,rgba(200,169,110,.15),rgba(200,169,110,.05));color:var(--accent);border:1px solid rgba(200,169,110,.2);}
.sidebar-footer{padding:16px 12px;border-top:1px solid var(--border);}
.main{margin-right:var(--sidebar-w);flex:1;padding:36px 40px;overflow-y:auto;}
.topbar{display:flex;align-items:center;gap:16px;margin-bottom:28px;}
.page-title{font-size:1.5rem;font-weight:800;background:linear-gradient(90deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.form-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:32px;max-width:600px;}
.field{margin-bottom:20px;}
.field label{display:block;font-size:.82rem;font-weight:600;color:var(--muted);margin-bottom:8px;}
.field input,.field textarea{width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);font-family:inherit;font-size:.9rem;outline:none;transition:border-color .2s;}
.field input:focus,.field textarea:focus{border-color:rgba(200,169,110,.5);}
.field input[type="file"]{padding:9px 14px;cursor:pointer;}
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 24px;border-radius:10px;font-size:.88rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-primary{background:var(--accent);color:#1a1000;}
.btn-primary:hover{background:#dbbe84;}
.btn-back{background:var(--surface2);color:var(--text);border:1px solid var(--border);}
.btn-back:hover{border-color:var(--accent);color:var(--accent);}
.alert-error{background:rgba(224,92,92,.1);border:1px solid rgba(224,92,92,.3);color:var(--danger);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:.85rem;}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:768px){.sidebar{display:none;}.main{margin-right:0;padding:20px;}.row2{grid-template-columns:1fr;}}
</style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="brand">☕ کافه تاسیان</div>
    <div class="sub">پنل مدیریت</div>
  </div>
  <nav>
    <a href="index.php"    class="nav-item"><span>📊</span> داشبورد</a>
    <a href="orders.php"   class="nav-item"><span>🧾</span> سفارشات</a>
    <a href="products.php" class="nav-item active"><span>📦</span> محصولات</a>
    <a href="notifications.php" class="nav-item">
    <span>🔔</span> اطلاعیه‌ها
    </a>
    <a href="users.php"    class="nav-item"><span>👥</span> کاربران</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="nav-item" style="color:var(--danger);"><span>🚪</span> خروج</a>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <a href="products.php" class="btn btn-back">← بازگشت</a>
    <div class="page-title">افزودن محصول جدید</div>
  </div>

  <?php if ($error): ?>
  <div class="alert-error">⚠️ <?= $error ?></div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" enctype="multipart/form-data">
      <div class="field">
        <label>نام محصول *</label>
        <input type="text" name="pro_name" placeholder="مثال: لاته وانیلی" value="<?= htmlspecialchars($_POST['pro_name'] ?? '') ?>">
      </div>
      <div class="row2">
        <div class="field">
          <label>قیمت (تومان) *</label>
          <input type="number" name="pro_price" placeholder="مثال: 85000" min="0" value="<?= htmlspecialchars($_POST['pro_price'] ?? '') ?>">
        </div>
        <div class="field">
          <label>موجودی انبار *</label>
          <input type="number" name="pro_qty" placeholder="مثال: 20" min="0" value="<?= htmlspecialchars($_POST['pro_qty'] ?? '') ?>">
        </div>
      </div>
      <div class="field">
        <label>تصویر محصول</label>
        <input type="file" name="pro_image" accept="image/*">
      </div>
      <div style="display:flex;gap:12px;margin-top:8px;">
        <button type="submit" class="btn btn-primary">✅ ذخیره محصول</button>
        <a href="products.php" class="btn btn-back">انصراف</a>
      </div>
    </form>
  </div>
</main>

</body>
</html>