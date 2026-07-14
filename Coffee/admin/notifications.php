<?php
session_start();
$link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
if (mysqli_connect_errno()) die("خطای اتصال: " . mysqli_connect_error());
require '_nav.php';

// حذف اطلاعیه
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    mysqli_query($link, "DELETE FROM notification WHERE id=$del_id");
    header("Location: notifications.php?deleted=1");
    exit();
}

// افزودن اطلاعیه
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim(mysqli_real_escape_string($link, $_POST['title']   ?? ''));
    $message = trim(mysqli_real_escape_string($link, $_POST['message'] ?? ''));
    if (!$title)   $error = 'عنوان الزامی است.';
    elseif (!$message) $error = 'متن اطلاعیه الزامی است.';
    else {
        mysqli_query($link, "INSERT INTO notification (title, message) VALUES ('$title', '$message')");
        header("Location: notifications.php?saved=1");
        exit();
    }
}

$notifs = mysqli_query($link, "SELECT * FROM notification ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مدیریت اطلاعیه‌ها – ادمین</title>
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
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;}
.page-title{font-size:1.6rem;font-weight:800;background:linear-gradient(90deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.card-header{padding:18px 22px;border-bottom:1px solid var(--border);}
.card-header h2{font-size:.95rem;font-weight:700;}
.card-body{padding:20px 22px;}
.field{margin-bottom:16px;}
.field label{display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:8px;}
.field input,.field textarea{width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);font-family:inherit;font-size:.88rem;outline:none;transition:border-color .2s;}
.field input:focus,.field textarea:focus{border-color:rgba(200,169,110,.4);}
.field textarea{resize:vertical;min-height:100px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:10px;font-size:.85rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-primary{background:var(--accent);color:#1a1000;}
.btn-primary:hover{background:#dbbe84;}
.btn-danger{background:rgba(224,92,92,.12);color:var(--danger);border:1px solid rgba(224,92,92,.2);}
.btn-danger:hover{background:rgba(224,92,92,.22);}
.btn-sm{padding:5px 12px;font-size:.75rem;}
.alert-ok {background:rgba(126,184,154,.1);border:1px solid rgba(126,184,154,.3);color:var(--accent2);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:.85rem;}
.alert-err{background:rgba(224,92,92,.1);border:1px solid rgba(224,92,92,.3);color:var(--danger);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:.85rem;}
.alert-del{background:rgba(224,160,80,.1);border:1px solid rgba(224,160,80,.3);color:#e0a050;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:.85rem;}
.notif-item{padding:16px 0;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:14px;}
.notif-item:last-child{border-bottom:none;}
.notif-dot{width:8px;height:8px;border-radius:50%;background:var(--accent);margin-top:6px;flex-shrink:0;}
.notif-content{flex:1;}
.notif-title{font-size:.88rem;font-weight:600;margin-bottom:4px;}
.notif-msg{font-size:.8rem;color:var(--muted);line-height:1.6;margin-bottom:6px;}
.notif-time{font-size:.72rem;color:var(--muted);}
.empty{text-align:center;padding:40px;color:var(--muted);font-size:.85rem;}
@media(max-width:768px){.sidebar{display:none;}.main{margin-right:0;padding:20px;}.grid2{grid-template-columns:1fr;}}
</style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="brand">☕ کافه تاسیان</div>
    <div class="sub">پنل مدیریت</div>
  </div>
  <nav>
    <a href="index.php"         class="nav-item"><span>📊</span> داشبورد</a>
    <a href="orders.php"        class="nav-item"><span>🧾</span> سفارشات</a>
    <a href="products.php"      class="nav-item"><span>📦</span> محصولات</a>
    <a href="users.php"         class="nav-item"><span>👥</span> کاربران</a>
    <a href="notifications.php" class="nav-item active"><span>🔔</span> اطلاعیه‌ها</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="nav-item" style="color:var(--danger);"><span>🚪</span> خروج</a>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div class="page-title">مدیریت اطلاعیه‌ها</div>
  </div>

  <?php if (isset($_GET['saved'])): ?>
  <div class="alert-ok">✅ اطلاعیه با موفقیت ارسال شد.</div>
  <?php endif; ?>
  <?php if (isset($_GET['deleted'])): ?>
  <div class="alert-del">🗑 اطلاعیه حذف شد.</div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="alert-err">⚠️ <?= $error ?></div>
  <?php endif; ?>

  <div class="grid2">

    <!-- فرم ارسال اطلاعیه -->
    <div class="card">
      <div class="card-header"><h2>ارسال اطلاعیه جدید</h2></div>
      <div class="card-body">
        <form method="POST" action="">
          <div class="field">
            <label>عنوان</label>
            <input type="text" name="title" placeholder="مثال: تخفیف ویژه آخر هفته"
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
          </div>
          <div class="field">
            <label>متن اطلاعیه</label>
            <textarea name="message" placeholder="متن اطلاعیه را بنویسید..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">🔔 ارسال به همه کاربران</button>
        </form>
      </div>
    </div>

    <!-- لیست اطلاعیه‌ها -->
    <div class="card">
      <div class="card-header"><h2>اطلاعیه‌های ارسال‌شده</h2></div>
      <div class="card-body">
        <?php
        $count = 0;
        while ($row = mysqli_fetch_assoc($notifs)):
          $count++;
          $diff = time() - strtotime($row['created_at']);
          if      ($diff < 3600)  $t = intval($diff/60)   . ' دقیقه پیش';
          elseif  ($diff < 86400) $t = intval($diff/3600) . ' ساعت پیش';
          else                    $t = intval($diff/86400) . ' روز پیش';
        ?>
        <div class="notif-item">
          <div class="notif-dot"></div>
          <div class="notif-content">
            <p class="notif-title"><?= htmlspecialchars($row['title']) ?></p>
            <p class="notif-msg"><?= htmlspecialchars($row['message']) ?></p>
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <span class="notif-time"><?= $t ?></span>
              <a href="notifications.php?delete=<?= $row['id'] ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('این اطلاعیه حذف شود؟')">🗑 حذف</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <div class="empty">هنوز اطلاعیه‌ای ارسال نشده</div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</main>

</body>
</html>