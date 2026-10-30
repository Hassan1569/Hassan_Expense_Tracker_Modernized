<?php
$con = mysqli_connect('localhost', 'root', '', 'expense_tracker');
if (!$con) { die('Database connection failed. Check db_config.php and MySQL.'); }
mysqli_set_charset($con, 'utf8mb4');
