<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());
require '_nav.php';

// حذف محصول
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    mysqli_query($link, "DELETE FROM product WHERE pro_code=$del_id");
    header("Location: products.php?deleted=1");
    exit();
}

$products = mysqli_query($link, "SELECT * FROM product ORDER BY pro_code DESC");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مدیریت محصولات – ادمین</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{--bg:#0d0f12;--surface:#151820;--surface2:#1c2030;--border:rgba(255,255,255,.07);--accent:#c8a96e;--accent2:#7eb89a;--danger:#e05c5c;--warn:#e0a050;--ok:#5cb87e;--text:#e8e8e8;--muted:#6b7280;--sidebar-w:240px;}
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
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;}
.page-title{font-size:1.6rem;font-weight:800;background:linear-gradient(90deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:10px;font-size:.85rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-primary{background:var(--accent);color:#1a1000;}
.btn-primary:hover{background:#dbbe84;transform:translateY(-1px);}
.btn-danger{background:rgba(224,92,92,.12);color:var(--danger);border:1px solid rgba(224,92,92,.25);}
.btn-danger:hover{background:rgba(224,92,92,.22);}
.btn-edit{background:rgba(200,169,110,.1);color:var(--accent);border:1px solid rgba(200,169,110,.2);}
.btn-edit:hover{background:rgba(200,169,110,.2);}
.btn-sm{padding:5px 13px;font-size:.75rem;border-radius:7px;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead th{padding:13px 18px;font-size:.75rem;font-weight:600;color:var(--muted);text-align:right;background:var(--surface2);border-bottom:1px solid var(--border);}
tbody td{padding:12px 18px;font-size:.85rem;border-bottom:1px solid var(--border);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:rgba(255,255,255,.025);}
.product-img{width:52px;height:52px;object-fit:cover;border-radius:10px;border:1px solid var(--border);}
.stock-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;}
.stock-ok{background:rgba(92,184,126,.12);color:var(--ok);}
.stock-low{background:rgba(224,160,80,.12);color:var(--warn);}
.stock-out{background:rgba(224,92,92,.12);color:var(--danger);}
.alert-success{background:rgba(92,184,126,.1);border:1px solid rgba(92,184,126,.3);color:var(--ok);padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:.85rem;}
.alert-danger{background:rgba(224,92,92,.1);border:1px solid rgba(224,92,92,.3);color:var(--danger);padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:.85rem;}
.actions{display:flex;gap:8px;align-items:center;}
@media(max-width:768px){.sidebar{display:none;}.main{margin-right:0;padding:20px;}}
</style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="brand">☕ کافه تاسیان</div>
    <div class="sub">پنل مدیریت</div>
  </div>
  <nav>
    <a href="index.php"    class="nav-item <?= $current_page=='index.php'?'active':'' ?>"><span>📊</span> داشبورد</a>
    <a href="orders.php"   class="nav-item <?= $current_page=='orders.php'?'active':'' ?>"><span>🧾</span> سفارشات</a>
    <a href="products.php" class="nav-item <?= $current_page=='products.php'?'active':'' ?>"><span>📦</span> محصولات</a>
    <a href="notifications.php" class="nav-item">
    <span>🔔</span> اطلاعیه‌ها
    </a>
    <a href="users.php"    class="nav-item <?= $current_page=='users.php'?'active':'' ?>"><span>👥</span> کاربران</a>
    <a href="notifications.php" class="nav-item <?= $current_page=='notifications.php'?'active':'' ?>"><span>🔔</span> اطلاعیه‌ها</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="nav-item" style="color:var(--danger);"><span>🚪</span> خروج</a>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div class="page-title">مدیریت محصولات</div>
    <a href="add-product.php" class="btn btn-primary">+ افزودن محصول</a>
  </div>

  <?php if (isset($_GET['deleted'])): ?>
  <div class="alert-danger">🗑 محصول با موفقیت حذف شد.</div>
  <?php endif; ?>
  <?php if (isset($_GET['saved'])): ?>
  <div class="alert-success">✅ محصول با موفقیت ذخیره شد.</div>
  <?php endif; ?>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>تصویر</th>
          <th>نام محصول</th>
          <th>قیمت (تومان)</th>
          <th>موجودی</th>
          <th>کد محصول</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $count = 0;
        while ($row = mysqli_fetch_assoc($products)):
          $count++;
          $qty = intval($row['pro_qty']);
          if ($qty === 0)       { $sc = 'stock-out'; $sl = 'ناموجود'; }
          elseif ($qty <= 5)    { $sc = 'stock-low'; $sl = "کم ($qty)"; }
          else                  { $sc = 'stock-ok';  $sl = $qty; }
        ?>
        <tr>
          <td>
            <?php if (!empty($row['pro_image'])): ?>
            <img src="../images/products/<?= htmlspecialchars($row['pro_image']) ?>" class="product-img" alt="">
            <?php else: ?>
            <div style="width:52px;height:52px;border-radius:10px;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:1.2rem;">☕</div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($row['pro_name']) ?></strong></td>
          <td style="color:var(--accent);font-weight:700"><?= number_format($row['pro_price']) ?></td>
          <td><span class="stock-badge <?= $sc ?>"><?= $sl ?></span></td>
          <td style="color:var(--muted);font-size:.8rem">#<?= $row['pro_code'] ?></td>
          <td>
            <div class="actions">
              <a href="edit-product.php?id=<?= $row['pro_code'] ?>" class="btn btn-edit btn-sm">✏️ ویرایش</a>
              <a href="products.php?delete=<?= $row['pro_code'] ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('آیا از حذف این محصول مطمئن هستید؟')">🗑 حذف</a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <tr><td colspan="6" style="text-align:center;padding:60px;color:var(--muted);">محصولی یافت نشد</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>