<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
?>
<?php
if (isset($_POST['submit'])) {
    $data = $_POST;
        $err = do_save();
        show_error($err);
}
?>
<script>
$(document).ready(function(){
  //$("#btn1").click(function(){
    $("#username").focus();
    //$("p").html("focus event triggered");
  //});  
});
</script>
<div class='container'>
    <div class="page-header">
        <h2>กรอกข้อมูลสมัครเข้าใช้ระบบ</h2>
    </div>
    
    <form class="form-horizontal" id="signupfrm" method="post" action="">
        <fieldset>
            <div class="form-group">
              <label class="control-label col-xs-2" for="username">ชื่อเข้าระบบ</label>
              <div class="col-xs-3">
                <input type="text" class="input-xlarge" id="username" name="username" placeholder="Username">
                <p class="help-block">ชื่อต้องเป็นภาษาอังกฤษหรือตัวเลขความยาวไม่ต่ำกว่า 5 ตัวอักษร</p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="password">รหัสผ่าน</label>
              <div class="col-xs-3">
                <input type="password" class="input-xlarge" id="password" name="password">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-xs-2" for="confirm_password">ยืนยันรหัสผ่าน</label>
              <div class="col-xs-3">
                <input type="password" class="input-xlarge" id="confirm_password" name='confirm_password'>
                <p class="help-block">รหัสผ่านต้องประกอบตัวอักษรตัวเล็ก ตัวใหญ่ และตัวเลขความยาวไม่น้อยกว่า 6 ตัวอักษร</p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="pid">เลขบัตรประชาชน</label>
              <div class="col-xs-3">
                <input type="text" class="input-xlarge" id="pid" name="pid" placeholder="0123456789012">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="email">อีเมล์</label>
              <div class="col-xs-3">
                <input type="text" class="input-xlarge" id="email" name="email" placeholder="smith@cstc.ac.th">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="fname">ชื่อ</label>
              <div class="col-xs-3">
                <input type="text" class="input-xlarge" id="fname" name="fname" placeholder="สมิทธ์">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="lname">นามสกุล</label>
              <div class="col-xs-3">
                <input type="text" class="input-xlarge" id="lname" name="fname" placeholder="สุขขี" >
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="gid">กลุ่มผู้ใช้</label>
              <div class="col-xs-2">
                <select class='form-control'id="gid" name="gid">
                    <option value="1" >ผู้ดูแลระบบ</option>
                    <option value="2" selected="selected">ครู</option>
                    <option value="3">เจ้าหน้าที่</option>
                    <option value="4">นักเรียน</option>
                    <option value="5">อื่นๆ</option>
                </select>              
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="department">แผนก/งาน</label>
              <div class="col-xs-3 col-sm-3">
                <input type="text" class="input-xlarge" id="department" name="department">
              </div>
            </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                <div class="checkbox">
                    <label><input type="checkbox" id='agree' name='agree'>ยืนยันข้อมูลถูกต้อง</label>
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
    $err = do_validate();
    if (count($err) > 0)
        return $err;
    /* ----End Validate ---- */

    $sql = "INSERT INTO `member` 
        (`mem_id`, `mem_user`,`mem_fname_th`, `mem_lname_th`, `mem_pass`, `mem_disp_name`, `mem_reg_date`)
        VALUES
        (NULL, ?, ?, ?, ?, ?, now());";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $data['mem_user'], $data['mem_fname_th'], $data['mem_lname_th'], $data['mem_pass'], $mem_disp_name);
    $mem_disp_name = $data['mem_fname_th'].' '.$data['mem_lname_th'];
    /* execute prepared statement */
    mysqli_stmt_execute($stmt);
    if(mysqli_stmt_affected_rows($stmt)>0){
        $_SESSION['ses_msg'] = "ลงทะเบียนเรียบร้อยครับ กรุณากรอกข้อมูลเบื้องต้นให้ครบถ้วน";
        redirect('home/edit_profile');
    } else {
        $_SESSION['ses_msg'] = "ลงทะเบียนไม่สำเร็จ";        
    }
    /* close statement and connection */
    mysqli_stmt_close($stmt);
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
    if (empty($data['fname']))
        $err[] = 'ใส่ชื่อด้วยครับ';
    if (empty($data['lname']))
        $err[] = 'ใส่นามสกุลด้วยครับ';
    if (!preg_match('/[a-zA-Z0-9_@]{5,}/', $data['password']))
        $err[] = 'รหัสผ่านต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 5 ตัวอักษร';
    if ($data['password'] != $data['confirm_password'])
        $err[] = 'รหัสยืนยันไม่ตรงกับรหัสผ่าน';
    return $err;
    /* ----End Validate ---- */
}
?>

