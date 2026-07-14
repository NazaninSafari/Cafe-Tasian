<?php
// فایل مشترک ناوبری ادمین
// این فایل در هر صفحه ادمین include می‌شود
if (!isset($_SESSION["state_login"]) || $_SESSION["state_login"] !== true) {
    header("../sign-in.php");
    exit();
}
// بررسی نقش ادمین
$check_admin_link = mysqli_connect('localhost', 'root', '', 'Coffee_db');
$admin_check = mysqli_query($check_admin_link, "SELECT type FROM user WHERE id='{$_SESSION['id']}'");
$admin_row = mysqli_fetch_assoc($admin_check);
if (!$admin_row || $admin_row['type'] != 1) {
    header("../index.php");
    exit();
}
mysqli_close($check_admin_link);
$current_page = basename($_SERVER['PHP_SELF']);
?>