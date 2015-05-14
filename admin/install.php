<?php

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$sql = "SELECT * FROM users WHERE username ='admin'";
$result = mysqli_query($db, $sql);
if (mysqli_num_rows($result) == 0) {
    $sql = "INSERT INTO `users` (`id`, `username`, `password`, `fname`, `lname`, `groupname`, `pid`, `status`, `startban`, `access`, `hostname`) VALUES (NULL, \'admin\', \'password\', \'Admin\', \'ผู้ดูแลระบบ\', \'Admin\', \'99999\', \'Y\', \'0000-00-00 00:00:00\', \'0000-00-00 00:00:00\', \'\');";
    $result = mysqli_query($db, $sql);
    set_info("เพ่ิมข้อมูลผู้ดูแลระบบเรียบร้อย username=admin password=password กรุณาเปลี่ยนรหัสผ่าน");
    redirect('user/changepassword');
}

