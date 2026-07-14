<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());
require '_nav.php';

$track = isset($_GET['track']) ? mysqli_real_escape_string($link, $_GET['track']) : '';
if (!$track) { header("Location: orders.php"); exit(); }

// تغییر وضعیت
if (isset($_POST['new_state'])) {
    $new_state = intval($_POST['new_state']);
    if ($new_state >= 0 && $new_state <= 3) {
        mysqli_query($link, "UPDATE `order` SET state='$new_state' WHERE trackcode='$track'");
        header("Location: order-detail.php?track=$track&updated=1");
        exit();
    }
}

// اطلاعات سفارش
$detail = mysqli_query($link,
    "SELECT o.*, p.pro_name, p.pro_image
     FROM `order` o
     LEFT JOIN product p ON o.pro_code = p.pro_code
     WHERE o.trackcode='$track'");

$items = [];
$total = 0;
$order_info = null;
while ($row = mysqli_fetch_assoc($detail)) {
    if (!$order_info) $order_info = $row;
    $items[] = $row;
    $total += $row['pro_price'] * $row['pro_qty'];
}

if (!$order_info) { header("Location: orders.php"); exit(); }

$states = [
  0 => ['ثبت شده',             'badge-0'],
  1 => ['در حال آماده‌سازی',   'badge-1'],
  2 => ['آماده تحویل',         'badge-2'],
  3 => ['تحویل داده شده',      'badge-3'],
];
$cur_state = intval($order_info['state']);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>جزئیات سفارش #<?= $track ?></title>
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
.main{margin-right:var(--sidebar-w);flex:1;padding:36px 40px;}
.page-title{font-size:1.4rem;font-weight:800;color:var(--accent);margin-bottom:6px;}
.page-sub{color:var(--muted);font-size:.85rem;margin-bottom:28px;}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.card-header{padding:18px 22px;border-bottom:1px solid var(--border);}
.card-header h2{font-size:.95rem;font-weight:700;}
.card-body{padding:20px 22px;}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--border);font-size:.85rem;}
.info-row:last-child{border-bottom:none;}
.info-row .lbl{color:var(--muted);}
.info-row .val{font-weight:600;}
.badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:600;}
.badge-0{background:rgba(224,160,80,.12);color:var(--warn);}
.badge-1{background:rgba(100,160,255,.12);color:#64a0ff;}
.badge-2{background:rgba(126,184,154,.12);color:var(--accent2);}
.badge-3{background:rgba(92,184,126,.15);color:var(--ok);}
table{width:100%;border-collapse:collapse;}
thead th{padding:12px 18px;font-size:.75rem;font-weight:600;color:var(--muted);text-align:right;background:var(--surface2);border-bottom:1px solid var(--border);}
tbody td{padding:13px 18px;font-size:.85rem;border-bottom:1px solid var(--border);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
.product-img{width:48px;height:48px;object-fit:cover;border-radius:10px;border:1px solid var(--border);}
.status-form{display:flex;gap:10px;flex-wrap:wrap;padding:20px 22px;}
.state-btn{padding:9px 20px;border-radius:10px;font-size:.82rem;font-weight:600;border:1px solid var(--border);color:var(--muted);background:var(--surface2);cursor:pointer;font-family:inherit;transition:all .2s;}
.state-btn:hover{border-color:var(--accent);color:var(--accent);}
.state-btn.current{border-color:var(--accent);color:var(--accent);background:rgba(200,169,110,.1);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-back{background:var(--surface2);color:var(--text);border:1px solid var(--border);}
.btn-back:hover{border-color:var(--accent);color:var(--accent);}
.success-msg{background:rgba(92,184,126,.1);border:1px solid rgba(92,184,126,.3);color:var(--ok);padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:.85rem;}
@media(max-width:768px){.grid2{grid-template-columns:1fr;}.sidebar{display:none;}.main{margin-right:0;padding:20px;}}
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
    <a href="orders.php"   class="nav-item active"><span>🧾</span> سفارشات</a>
    <a href="products.php" class="nav-item"><span>📦</span> محصولات</a>
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
  <div style="display:flex;align-items:center;gap:14px;margin-bottom:6px;">
    <a href="orders.php" class="btn btn-back">← بازگشت</a>
    <div class="page-title">سفارش #<?= $track ?></div>
  </div>
  <div class="page-sub">جزئیات و مدیریت وضعیت سفارش</div>

  <?php if (isset($_GET['updated'])): ?>
  <div class="success-msg">✅ وضعیت سفارش با موفقیت به‌روزرسانی شد.</div>
  <?php endif; ?>

  <div class="grid2">
    <!-- اطلاعات سفارش -->
    <div class="card">
      <div class="card-header"><h2>اطلاعات سفارش</h2></div>
      <div class="card-body">
        <div class="info-row"><span class="lbl">کد پیگیری</span><span class="val" style="color:var(--accent)">#<?= $order_info['trackcode'] ?></span></div>
        <div class="info-row"><span class="lbl">کاربر</span><span class="val"><?= htmlspecialchars($order_info['username']) ?></span></div>
        <div class="info-row"><span class="lbl">موبایل</span><span class="val" style="direction:ltr"><?= htmlspecialchars($order_info['mobile']) ?></span></div>
        <div class="info-row"><span class="lbl">تاریخ ثبت</span><span class="val"><?= $order_info['orderdate'] ?></span></div>
        <div class="info-row"><span class="lbl">مبلغ کل</span><span class="val"><?= number_format($total) ?> تومان</span></div>
        <div class="info-row"><span class="lbl">وضعیت</span>
          <span class="badge <?= 'badge-'.$cur_state ?>"><?= $states[$cur_state][0] ?></span>
        </div>
      </div>
    </div>

    <!-- آدرس -->
    <div class="card">
      <div class="card-header"><h2>آدرس تحویل</h2></div>
      <div class="card-body">
        <p style="line-height:1.8;font-size:.9rem;color:var(--text)"><?= htmlspecialchars($order_info['address']) ?></p>
      </div>
    </div>
  </div>

  <!-- تغییر وضعیت -->
  <div class="card" style="margin-bottom:24px;">
    <div class="card-header"><h2>تغییر وضعیت سفارش</h2></div>
    <form method="POST" action="">
      <div class="status-form">
        <?php foreach ($states as $val => [$label, $_]): ?>
        <button type="submit" name="new_state" value="<?= $val ?>"
          class="state-btn <?= $val === $cur_state ? 'current' : '' ?>">
          <?= $label ?>
        </button>
        <?php endforeach; ?>
      </div>
    </form>
  </div>

  <!-- آیتم‌های سفارش -->
  <div class="card">
    <div class="card-header"><h2>محصولات سفارش</h2></div>
    <table>
      <thead>
        <tr>
          <th>تصویر</th>
          <th>نام محصول</th>
          <th>سایز</th>
          <th>تعداد</th>
          <th>قیمت واحد</th>
          <th>جمع</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
          <td>
            <?php if ($item['pro_image']): ?>
            <img src="../images/products/<?= htmlspecialchars($item['pro_image']) ?>" class="product-img" alt="">
            <?php else: ?>
            <span style="color:var(--muted)">—</span>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($item['pro_name'] ?? '—') ?></strong></td>
          <td><?= htmlspecialchars($item['size'] ?? '—') ?></td>
          <td><?= intval($item['pro_qty']) ?></td>
          <td><?= number_format($item['pro_price']) ?></td>
          <td style="color:var(--accent);font-weight:700"><?= number_format($item['pro_price'] * $item['pro_qty']) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr style="background:var(--surface2)">
          <td colspan="5" style="text-align:left;font-weight:700;padding:14px 18px;">مجموع</td>
          <td style="color:var(--ok);font-weight:800;font-size:1rem;padding:14px 18px;"><?= number_format($total) ?> تومان</td>
        </tr>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>