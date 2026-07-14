<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());
require '_nav.php';

// جستجو
$search = isset($_GET['q']) ? trim(mysqli_real_escape_string($link, $_GET['q'])) : '';
$where  = $search ? "WHERE username LIKE '%$search%' OR mobile LIKE '%$search%'" : '';

$users = mysqli_query($link, "SELECT id, username, mobile, email, type FROM user $where ORDER BY id DESC");
$total = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM user"))[0];
$admins = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM user WHERE type=1"))[0];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مدیریت کاربران – ادمین</title>
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
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;gap:16px;flex-wrap:wrap;}
.page-title{font-size:1.6rem;font-weight:800;background:linear-gradient(90deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.summary{display:flex;gap:14px;margin-bottom:24px;}
.sum-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px 22px;display:flex;flex-direction:column;gap:4px;min-width:130px;}
.sum-card .lbl{font-size:.75rem;color:var(--muted);}
.sum-card .val{font-size:1.5rem;font-weight:800;}
.sum-card.c1 .val{color:var(--accent);}
.sum-card.c2 .val{color:var(--accent2);}
.sum-card.c3 .val{color:#64a0ff;}
.search-box{display:flex;gap:10px;align-items:center;}
.search-box input{background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:9px 16px;color:var(--text);font-family:inherit;font-size:.85rem;outline:none;width:240px;transition:border-color .2s;}
.search-box input:focus{border-color:rgba(200,169,110,.4);}
.search-box button{padding:9px 18px;background:var(--accent);color:#1a1000;border:none;border-radius:10px;font-family:inherit;font-size:.85rem;font-weight:700;cursor:pointer;transition:all .2s;}
.search-box button:hover{background:#dbbe84;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead th{padding:13px 18px;font-size:.75rem;font-weight:600;color:var(--muted);text-align:right;background:var(--surface2);border-bottom:1px solid var(--border);}
tbody td{padding:13px 18px;font-size:.85rem;border-bottom:1px solid var(--border);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:rgba(255,255,255,.02);}
.avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;color:#1a1000;}
.user-cell{display:flex;align-items:center;gap:12px;}
.badge-role{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:700;}
.role-admin{background:linear-gradient(135deg,rgba(200,169,110,.2),rgba(200,169,110,.08));color:var(--accent);border:1px solid rgba(200,169,110,.3);}
.role-user{background:rgba(126,184,154,.1);color:var(--accent2);border:1px solid rgba(126,184,154,.2);}
.empty{text-align:center;padding:60px;color:var(--muted);}
@media(max-width:768px){.sidebar{display:none;}.main{margin-right:0;padding:20px;}.summary{flex-wrap:wrap;}}
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
    <div class="page-title">مدیریت کاربران</div>
    <form class="search-box" method="GET" action="">
      <input type="text" name="q" placeholder="جستجو نام یا موبایل..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit">🔍 جستجو</button>
    </form>
  </div>

  <!-- خلاصه آمار -->
  <div class="summary">
    <div class="sum-card c1">
      <span class="lbl">کل کاربران</span>
      <span class="val"><?= number_format($total) ?></span>
    </div>
    <div class="sum-card c2">
      <span class="lbl">کاربران عادی</span>
      <span class="val"><?= number_format($total - $admins) ?></span>
    </div>
    <div class="sum-card c3">
      <span class="lbl">ادمین‌ها</span>
      <span class="val"><?= number_format($admins) ?></span>
    </div>
  </div>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>کاربر</th>
          <th>موبایل</th>
          <th>ایمیل</th>
          <th>نقش</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $count = 0;
        while ($row = mysqli_fetch_assoc($users)):
          $count++;
          $initial = mb_substr($row['username'], 0, 1, 'UTF-8');
          $is_admin = intval($row['type']) === 1;
        ?>
        <tr>
          <td style="color:var(--muted);font-size:.8rem"><?= $row['id'] ?></td>
          <td>
            <div class="user-cell">
              <div class="avatar"><?= $initial ?></div>
              <strong><?= htmlspecialchars($row['username']) ?></strong>
            </div>
          </td>
          <td style="direction:ltr;text-align:right;color:var(--muted)"><?= htmlspecialchars($row['mobile'] ?? '—') ?></td>
          <td style="color:var(--muted);font-size:.82rem"><?= htmlspecialchars($row['email'] ?? '—') ?></td>
          <td>
            <?php if ($is_admin): ?>
            <span class="badge-role role-admin">👑 ادمین</span>
            <?php else: ?>
            <span class="badge-role role-user">👤 کاربر</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <tr><td colspan="5" class="empty">کاربری یافت نشد</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>