<?php
$link = mysqli_connect("localhost", "root", "", "LoginReg");

if (!$link) {
    die("❌ Kết nối thất bại: " . mysqli_connect_error());
}
?>