<?php
require_once 'app.php';
$id=(int)($_POST['getID']??0);$name=trim($_POST['expense_name']??'');$amount=(float)($_POST['amount']??0);
if($_SERVER['REQUEST_METHOD']==='POST' && $id && $name!==''){ $stmt=mysqli_prepare($con,'UPDATE expense_category_tbl SET amount=?,expense_category_name=? WHERE expense_category_id=?');mysqli_stmt_bind_param($stmt,'dsi',$amount,$name,$id);mysqli_stmt_execute($stmt);flash('success','Budget updated.'); }
redirect('expense_category.php');
