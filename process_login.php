<?php
session_start();
$con = new mysqli("localhost", "root", "" , "system_pos");
$con->set_charset("utf-8");


$sql = "SELECT * FROM account WHERE username = '".$_POST["username"]."' AND password = '".$_POST["password"]."' ";
$result = $con->query($sql);
$num = $result->num_rows;

if($num > 0){
    $row = $result->fetch_assoc();
    $_SESSION["account_info"] = $row;
    header("location:index.php");
}else{
    $_SESSION["error_login"] = "ชื่อผู้ใช้งานหรือรหัสไม่ถูกต้อง";
    header("location:login.php");
}


