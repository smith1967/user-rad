<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "โอนข้อมูล std เข้าระบบ radius";
$active = 'admin';
$subactive = 'import-std-radius';
is_admin('home/index');
?>
<?php require_once INC_PATH . 'header.php'; ?>
<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <?php
    show_message();
    if (isset($_GET['action']) && isset($_GET['group'])) {
        do_delete($_GET['group']);
    }
    if (isset($_POST['submit']) && isset($_POST['grp'])) {
        do_import($_POST['grp'], $_POST['optconf']);
    }
    ?>     

    <div class="col-md-6">
        <form method="post">
            <div class="col-md-6">
                <div class="form-group">
                    <label>กลุ่มผู้ใช้งาน/ดาวน์โหลด/อัพโหลด</label>
                    <?php
                    $configs = getConfigs();
                    foreach ($configs as $config) :
                        ?>
                        <div class="radio">
                            <label>
                                <input type="radio" name="optconf" id="optionsRadios1" value="<?php echo $config['gid'] ?>" ><?php echo $config['group_desc'] ?>
                                <span class="badge"><?php echo $config['download'] ?></span><span class="badge"><?php echo $config['upload'] ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <label for="sel1">เลือกกลุ่มนักศึกษา:</label>
                    <select class="form-control" id="sel2" name="grp">
                        <?php $groups = getGroupStd(); ?>
                        <?php while ($row = mysqli_fetch_array($groups)) : ?>
                            <option data-subtext="<?php echo $row['total'] ?>"><?php echo $row['grp']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-default" name="submit">โอนข้อมูล</button>
                </div>
            </div>
        </form> 
    </div>
    <div class="table-responsive col-md-6">
        <table class="table table-condensed table-striped" >
            <thead><th>กลุ่มชั้นปี/จำนวน</th><th>ลบข้อมูล</th></thead>
            <?php
            $groups = getGroups();
            foreach ($groups as $group) :
                ?>
                <tr>
                    <td> <?php echo $group['grp']; ?> <span class="badge"><?php echo $group['total'] ?></span></td>
                    <td> <a href="<?php echo site_url('admin/import-std-radius') . '&action=delete&group=' . $row['grp']; ?>" class="delete">ลบ</a></td>
                </tr>
                <?php
            endforeach;
            ?>
        </table>
    </div>   
</div>  
</div> <!--End Main container -->
<script>
    $('.delete').click(function() {
        if (!confirm('ยืนยันลบข้อมูล')) {
            return false;
        }
    });
</script>
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function getTotal() {
    global $db;
    $sql = "SELECT COUNT('std_id') AS total FROM stdtemp";
    $rs = mysqli_query($db, $sql);
    $total = mysqli_fetch_array($rs);
    return $total;
}

function do_delete($val) {
    global $db;
    $val = $val . '%';
    $sql = "DELETE FROM radcheck WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล radcheck จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    $sql = "DELETE FROM register WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล register จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    $sql = "DELETE FROM users WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล users จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    $sql = "DELETE FROM radreply WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล radreply จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    $sql = "DELETE FROM radacct WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล radacct จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    redirect('admin/import-std-radius');
}

function getGroups() {
    global $db;
    $groups = array();
    $sql = "SELECT DISTINCT(substring(username,1,4)) as grp,COUNT(substring(username,1,4)) as total  FROM users WHERE username REGEXP '^[0-9].{4}' GROUP BY substring(username,1,4) ORDER BY grp DESC;";
    $result = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $groups[] = $row;
    }
    return $groups;
}

function getGroupStd() {
    global $db;
    $sql = "SELECT DISTINCT(substring(std_id,1,4)) as grp,COUNT(substring(std_id,1,4)) as total  FROM stdtemp WHERE std_id REGEXP '^[0-9].{4}' GROUP BY substring(std_id,1,4) ORDER BY grp DESC;";
    $result = mysqli_query($db, $sql);
    return $result;
}

function do_import($grp, $gid) {
    global $db;
    if (empty($gid)) {
        set_err("กรุณาเลือกกลุ่มผู้ใช้งาน");
        redirect('admin/import-std-radius');
    }
    $cfg = getConfig($gid);
    /* tranfer from tmp to radius */
    $sql = "SELECT * FROM stdtemp WHERE std_id LIKE '" . $grp . "%' AND std_id NOT IN (SELECT UserName FROM radcheck)";
    $result = mysqli_query($db, $sql);
//echo "<table>";
    if (mysqli_num_rows($result) > 0) {
        while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $sql = "REPLACE INTO radcheck (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'Password', '==', " . pq($line['pid']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radcheck ผิดพลาด : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radcheck (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'Simultaneous-Use', ':=', " . pq(1) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radcheck ผิดพลาด : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'Idle-Timeout', ':=', '900');";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูล Idle-Timeout ผิดพลาด : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'WISPr-Bandwidth-Max-Down', ':=', " . pq($cfg['download']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radreply ผิดพลาด  : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'WISPr-Bandwidth-Max-Up', ':=', " . pq($cfg['upload']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radreply ผิดพลาด  : " . mysqli_error($db));
            }
            //---- UserGroup Info ---//
            $sql = "REPLACE INTO usergroup (UserName, GroupName) VALUES (" . pq($line['std_id']) . ", " . pq($cfg['groupname']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง usergroup ผิดพลาด  : " . mysqli_error($db));
            }
        }
        set_info("เพิ่มข้อมูลเข้า radius จำนวน " . mysqli_num_rows($result) . " รายการ ");
    } else {
        set_err('ไม่มีการโอนข้อมูลเข้าตาราง radius เนื่องจากมีข้อมูลในระบบอยู่แล้ว');
    }
//  transfer new data from tmp to users
    $sql = "REPLACE INTO users (username,password,fname,lname,groupname,pid) 
    SELECT stdtemp.std_id,stdtemp.pid,stdtemp.fname,stdtemp.lname,stdtemp.groupname,stdtemp.pid 
    FROM 
    `stdtemp` WHERE std_id LIKE '" . $grp . "%' AND std_id NOT IN (SELECT username FROM users);";
    mysqli_query($db, $sql);
    if (mysqli_affected_rows($db) < 1) {
        set_err("การเพิ่มข้อมูลเข้าตาราง users ผิดพลาด  : " . mysqli_error($db));
        ;
        //redirect('form.php');
    } else {
        set_info('โอนข้อมูลเข้าตาราง users จำนวน ' . mysqli_affected_rows($db) . ' แถว');
    }
    redirect('admin/import-std-radius');
}

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

function getConfig($gid) {
    global $db;
    $sql = "SELECT * FROM group_config WHERE gid = " . pq($gid) . ";";
    $rs = mysqli_query($db, $sql);
    $config = mysqli_fetch_array($rs);
    return $config;
}
