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
        <pre class="text-info">
            การติดตั้งเพื่อใช้งานเบื้องต้น
            กำหนดค่าคอนฟิกใน include/config.php
            ติดตั้ง database จาก schema/radius.sql
            เพิ่ม user admin ลงในตาราง users
            Login เข้าระบบโดยใช้ user admin
            การโอนข้อมูลเข้าระบบ
            - เมนู จัดการระบบ
            -- เมนู จัดการไฟล์
            --- อัพโหลดไฟล์
            -- -ทำการตรวจสอบไฟล์
            --- โอนข้อมูลเข้าระบบ
            ให้กำหนดกลุ่มผู้ใช้และตั้งค่าการดาวน์โหลด/อัพโหลด
            -- เมนู แก้ไขตั้งค่ากลุ่มผู้ใช้
            --- จัดการ ลบ แก้ไข หรือเพิ่มข้อมูล กลุ่มผู้ใช้
            หลังจากนั้นทำการโอนข้อมูลเข้าระบบ
            -- เมนู โอน/ลบข้อมูล           
        </pre>
</div>
<?php require_once INC_PATH.'footer.php'; ?>
