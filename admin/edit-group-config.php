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
    die(print_r($data));
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
<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
</div>
<div class='container'>
    <?php show_message(); ?>
            <div class="table table-responsive">
                <table class="table-striped table-condensed">
                    <tr><th>ชื่อกลุ่ม(Eng)</th><th>ชื่อ(Thai)</th><th>ดาวน์โหลด</th><th>อัพโหลด</th><th>กระทำการ</th></tr>
                    <?php
                    $configs = getConfigs();
                    foreach ($configs as $config) :
                        ?>                     
                            <tr>
                                <td><form method="post"><input type="hidden" value="<?php echo $config['gid'] ?>" name="gid"><input type="text" class="form-control input-sm" name="groupname" value="<?php echo $config['groupname'] ?>"</td>
                                <td><input type="text" class="form-control input-sm" name="group_desc" value="<?php echo $config['group_desc'] ?>"</td>
                                <td><input type="text" class="form-control input-sm" name="download" value="<?php echo $config['download'] ?>"</td>
                                <td><input type="text" class="form-control input-sm" name="upload" value="<?php echo $config['upload'] ?>"</td>
                                <td class="text-center"><button type="submit" class="btn btn-sm btn-primary" name="submit">บันทึก</button></form></td>
                            </tr>
                        </form>

                    <?php endforeach; ?>
                </table>
            </div>    
</div>  
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function getConfigs() {
    global $db;
    $configs = array();
    $sql = "SELECT * FROM group_config;";
    $rs = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($rs)) {
        $configs[] = $row;
    }
    return $configs;
}

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
    mysqli_affected_rows($db) > 0 ? set_info('แก้ไขรหัสผ่านสำเร็จ') : set_err('ไม่สามารถแก้ไขรหัสผ่าน' . mysqli_error($db));
    if ($data['username'] !== 'admin'):
        $query = "UPDATE radcheck SET value = " . pq($data['newpass']) . " WHERE username = " . pq($data['username']) . " AND Attribute ='Password'";
        $result = mysqli_query($db, $query);
        mysqli_affected_rows($db) < 1 ? set_err('ไม่สามารถแก้ไขรหัสผ่าน' . mysqli_error($db)) : set_info('แก้ไขรหัสผ่าน radcheck สำเร็จ');
    endif;
    redirect('user/change-password');
}

function do_validate($data) {
    $valid = true;
    $data = &$_POST;
    if (!preg_match('/[a-zA-Z0-9_]{5,}/', $data['username'])) {
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

