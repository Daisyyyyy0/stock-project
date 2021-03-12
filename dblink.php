<?php

$host = "localhost";
$user = "root";
$passwd = "root";
$database = "AIstock";

$connect = new mysqli($host, $user, $passwd, $database);

if ($connect->connect_error)
    die("連線失敗: " . $connect->connect_error);
// echo "連線成功";
?>