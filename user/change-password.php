<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$active = 'change-password';
?>
<?php
if (isset($_POST['submit'])) {
    $data = $_POST;
    $err = do_validate($data);  // check ความถูกต้องของข้อมูล
    if (count($err)) {
        show_error($err);
        foreach ($_POST as $k => $v) {
            $$k = $v;  // set variable to form
        }
    } else {
        do_save();  // ไม่มี error บันทึกข้อมูล
    }
}
?>
<?php require_once INC_PATH . 'header.php'; ?>
<script>
    $(document).ready(function() {
        $("#username").focus();
    });
</script>
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
<div class='container'>
    <div class="page-header">
        <h2>กรอกข้อมูลสมัครเข้าใช้ระบบ</h2>
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
                    <input type="password" class="input-xlarge" id="password" name="password" value='<?php echo isset($password) ? $password : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="confpass">ยืนยันรหัสผ่าน</label>
                <div class="col-xs-3">
                    <input type="password" class="input-xlarge" id="confirm_password" name='confirm_password' value='<?php echo isset($confirm_password) ? $confirm_password : ''; ?>'>
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

function do_save() {
    global $db;
    $data = &$_POST;
    //var_dump($data);
    //die();
    $sql = "INSERT INTO `register` (
			`id` ,`username` ,
			`fname`,`lname` ,
			`gid`, `password` ,
			`email`,`comfirm` ,
			`pid`,`department`,
			`created`,`hostname`
		)VALUES(
			NULL," . pq($data['username']) . ",
			" . pq($data['fname']) . "," . pq($data['lname']) . ",
			" . pq($data['gid']) . "," . pq($data['password']) . ",
			" . pq($data['email']) . ",'N',
			" . pq($data['pid']) . "," . pq($data['department']) . ",
			NOW()," . pq(get_ip()) . ");";

    // die("sql: ".$sql);
    mysqli_query($db, $sql);
    if (mysqli_affected_rows($db) > 0) {
        $_SESSION['info'] = "ลงทะเบียนเรียบร้อยครับ";
        redirect('home/index');
    } else {
        $_SESSION['error'] = "ลงทะเบียนไม่สำเร็จ กรุณาตรวจสอบข้อมูล" . mysqli_error($db) . $sql;
        redirect('user/signup');
    }
    /* close statement and connection */
    //redirect();
}

function get_info($mem_id) {
    global $db;
    $query = "SELECT * FROM member WHERE mem_id='" . pq($mem_id + 0) . "'";
    $res = mysqli_query($db, $query);
    return $res;
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

