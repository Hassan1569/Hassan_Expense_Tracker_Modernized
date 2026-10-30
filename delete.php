<?php
require_once 'app.php';
$id=(int)($_POST['expense_id']??$_GET['expense_id']??0); if($id){$stmt=mysqli_prepare($con,'DELETE FROM expense_tbl WHERE expense_id=?');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);flash('success','Expense deleted successfully.');} redirect('expense_report.php');
