<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());
require '_nav.php';

// آمار داشبورد
$user_count     = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM user"))[0];
$product_count  = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM product"))[0];
$order_count    = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(DISTINCT trackcode) FROM `order`"))[0];
$total_sales_r  = mysqli_fetch_row(mysqli_query($link, "SELECT SUM(pro_price * pro_qty) FROM `order`"));
$total_sales    = $total_sales_r[0] ?? 0;

// آخرین سفارش‌ها
$recent_orders  = mysqli_query($link,
    "SELECT trackcode, username, orderdate, SUM(pro_price*pro_qty) as total, state
     FROM `order`
     GROUP BY trackcode
     ORDER BY MAX(id) DESC
     LIMIT 5");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>داشبورد ادمین – کافه تاسیان</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:        #0d0f12;
  --surface:   #151820;
  --surface2:  #1c2030;
  --border:    rgba(255,255,255,.07);
  --accent:    #c8a96e;        /* طلایی قهوه */
  --accent2:   #7eb89a;        /* سبز نعنایی */
  --danger:    #e05c5c;
  --warn:      #e0a050;
  --ok:        #5cb87e;
  --text:      #e8e8e8;
  --muted:     #6b7280;
  --sidebar-w: 240px;
}

body { font-family: 'Vazirmatn', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; direction: rtl; }

/* ─── Sidebar ─────────────────────────────── */
.sidebar {
  width: var(--sidebar-w);
  background: var(--surface);
  border-left: 1px solid var(--border);
  display: flex; flex-direction: column;
  position: fixed; top: 0; right: 0; bottom: 0;
  z-index: 100;
}
.sidebar-logo {
  padding: 28px 24px 20px;
  border-bottom: 1px solid var(--border);
}
.sidebar-logo .brand { font-size: 1.25rem; font-weight: 800; color: var(--accent); letter-spacing: -.5px; }
.sidebar-logo .sub   { font-size: .72rem; color: var(--muted); margin-top: 2px; }

.sidebar nav { flex: 1; padding: 20px 12px; display: flex; flex-direction: column; gap: 4px; }
.nav-item {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 14px; border-radius: 10px;
  color: var(--muted); text-decoration: none;
  font-size: .875rem; font-weight: 500;
  transition: all .2s;
}
.nav-item:hover { background: var(--surface2); color: var(--text); }
.nav-item.active { background: linear-gradient(135deg, rgba(200,169,110,.15), rgba(200,169,110,.05)); color: var(--accent); border: 1px solid rgba(200,169,110,.2); }
.nav-item .icon { font-size: 1.1rem; width: 20px; text-align: center; }

.sidebar-footer {
  padding: 16px 12px;
  border-top: 1px solid var(--border);
}

/* ─── Main ─────────────────────────────────── */
.main {
  margin-right: var(--sidebar-w);
  flex: 1;
  padding: 36px 40px;
  overflow-y: auto;
}
.page-title {
  font-size: 1.6rem; font-weight: 800;
  background: linear-gradient(90deg, var(--accent), var(--accent2));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  margin-bottom: 8px;
}
.page-sub { color: var(--muted); font-size: .85rem; margin-bottom: 32px; }

/* ─── Stat Cards ───────────────────────────── */
.stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 36px; }
.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 24px 22px;
  position: relative; overflow: hidden;
  transition: transform .2s, border-color .2s;
}
.stat-card:hover { transform: translateY(-3px); border-color: rgba(200,169,110,.3); }
.stat-card::before {
  content: ''; position: absolute;
  top: -30px; left: -30px;
  width: 120px; height: 120px;
  border-radius: 50%;
  opacity: .06;
}
.stat-card.c1::before { background: var(--accent); }
.stat-card.c2::before { background: var(--accent2); }
.stat-card.c3::before { background: var(--warn); }
.stat-card.c4::before { background: var(--ok); }

.stat-icon { font-size: 1.5rem; margin-bottom: 14px; }
.stat-label { font-size: .78rem; color: var(--muted); margin-bottom: 6px; }
.stat-value { font-size: 1.9rem; font-weight: 800; line-height: 1; }
.stat-card.c1 .stat-value { color: var(--accent); }
.stat-card.c2 .stat-value { color: var(--accent2); }
.stat-card.c3 .stat-value { color: var(--warn); }
.stat-card.c4 .stat-value { color: var(--ok); }

/* ─── Table Card ───────────────────────────── */
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
}
.card-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.card-header h2 { font-size: 1rem; font-weight: 700; }
.card-header a {
  font-size: .8rem; color: var(--accent);
  text-decoration: none; font-weight: 500;
}
.card-header a:hover { text-decoration: underline; }

table { width: 100%; border-collapse: collapse; }
thead th {
  padding: 13px 18px;
  font-size: .75rem; font-weight: 600;
  color: var(--muted); text-align: right;
  background: var(--surface2);
  border-bottom: 1px solid var(--border);
}
tbody td {
  padding: 14px 18px;
  font-size: .85rem;
  border-bottom: 1px solid var(--border);
  vertical-align: middle;
}
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover { background: rgba(255,255,255,.02); }

