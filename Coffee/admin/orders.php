<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());
require '_nav.php';

// فیلتر وضعیت
$filter = isset($_GET['state']) && is_numeric($_GET['state']) ? intval($_GET['state']) : -1;
$where  = $filter >= 0 ? "WHERE state=$filter" : "";

$orders = mysqli_query($link,
    "SELECT trackcode, username, orderdate, SUM(pro_price*pro_qty) as total, state, address, mobile
     FROM `order`
     $where
     GROUP BY trackcode
     ORDER BY MAX(id) DESC");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مدیریت سفارشات – ادمین</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
<?php include_once '_style.php'; ?>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg:#0d0f12;--surface:#151820;--surface2:#1c2030;--border:rgba(255,255,255,.07);
  --accent:#c8a96e;--accent2:#7eb89a;--danger:#e05c5c;--warn:#e0a050;--ok:#5cb87e;
  --text:#e8e8e8;--muted:#6b7280;--sidebar-w:240px;
}
body { font-family:'Vazirmatn',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;direction:rtl; }
.sidebar{width:var(--sidebar-w);background:var(--surface);border-left:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;right:0;bottom:0;z-index:100;}
.sidebar-logo{padding:28px 24px 20px;border-bottom:1px solid var(--border);}
.sidebar-logo .brand{font-size:1.25rem;font-weight:800;color:var(--accent);}
.sidebar-logo .sub{font-size:.72rem;color:var(--muted);margin-top:2px;}
.sidebar nav{flex:1;padding:20px 12px;display:flex;flex-direction:column;gap:4px;}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;color:var(--muted);text-decoration:none;font-size:.875rem;font-weight:500;transition:all .2s;}
.nav-item:hover{background:var(--surface2);color:var(--text);}
.nav-item.active{background:linear-gradient(135deg,rgba(200,169,110,.15),rgba(200,169,110,.05));color:var(--accent);border:1px solid rgba(200,169,110,.2);}
.sidebar-footer{padding:16px 12px;border-top:1px solid var(--border);}
.main{margin-right:var(--sidebar-w);flex:1;padding:36px 40px;}
.page-title{font-size:1.6rem;font-weight:800;background:linear-gradient(90deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:8px;}
.page-sub{color:var(--muted);font-size:.85rem;margin-bottom:28px;}
.filters{display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap;}
.filter-btn{padding:7px 18px;border-radius:20px;font-size:.8rem;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--muted);background:var(--surface);transition:all .2s;font-family:inherit;cursor:pointer;}
.filter-btn:hover,.filter-btn.active{border-color:var(--accent);color:var(--accent);background:rgba(200,169,110,.08);}
.card{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.card-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.card-header h2{font-size:1rem;font-weight:700;}
table{width:100%;border-collapse:collapse;}
thead th{padding:13px 18px;font-size:.75rem;font-weight:600;color:var(--muted);text-align:right;background:var(--surface2);border-bottom:1px solid var(--border);}
tbody td{padding:14px 18px;font-size:.85rem;border-bottom:1px solid var(--border);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:rgba(255,255,255,.02);}
.badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:600;white-space:nowrap;}
.badge-0{background:rgba(224,160,80,.12);color:var(--warn);}
.badge-1{background:rgba(100,160,255,.12);color:#64a0ff;}
.badge-2{background:rgba(126,184,154,.12);color:var(--accent2);}
.badge-3{background:rgba(92,184,126,.15);color:var(--ok);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:.8rem;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-sm{padding:5px 12px;font-size:.75rem;}
.btn-ghost{background:var(--surface2);color:var(--text);border:1px solid var(--border);}
.btn-ghost:hover{border-color:var(--accent);color:var(--accent);}
.empty{text-align:center;padding:60px;color:var(--muted);}
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
  <div class="page-title">مدیریت سفارشات</div>
  <div class="page-sub">مشاهده و تغییر وضعیت تمام سفارش‌ها</div>

  <div class="filters">
    <a href="orders.php"         class="filter-btn <?= $filter==-1?'active':'' ?>">همه</a>
    <a href="orders.php?state=0" class="filter-btn <?= $filter==0?'active':'' ?>">ثبت شده</a>
    <a href="orders.php?state=1" class="filter-btn <?= $filter==1?'active':'' ?>">در حال آماده‌سازی</a>
    <a href="orders.php?state=2" class="filter-btn <?= $filter==2?'active':'' ?>">آماده تحویل</a>
    <a href="orders.php?state=3" class="filter-btn <?= $filter==3?'active':'' ?>">تحویل داده شده</a>
  </div>

  <div class="card">
    <div class="card-header">
      <h2>لیست سفارشات</h2>
    </div>
    <table>
      <thead>
        <tr>
          <th>کد سفارش</th>
          <th>کاربر</th>
          <th>موبایل</th>
          <th>تاریخ</th>
          <th>مبلغ (تومان)</th>
          <th>وضعیت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $states = [
          '0'=>['ثبت شده','badge-0'],
          '1'=>['در حال آماده‌سازی','badge-1'],
          '2'=>['آماده تحویل','badge-2'],
          '3'=>['تحویل داده شده','badge-3']
        ];
        $count = 0;
        while ($row = mysqli_fetch_assoc($orders)):
          $count++;
          [$label,$cls] = $states[$row['state']] ?? ['نامشخص','badge-0'];
        ?>
        <tr>
          <td><strong style="color:var(--accent)">#<?= $row['trackcode'] ?></strong></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td style="direction:ltr;text-align:right"><?= htmlspecialchars($row['mobile']) ?></td>
          <td style="color:var(--muted)"><?= $row['orderdate'] ?></td>
          <td><?= number_format($row['total']) ?></td>
          <td><span class="badge <?= $cls ?>"><?= $label ?></span></td>
          <td>
            <a href="order-detail.php?track=<?= urlencode($row['trackcode']) ?>" class="btn btn-ghost btn-sm">جزئیات</a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <tr><td colspan="7" class="empty">سفارشی یافت نشد</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>