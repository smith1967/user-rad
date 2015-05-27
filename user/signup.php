<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$active = 'signup';
?>
<?php
if (isset($_POST['submit'])) {
    $data = $_POST;
    $valid = do_validate($data);  // check ความถูกต้องของข้อมูล
    if (!$valid) {
        show_message();
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
    $(document).ready(function () {
        $("#username").focus();
    });
</script>
<?php
show_message();
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
                <label class="control-label col-xs-2" for="password">รหัสผ่าน</label>
                <div class="col-xs-3">
                    <input type="password" class="input-xlarge" id="password" name="password" value='<?php echo isset($password) ? $password : ''; ?>'>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-2" for="confirm_password">ยืนยันรหัสผ่าน</label>
                <div class="col-xs-3">
                    <input type="password" class="input-xlarge" id="confirm_password" name='confirm_password' value='<?php echo isset($confirm_password) ? $confirm_password : ''; ?>'>
                    <p class="help-block">รหัสผ่านต้องประกอบตัวอักษรตัวเล็ก ตัวใหญ่ และตัวเลขความยาวไม่น้อยกว่า 6 ตัวอักษร</p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="pid">เลขบัตรประชาชน</label>
                <div class="col-xs-3">
                    <input type="text" class="input-xlarge" id="pid" name="pid" placeholder="0123456789012" value='<?php echo isset($pid) ? $pid : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="email">อีเมล์</label>
                <div class="col-xs-3">
                    <input type="text" class="input-xlarge" id="email" name="email" placeholder="smith@cstc.ac.th" value='<?php echo isset($email) ? $email : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="fname">ชื่อ</label>
                <div class="col-xs-3">
                    <input type="text" class="input-xlarge" id="fname" name="fname" placeholder="สมิทธ์" value='<?php echo isset($fname) ? $fname : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="lname">นามสกุล</label>
                <div class="col-xs-3">
                    <input type="text" class="input-xlarge" id="lname" name="lname" placeholder="สุขขี" value='<?php echo isset($lname) ? $lname : ''; ?>' >
                </div>
            </div>
            <div class="form-group"> 
                <label class="control-label col-xs-2 col-md-2" for="gid">กลุ่มผู้ใช้</label>
                <div class="col-xs-6 col-md-2">
                    <select class='form-control input-sm'id="gid" name="gid">
<?php
$gid_list = array(
    '1' => 'ผู้ดูแลระบบ',
    '2' => 'ครู',
    '3' => 'เจ้าหน้าที่',
    '4' => 'อื่นๆ'
);
$def = isset($gid) ? $gid : '2';
echo gen_option($gid_list, $def)
?>
                    </select>              
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-2" for="department">แผนก/งาน</label>
                <div class="col-xs-3 col-sm-3">
                    <input type="text" class="input-xlarge" id="department" name="department" value='<?php echo isset($department) ? $department : ''; ?>'>
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    <div class="checkbox">
                        <label><input type="checkbox" id='agree' name='agree' value='1'>ยืนยันข้อมูลถูกต้อง</label>
                    </div>
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
    $valid = TRUE;
    if (!preg_match('/[a-zA-Z0-9_@]{5,}/', $data['username'])) {
        set_err('ชื่อผู้ใช้ต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 5 ตัวอักษร');
        $valid = FALSE;
    }
    if (!preg_match('/[a-zA-Z0-9_@]{6,}/', $data['password'])) {
        set_err('รหัสผ่านต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 6 ตัวอักษร');
        $valid = FALSE;
    }
    if ($data['password'] != $data['confirm_password']) {
        set_err('รหัสยืนยันไม่ตรงกับรหัสผ่าน');
        $valid = FALSE;
    }
    if ($data['password'] == $data['username']) {
        set_err('ชื่อผู้ใช้กับรหัสผ่านต้องไม่เหมือนกันครับ');
        $valid = FALSE;
    }
    if (empty($data['fname'])) {
        set_err('กรุณาใส่ชื่อด้วยครับ');
        $valid = FALSE;
    }
    if (empty($data['lname'])) {
        set_err('กรุณาใส่นามสกุลด้วยครับ');
        $valid = FALSE;
    }
    if (check_pid($data['pid'])) {
        set_err('ตรวจสอบรหัสบัตรประชาชนให้ถูกต้องครับ');
        $valid = FALSE;
    }
    if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) == FALSE) {
        set_err('รูปแบบอีเมล์ไม่ถูกต้อง');
        $valid = FALSE;
    }
    if (empty($data['department'])) {
        set_err('กรุณาใส่ชื่อแผนก/งานด้วยครับ');
        $valid = FALSE;
    }
    if (empty($data['agree'])) {
        set_err('กรุณายืนยันข้อมูล');
        $valid = FALSE;
    }
    return $valid;
    /* ----End Validate ---- */
}
?>
<?php require_once INC_PATH . 'footer.php'; ?>

