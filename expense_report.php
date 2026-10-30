<?php
require_once 'app.php';
$search=trim($_GET['q']??'');
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='delete'){
 $id=(int)$_POST['expense_id'];$stmt=mysqli_prepare($con,'DELETE FROM expense_tbl WHERE expense_id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);flash('success','Expense deleted successfully.');redirect('expense_report.php');
}
$rows=expense_rows($con,$search);$stats=dashboard_stats($con);render_header('Expense Report','expense_report.php');
?>
<div class="page-head"><div><div class="eyebrow">Transactions</div><h1>Expense report</h1><p>Search, review and remove recorded expenses.</p></div><a class="btn btn-primary" href="index.php"><i class="fa fa-plus"></i> Record expense</a></div>
<section class="card table-card"><div class="table-toolbar"><form class="search" method="get"><i class="fa fa-search"></i><input name="q" value="<?= e($search) ?>" placeholder="Search category or description..."></form><div class="inline-actions"><a class="btn btn-light" href="expense_report.php"><i class="fa fa-refresh"></i> Reset</a><a class="btn btn-light" href="export.php"><i class="fa fa-download"></i> CSV</a></div></div><div class="table-wrap"><table class="table"><thead><tr><th>Category</th><th>Description</th><th>Date</th><th>Amount</th><th>Action</th></tr></thead><tbody><?php if(mysqli_num_rows($rows)): while($r=mysqli_fetch_assoc($rows)): ?><tr><td><span class="badge badge-green"><?= e($r['expense_category_name']) ?></span></td><td><?= e($r['expense_description']) ?></td><td><?= e(date('d M Y',strtotime($r['expense_date']))) ?></td><td><strong>PKR <?= money($r['amount_spent']) ?></strong></td><td><form method="post" onsubmit="return confirm('Delete this expense?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="expense_id" value="<?= (int)$r['expense_id'] ?>"><button class="btn btn-light" style="color:#dc2626"><i class="fa fa-trash-o"></i></button></form></td></tr><?php endwhile; else: ?><tr><td colspan="5" class="empty"><i class="fa fa-search" style="font-size:24px"></i><br><br>No expenses found.</td></tr><?php endif; ?></tbody></table></div></section>
<?php render_footer(); ?>
