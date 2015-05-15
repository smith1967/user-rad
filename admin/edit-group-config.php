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
        if (isset($data['gid'])) {
            do_update();  // ไม่มี error บันทึกข้อมูล
        } else {
            do_insert();
        }
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    do_delete($_GET['gid']);
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
    <?php show_message(); ?>
    <div class="panel panel-default">
        <div class="panel-heading">แก้ไขข้อมูลกลุ่มผู้ใช้</div>
        <div class="panel-body">
            <div class="table table-responsive">
                <table class="table-striped table-condensed">
                    <tr><th>ชื่อกลุ่ม(Eng)</th><th>ชื่อ(Thai)</th><th>ดาวน์โหลด</th><th>อัพโหลด</th><th>กระทำการ</th></tr>
                    <?php
                    $configs = getConfigs();
                    foreach ($configs as $config) :
                        $delete_url = site_url('admin/edit-group-config') . '&action=delete&gid=' . $config['gid'];
                        ?>                     
                        <tr>
                            <td><form method="post"><input type="hidden" value="<?php echo $config['gid'] ?>" name="gid"><input type="text" class="form-control input-sm" name="groupname" value="<?php echo $config['groupname'] ?>"</td>
                                    <td><input type="text" class="form-control input-sm" name="group_desc" value="<?php echo $config['group_desc'] ?>"</td>
                                    <td><input type="text" class="form-control input-sm" name="download" value="<?php echo $config['download'] ?>"</td>
                                    <td><input type="text" class="form-control input-sm" name="upload" value="<?php echo $config['upload'] ?>"</td>
                                    <td class="text-center">
                                        <button type="submit" class="btn btn-sm btn-warning" name="submit">แก้ไข</button></form>
                                <a class="btn btn-danger btn-sm" href="<?php echo $delete_url; ?>" role="button">ลบข้อมูล</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><form method="post"><input type="text" class="form-control input-sm" name="groupname" value=""</td>
                                <td><input type="text" class="form-control input-sm" name="group_desc" value=""</td>
                                <td><input type="text" class="form-control input-sm" name="download" value=""</td>
                                <td><input type="text" class="form-control input-sm" name="upload" value=""</td>
                                <td class="text-center"><button type="submit" class="btn btn-sm btn-primary" name="submit">บันทึก</button></form></td>
                    </tr>

                </table>
            </div>    
        </div>
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
    $query = "UPDATE `group_config` SET `groupname` = " . pq($data['groupname']) . ", `group_desc` = " . pq($data['group_desc']) . ", `upload` = " . pq($data['upload']) . ", `download` = " . pq($data['download']) . " WHERE `gid` = " . pq($data['gid']) . ";";
    $result = mysqli_query($db, $query);
    if (mysqli_affected_rows($db) == 0) {
        set_err('ไม่สามารถแก้ไขข้อมูล');
    } else {
        set_info('แก้ไขข้อมูลสำเร็จ');
    }
    redirect('admin/edit-group-config');
}

function do_insert() {
    global $db;
    $data = &$_POST;
    $query = "INSERT INTO group_config (groupname, group_desc, upload, download) VALUES (".pq($data['groupname']).", ".pq($data['group_desc']).", ".pq($data['upload']).", ".pq($data['download']).");";
    mysqli_query($db, $query);
    if (mysqli_affected_rows($db) > 0) {
        set_info('เพิ่มข้อมูลสำเร็จ');
    }else{
        set_err('ไม่สามารถเพิ่มข้อมูล '.  mysqli_error($db));
    }
    redirect('admin/edit-group-config');
}


function do_delete($gid) {
    global $db;
    if (empty($gid)) {
        set_err('ค่าพารามิเตอร์ไม่ถูกต้อง');
        redirect('admin/edit-group-config');
    }
    $query = "DELETE FROM group_config WHERE gid =" . pq($gid);
    mysqli_query($db, $query);
    if (mysqli_affected_rows($db)) {
        set_info('ลบข้อมูลสำเร็จ');
    }
    redirect('admin/edit-group-config');
}

function do_validate($data) {
    $valid = true;
    $data = &$_POST;
    if (!preg_match('/[a-zA-Z0-9_]{5,}/', $data['groupname'])) {
        set_err('ชื่อผู้ใช้ต้องเป็นตัวเลขหรือตัวอักษรภาษาอังกฤษ ความยาวไม่ต่ำกว่า 5 ตัวอักษร');
        $valid = false;
    }
    if (!preg_match('/[0-9]{1,}/', $data['download'])) {
        set_err('ข้อมูลดาวน์โหลดต้องเป็นตัวเลข');
        $valid = false;
    }
    if (!preg_match('/[0-9]{1,}/', $data['upload'])) {
        set_err('ข้อมูลอัพโหลดต้องเป็นตัวเลข');
        $valid = false;
    }
    return $valid;
}
?>

