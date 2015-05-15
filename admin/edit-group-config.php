<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "แก้ไขตั้งค่ากลุ่มผู้ใช้";
$active = 'admin';
$subactive = 'edit-group-config';
is_admin('home/index');
?>
<?php
if (isset($_POST['submit'])) {
    $data = $_POST;
    $valid = do_validate($data);  // check ความถูกต้องของข้อมูล
    if (!$valid) {
        foreach ($_POST as $k => $v) {
            $$k = $v;  // set variable to form
        }
    } else {
        do_update();  // ไม่มี error บันทึกข้อมูล
    }
}
?>
<?php require_once INC_PATH . 'header.php'; ?>
<script>
    $(document).ready(function() {
        $("#username").focus();
    });
</script>

<div class='container'>
    <?php show_message(); ?>
    <div class="page-header">
        <h2>แก้ไขรหัสผ่าน</h2>
    </div>

    <form class="form-horizontal" id="signupfrm" method="post" action="">
        <fieldset>
            <div class="form-group">
                <label class="control-label col-xs-2" for="username">ชื่อผู้ใช้</label>
                <div class="col-xs-3">
                    <input type="text" class="input-xlarge" id="username" name="username" placeholder="Username" value='<?php echo isset($username) ? $username : ''; ?>'>
                    <p class="help-block">ชื่อผู้ใช้ต้องเป็นภาษาอังกฤษหรือตัวเลขความยาวไม่ต่ำกว่า 5 ตัวอักษร</p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="password">รหัสผ่านเดิม</label>
                <div class="col-xs-3">
                    <input type="password" class="input-xlarge" id="password" name="password" value='<?php echo isset($password) ? $password : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="newpass">รหัสผ่านใหม่</label>
                <div class="col-xs-3">
                    <input type="password" class="input-xlarge" id="password" name="newpass" value='<?php echo isset($newpass) ? $newpass : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="confpass">ยืนยันรหัสผ่านใหม่</label>
                <div class="col-xs-3">
                    <input type="password" class="input-xlarge" id="confirm_password" name='confpass' value='<?php echo isset($confpass) ? $confpass : ''; ?>'>
                    <p class="help-block">รหัสผ่านต้องประกอบตัวอักษรตัวเล็ก ตัวใหญ่ และตัวเลขความยาวไม่น้อยกว่า 6 ตัวอักษร</p>
                </div>
            </div>     
            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    <button type="submit" class="btn btn-primary" name='submit'>บันทึกข้อมูล</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function do_update() {
    global $db;
    $data = &$_POST;
    $query = "SELECT * FROM users WHERE username=" . pq($data['username']) . " AND password = " . pq($data['password']);
    //die($query);
    $result = mysqli_query($db, $query);
    if (mysql_num_rows($result) == 0) {
        set_err('กรุณาตรวจสอบชื่อผู้ใช้และรหัสผ่าน');
        redirect('user/change-password');
        return;
    }
    $query = "UPDATE users SET password = " . pq($data['newpass']) . " WHERE username = " . pq($data['username']);
    $result = mysqli_query($db, $query);
    mysqli_affected_rows($db) > 0 ? set_info('แก้ไขรหัสผ่านสำเร็จ') : set_err('ไม่สามารถแก้ไขรหัสผ่าน' . mysqli_error($db))  ;
    if ($data['username'] !== 'admin'):
        $query = "UPDATE radcheck SET value = " . pq($data['newpass']) . " WHERE username = " . pq($data['username']) . " AND Attribute ='Password'";
        $result = mysqli_query($db, $query);
        mysqli_affected_rows($db) < 1 ? set_err('ไม่สามารถแก้ไขรหัสผ่าน' . mysqli_error($db)) : set_info('แก้ไขรหัสผ่าน radcheck สำเร็จ');
    endif;
    redirect('user/change-password');
}

function do_validate($data) {
    $valid = true;
    if (!preg_match('/[a-zA-Z0-9_@]{5,}/', $data['username'])) {
        set_err('ชื่อผู้ใช้ต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 5 ตัวอักษร');
        $valid = false;
    }
    if (!preg_match('/[a-zA-Z0-9_@]{6,}/', $data['newpass'])) {
        set_err('รหัสผ่านต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 6 ตัวอักษร');
        $valid = false;
    }
    if ($data['newpass'] != $data['confpass']) {
        set_err('รหัสยืนยันไม่ตรงกับรหัสผ่าน');
        $valid = false;
    }
    return $valid;
}
?>

