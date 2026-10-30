<?php
session_start();
require_once __DIR__ . '/db_config.php';

function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function money($value) { return number_format((float)$value, 2); }
function redirect($url) { header('Location: ' . $url); exit; }
function flash($type, $message) { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function get_flash() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
function active_page($page) { return basename($_SERVER['PHP_SELF']) === $page ? 'active' : ''; }
function category_rows($con) {
    $sql = "SELECT c.expense_category_id, c.expense_category_name, c.amount,
            COALESCE(SUM(e.amount_spent),0) AS spent
            FROM expense_category_tbl c
            LEFT JOIN expense_tbl e ON e.expense_category_id = c.expense_category_id
            GROUP BY c.expense_category_id, c.expense_category_name, c.amount
            ORDER BY c.expense_category_name ASC";
    return mysqli_query($con, $sql);
}
function expense_rows($con, $search = '') {
    $search = trim($search);
    if ($search !== '') {
        $like = '%' . $search . '%';
        $stmt = mysqli_prepare($con, "SELECT e.*, c.expense_category_name FROM expense_tbl e JOIN expense_category_tbl c ON c.expense_category_id=e.expense_category_id WHERE c.expense_category_name LIKE ? OR e.expense_description LIKE ? ORDER BY e.expense_date DESC, e.expense_id DESC");
        mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    return mysqli_query($con, "SELECT e.*, c.expense_category_name FROM expense_tbl e JOIN expense_category_tbl c ON c.expense_category_id=e.expense_category_id ORDER BY e.expense_date DESC, e.expense_id DESC");
}
function dashboard_stats($con) {
    $stats = ['budget'=>0,'spent'=>0,'balance'=>0,'transactions'=>0,'categories'=>0];
    $r = mysqli_query($con, "SELECT COALESCE(SUM(amount),0) budget FROM expense_category_tbl"); $stats['budget'] = mysqli_fetch_assoc($r)['budget'];
    $r = mysqli_query($con, "SELECT COALESCE(SUM(amount_spent),0) spent, COUNT(*) transactions FROM expense_tbl"); $row = mysqli_fetch_assoc($r); $stats['spent'] = $row['spent']; $stats['transactions'] = $row['transactions'];
    $r = mysqli_query($con, "SELECT COUNT(*) categories FROM expense_category_tbl"); $stats['categories'] = mysqli_fetch_assoc($r)['categories'];
    $stats['balance'] = $stats['budget'] - $stats['spent'];
    return $stats;
}
function render_header($title, $active = 'index.php') {
    $flash = get_flash();
    ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> · Hassan Expense Tracker</title>
<link rel="stylesheet" href="font-awesome-4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="modern.css">
</head><body>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
  <div class="brand"><div class="brand-mark"><i class="fa fa-line-chart"></i></div><div><strong>Expense</strong><span>Tracker</span></div></div>
  <div class="profile"><div class="avatar">H</div><div><strong>Hassan</strong><span>Administrator</span></div></div>
  <nav class="nav"><div class="nav-label">Workspace</div>
    <a class="nav-item <?= $active==='index.php'?'active':'' ?>" href="index.php"><i class="fa fa-dashboard"></i><span>Dashboard</span></a>
    <a class="nav-item <?= $active==='expense_category.php'?'active':'' ?>" href="expense_category.php"><i class="fa fa-sliders"></i><span>Budgets & Categories</span></a>
    <a class="nav-item <?= $active==='expense_report.php'?'active':'' ?>" href="expense_report.php"><i class="fa fa-file-text-o"></i><span>Expense Report</span></a>
  </nav>
  <div class="sidebar-footer"><div class="developer"><span>Developed by</span><strong>Hassan</strong></div><span class="version">v2.0 · PHP</span></div>
</aside>
<main class="main">
<header class="topbar"><button class="menu-btn" id="menuBtn"><i class="fa fa-bars"></i></button><div class="crumb"><span>Workspace</span><i class="fa fa-angle-right"></i><strong><?= e($title) ?></strong></div><div class="top-actions"><div class="status-dot"><span></span> Local database</div><div class="user-pill"><span>H</span> Hassan</div></div></header>
<div class="content">
<?php if ($flash): ?><div class="toast <?= e($flash['type']) ?>"><i class="fa <?= $flash['type']==='success'?'fa-check-circle':'fa-exclamation-circle' ?>"></i><?= e($flash['message']) ?></div><?php endif; ?>
<?php }
function render_footer() { ?>
</div><footer class="footer"><span>Expense Tracker</span><span>Designed & developed by <strong>Hassan</strong></span></footer>
</main></div>
<script>const btn=document.getElementById('menuBtn'),side=document.getElementById('sidebar');if(btn){btn.onclick=()=>side.classList.toggle('open')}setTimeout(()=>{document.querySelectorAll('.toast').forEach(x=>x.classList.add('hide'))},3500);</script>
</body></html>
<?php }
