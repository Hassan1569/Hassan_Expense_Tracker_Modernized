<?php
require_once 'app.php';
header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=expense-report.csv');
$out=fopen('php://output','w');fputcsv($out,['Category','Description','Date','Amount (PKR)']);$rows=expense_rows($con,'');while($r=mysqli_fetch_assoc($rows))fputcsv($out,[$r['expense_category_name'],$r['expense_description'],$r['expense_date'],$r['amount_spent']]);fclose($out);exit;
