<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "แก้ไขข้อมูล";
$active = 'edit-user';
//$subactive = 'home';
!is_auth()? redirect():'';
?>
<?php
if (isset($_POST['submit'])) {
    $data = $_POST;
    $err = do_validate();
    if(count($err)){
        show_error($err);
        foreach ($_POST as $k => $v) {
            $$k = $v;  // set variable to form
        } 
    }else{
        do_update();  // ไม่มี error บันทึกข้อมูล
    }
    /*    
    $sql = "SELECT * FROM `register` WHERE `pid` LIKE '" . $data['pid'] . "';";
    $rs = mysqli_query($db,$sql);
    if (mysqli_num_rows($rs) > 0) {
        echo "รหัสประชาชนถูกใช้ไปแล้วครับ";
        die();
    }

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

    //echo "test test: ".$sql;
    mysqli_query($db, $sql);
    if (mysqli_affected_rows($db))
        echo "บันทึกข้อมูลเรียบร้อยแล้วครับ";
    else
        echo "ไม่สามารถบันทึกข้อมูลได้ -> " . mysql_error();
     * 
     */
}else{
    foreach ($_SESSION['user'] as $k => $v){
        $$k = $v;
    }
}
?>
<?php require_once INC_PATH.'header.php'; ?>
<script>
$(document).ready(function(){
    $("#username").focus();
});
</script>
<div class='container'>
    <div class="page-header">
        <h2>แก้ไขข้อมูลส่วนตัว</h2>
    </div>
        <?php if(isset($_SESSION['error'])): ?>
            <div class='alert-danger'><?php echo $_SESSION['error']?></div>
        <?php
            unset($_SESSION['error']);
            endif; 
        ?>
    <form class="form-horizontal" id="signupfrm" method="post" action="">
        <fieldset>
            <div class="form-group">
              <label class="control-label col-xs-2" for="username">ชื่อเข้าระบบ</label>
              <div class="col-xs-3">
                  <input type="text" class="input-xlarge" id="username" name="username" placeholder="Username" value='<?php echo isset($username)?$username:'';?>'>
                <p class="help-block">ชื่อต้องเป็นภาษาอังกฤษหรือตัวเลขความยาวไม่ต่ำกว่า 5 ตัวอักษร</p>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-xs-2" for="pid">เลขบัตรประชาชน</label>
              <div class="col-xs-3">
                  <input type="text" class="input-xlarge" id="pid" name="pid" placeholder="0123456789012" value='<?php echo isset($pid)?$pid:'';?>'>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-xs-2" for="fname">ชื่อ</label>
              <div class="col-xs-3">
                  <input type="text" class="input-xlarge" id="fname" name="fname" placeholder="สมิทธ์" value='<?php echo isset($fname)?$fname:'';?>'>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="lname">นามสกุล</label>
              <div class="col-xs-3">
                <input type="text" class="input-xlarge" id="lname" name="lname" placeholder="สุขขี" value='<?php echo isset($lname)?$lname:'';?>' >
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
                    $def = isset($gid)?$gid:'2';
                    echo gen_option($gid_list, $def)
                    ?>
                </select>              
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
<?php require_once INC_PATH.'footer.php'; ?>
<?php

function do_update() {
    global $db;
    $data = &$_POST;
    //var_dump($data);
    //die();
    foreach ($_POST as $k => $v) {
            $$k = pq($v);  // set variable to form
    }
    $id = $_SESSION['user']['id'];

    $sql = <<<EOD
            UPDATE users SET 
                username = {$username},
                fname = {$fname},
                lname = {$lname},
                pid = {$pid},
                email = {$email},
                pid = {$pid},
                department = {$department} 
            WHERE
                id = {$id};
EOD;
    mysqli_query($db, $sql);
    if (mysqli_affected_rows($db)>0){   
        $_SESSION['info'][] = "แก้ไขเรียบร้อยครับ";
        redirect('home/index');
    } else {
        $_SESSION['err'][] = "แก้ไขข้อมูลไม่สำเร็จกรุณาตรวจสอบข้อมูล".  mysqli_error($db) .$sql;        
        redirect('user/edit-user');
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


function do_validate() {
    $data = &$_POST;
    /* ---- Validate ---- */
    $err = array();
    if (!preg_match('/[a-zA-Z0-9_@]{5,}/', $data['username']))
        $err[] = 'ชื่อผู้ใช้ต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 5 ตัวอักษร';
    //if (!preg_match('/[a-zA-Z0-9_@]{6,}/', $data['password']))
    //    $err[] = 'รหัสผ่านต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 6 ตัวอักษร';
    //if ($data['password'] != $data['confirm_password'])
    //    $err[] = 'รหัสยืนยันไม่ตรงกับรหัสผ่าน';
    if (empty($data['fname']))
        $err[] = 'กรุณาใส่ชื่อด้วยครับ';
    if (empty($data['lname']))
        $err[] = 'กรุณาใส่นามสกุลด้วยครับ';
    if (check_pid($data['pid']))
        $err[] = 'ตรวจสอบรหัสบัตรประชาชนให้ถูกต้องครับ';
    /*
    if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)==FALSE)
        $err[] = 'รูปแบบอีเมล์ไม่ถูกต้อง';
    if (empty($data['department']))
        $err[] = 'กรุณาใส่ชื่อแผนก/งานด้วยครับ';
     * 
     */
    if (empty($data['agree']))
        $err[] = 'กรุณายืนยันข้อมูล';
    return $err;
    /* ----End Validate ---- */
}
?>

