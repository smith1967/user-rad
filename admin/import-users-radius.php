<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "โอนข้อมูล users เข้าระบบ radius";
$active = 'admin';
$subactive = 'import-users-radius';
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
    if (isset($_POST['submit']) && isset($_POST['groupname'])) {
        //die(var_dump($_POST));
        do_import($_POST['groupname'], $_POST['optconf']);
    }
    ?>     

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <label class="panel-title">กลุ่มผู้ใช้งาน/ดาวน์โหลด/อัพโหลด</label>
            </div>
            <div class="panel-body">
                <form method="post">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php
                            $configs = getConfigs();
                            foreach ($configs as $config) :
                                ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optconf" id="optionsRadios1" value="<?php echo $config['gid'] ?>" <?php echo $config['gid'] == 4 ? 'checked="checked"' : '' ?>>
                                        <?php echo $config['group_desc'] ?>
                                        <span class="badge"><?php echo $config['download'] ?></span><span class="badge"><?php echo $config['upload'] ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <label for="sel1">เลือกกลุ่มผู้ใช้:</label>
                            <select class="form-control" id="sel2" name="groupname">
                                <?php $groups = getGroupUsersTemp(); ?>
                                <?php foreach ($groups as $group) : ?>
                                    <option data-subtext="<?php echo $group['total'] ?>" ><?php echo $group['groupname']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default" name="submit">โอนข้อมูล</button>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
    <div class="table-responsive col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <label class="panel-title">ข้อมูลในระบบ radius</label>
            </div>
            <div class="panel-body">       
                <table class="table table-condensed table-striped" >
                    <thead><th><span class="badge">ชั้นปี</span><span class="badge">จำนวน</span></th><th>ลบข้อมูล</th></thead>
                    <?php
                    $groups = getGroupUsers();
                    foreach ($groups as $group) :
                        ?>
                        <tr>
                            <td><span class="badge"> <?php echo $group['groupname']; ?></span> <span class="badge"><?php echo $group['total'] ?></span></td>
                            <td> 
                                <a href="<?php echo site_url('admin/import-users-radius') . '&action=delete&group=' . $group['groupname']; ?>" class="delete">ลบ</a>
                                <a href="<?php echo site_url('admin/list-user') . '&action=list-users&group=' . $group['groupname']; ?>" >ดูรายชื่อ</a>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </table>
            </div>
        </div>

    </div>   
</div>  
</div> <!--End Main container -->
<script>
    $('.delete').click(function () {
        if (!confirm('ยืนยันลบข้อมูล')) {
            return false;
        }
    });
</script>
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function getTotal() {
    global $db;
    $sql = "SELECT COUNT('username') AS total FROM users_temp";
    $rs = mysqli_query($db, $sql);
    $total = mysqli_fetch_array($rs);
    return $total;
}

function do_delete($val) {
    global $db;
    if (empty($val)) {
        set_err("ไม่มีกลุ่มข้อมูล");
        redirect('admin/import-std-radius');
    }
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
    $sql = "DELETE FROM usergroup WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล usergroup จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    $sql = "DELETE FROM radreply WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล radreply จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    $sql = "DELETE FROM radacct WHERE username LIKE " . pq($val);
    mysqli_query($db, $sql);
    set_info("<p> ลบข้อมูล radacct จำนวน " . mysqli_affected_rows($db) . " รายการ </p>");
    redirect('admin/import-std-radius');
}

function getGroupUsers() {
    global $db;
    $groups = array();
    $sql = "SELECT DISTINCT(groupname),COUNT(groupname) as total FROM users WHERE groupname BETWEEN  1 AND 100    GROUP BY groupname;";
    $result = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $groups[] = $row;
    }
    return $groups;
}

function getGroupUsersTemp() {
    global $db;
    $groups = array();
    $sql = "SELECT DISTINCT(groupname),COUNT(groupname) as total  FROM users_temp GROUP BY groupname;";
    //$sql = "SELECT DISTINCT(substring(std_id,1,4)) as grp,COUNT(substring(std_id,1,4)) as total  FROM stdtemp WHERE std_id REGEXP '^[0-9].{4}' GROUP BY substring(std_id,1,4) ORDER BY grp DESC;";
    $result = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $groups[] = $row;
    }
    return $groups;
}

function do_import($groupname, $gid) {
    global $db;
    if (empty($gid)) {
        set_err("กรุณาเลือกกลุ่มผู้ใช้งาน");
        redirect('admin/import-users-radius');
    }
    $cfg = getConfig($gid);
    /* tranfer from tmp to radius */
    $sql = "SELECT * FROM users_temp WHERE groupname LIKE '" . $groupname . "%' AND username NOT IN (SELECT UserName FROM radcheck)";
    $result = mysqli_query($db, $sql);
//echo "<table>";
    if (mysqli_num_rows($result) > 0) {
        while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $sql = "REPLACE INTO radcheck (UserName, Attribute, Op, Value) VALUES (" . pq($line['username']) . ", 'Password', '==', " . pq($line['password']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radcheck ผิดพลาด : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radcheck (UserName, Attribute, Op, Value) VALUES (" . pq($line['username']) . ", 'Simultaneous-Use', ':=', " . pq(1) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radcheck ผิดพลาด : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['username']) . ", 'Idle-Timeout', ':=', '900');";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูล Idle-Timeout ผิดพลาด : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['username']) . ", 'WISPr-Bandwidth-Max-Down', ':=', " . pq($cfg['download']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radreply ผิดพลาด  : " . mysqli_error($db));
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['username']) . ", 'WISPr-Bandwidth-Max-Up', ':=', " . pq($cfg['upload']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง radreply ผิดพลาด  : " . mysqli_error($db));
            }
            //---- UserGroup Info ---//
            $sql = "REPLACE INTO usergroup (UserName, GroupName) VALUES (" . pq($line['username']) . ", " . pq($cfg['groupname']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                set_err("การเพิ่มข้อมูลเข้าตาราง usergroup ผิดพลาด  : " . mysqli_error($db));
            }
        }
        set_info("โอนข้อมูลเข้าระบบ radius จำนวน " . mysqli_num_rows($result) . " รายการ ");
    } else {
        set_err('ไม่มีการโอนข้อมูลเข้าตาราง radius เนื่องจากมีข้อมูลในระบบอยู่แล้ว');
    }
//  transfer new data from tmp to users
    $sql = "REPLACE INTO users (username,password,fname,lname,groupname) 
    SELECT users_temp.username,users_temp.password,users_temp.fname,users_temp.lname,users_temp.groupname 
    FROM 
    `users_temp` WHERE groupname LIKE '" . $groupname . "%' AND username NOT IN (SELECT username FROM users);";
    mysqli_query($db, $sql);
    if (mysqli_affected_rows($db) < 1) {
        set_err("การเพิ่มข้อมูลเข้าตาราง users ผิดพลาด  : " . mysqli_error($db));
        ;
        //redirect('form.php');
    } else {
        set_info('โอนข้อมูลเข้าตาราง users จำนวน ' . mysqli_affected_rows($db) . ' รายการ');
    }
    redirect('admin/import-users-radius');
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
