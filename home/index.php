<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ผู้ดูแลระบบ";
$active = 'home';   // active menu
?>
<?php
if (isset($_SESSION['err'])):
    echo show_error($_SESSION['err']);
    unset($_SESSION['err']);
    //var_dump($_SESSION);
endif;
if (isset($_SESSION['info'])):
    echo show_info($_SESSION['info']);
    unset($_SESSION['info']);
endif;
?>
<?php require_once INC_PATH.'header.php'; ?>
<div class='container'>
        <div class="page-header">
          <h1>ระบบจัดการข้อมูลผู้ใช้เครือข่าย</h1>
        </div>
        <p class="lead">ยินดีต้อนรับเข้าสู่ระบบจัดการผู้ใช้เครือข่ายอินเตอร์เน็ต วิทยาลัยเทคนิคฉะเชิงเทรา</p>
</div>
<?php require_once INC_PATH.'footer.php'; ?>