.badge {
  display: inline-flex; align-items: center;
  padding: 4px 12px; border-radius: 20px;
  font-size: .72rem; font-weight: 600; white-space: nowrap;
}
.badge-0 { background: rgba(224,160,80,.12); color: var(--warn); }
.badge-1 { background: rgba(100,160,255,.12); color: #64a0ff; }
.badge-2 { background: rgba(126,184,154,.12); color: var(--accent2); }
.badge-3 { background: rgba(92,184,126,.15); color: var(--ok); }

.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 16px; border-radius: 8px;
  font-size: .8rem; font-weight: 600;
  text-decoration: none; border: none; cursor: pointer;
  transition: all .2s; font-family: inherit;
}
.btn-primary { background: var(--accent); color: #1a1200; }
.btn-primary:hover { background: #dbbe84; }
.btn-sm { padding: 5px 12px; font-size: .75rem; }
.btn-ghost { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
.btn-ghost:hover { border-color: var(--accent); color: var(--accent); }

@media (max-width: 1024px) {
  .stats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .sidebar { display: none; }
  .main { margin-right: 0; padding: 20px; }
  .stats { grid-template-columns: 1fr 1fr; }
}
</style>
</head>
<body>

<!-- ─── Sidebar ─────────────────────────────── -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="brand">☕ کافه تاسیان</div>
    <div class="sub">پنل مدیریت</div>
  </div>
  <nav>
    <a href="index.php"    class="nav-item <?= $current_page=='index.php'?'active':'' ?>">
      <span class="icon">📊</span> داشبورد
    </a>
    <a href="orders.php"   class="nav-item <?= $current_page=='orders.php'?'active':'' ?>">
      <span class="icon">🧾</span> سفارشات
    </a>
    <a href="products.php" class="nav-item <?= $current_page=='products.php'?'active':'' ?>">
      <span class="icon">📦</span> محصولات
    </a>
    <a href="notifications.php" class="nav-item">
    <span>🔔</span> اطلاعیه‌ها
    </a>
    <a href="users.php"    class="nav-item <?= $current_page=='users.php'?'active':'' ?>">
      <span class="icon">👥</span> کاربران
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="nav-item" style="color:var(--danger);">
      <span class="icon">🚪</span> خروج
    </a>
  </div>
</aside>

<!-- ─── Main ─────────────────────────────────── -->
<main class="main">
  <div class="page-title">داشبورد</div>
  <div class="page-sub">خوش آمدید، <?= htmlspecialchars($_SESSION['username']) ?> — نمای کلی فروشگاه</div>

  <!-- آمار -->
  <div class="stats">
    <div class="stat-card c1">
      <div class="stat-icon">👤</div>
      <div class="stat-label">کاربران ثبت‌نام شده</div>
      <div class="stat-value"><?= number_format($user_count) ?></div>
    </div>
    <div class="stat-card c2">
      <div class="stat-icon">📦</div>
      <div class="stat-label">محصولات فعال</div>
      <div class="stat-value"><?= number_format($product_count) ?></div>
    </div>
    <div class="stat-card c3">
      <div class="stat-icon">🧾</div>
      <div class="stat-label">تعداد سفارشات</div>
      <div class="stat-value"><?= number_format($order_count) ?></div>
    </div>
    <div class="stat-card c4">
      <div class="stat-icon">💰</div>
      <div class="stat-label">فروش کل (تومان)</div>
      <div class="stat-value" style="font-size:1.35rem;"><?= number_format($total_sales) ?></div>
    </div>
  </div>

  <!-- آخرین سفارش‌ها -->
  <div class="card">
    <div class="card-header">
      <h2>آخرین سفارشات</h2>
      <a href="orders.php">مشاهده همه ←</a>
    </div>
    <table>
      <thead>
        <tr>
          <th>کد سفارش</th>
          <th>کاربر</th>
          <th>تاریخ</th>
          <th>مبلغ (تومان)</th>
          <th>وضعیت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $states = ['0'=>['ثبت شده','badge-0'], '1'=>['در حال آماده‌سازی','badge-1'], '2'=>['آماده تحویل','badge-2'], '3'=>['تحویل داده شده','badge-3']];
        while ($row = mysqli_fetch_assoc($recent_orders)):
          [$label,$cls] = $states[$row['state']] ?? ['نامشخص','badge-0'];
        ?>
        <tr>
          <td><strong style="color:var(--accent)">#<?= $row['trackcode'] ?></strong></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td style="color:var(--muted)"><?= $row['orderdate'] ?></td>
          <td><?= number_format($row['total']) ?></td>
          <td><span class="badge <?= $cls ?>"><?= $label ?></span></td>
          <td><a href="order-detail.php?track=<?= $row['trackcode'] ?>" class="btn btn-ghost btn-sm">مشاهده</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>