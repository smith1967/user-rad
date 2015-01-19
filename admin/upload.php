<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
?>

<?php
if (isset($_POST)) {
    $data = $_POST;
    // transfer data
    if (isset($data['tid'])) {
        //var_dump($data);
        //die();
        do_transfer($data);
    }
    /*     * *** delete data ****** */
    if (isset($data['did'])) {
        do_delete($data);
    }
}
?>
<script>
    $(document).ready(function() {
        $('input[type=file]').bootstrapFileInput();
        $('.file-inputs').bootstrapFileInput();
        /*
        $("#sel_gid").change(function() {
            $("#form_gid").submit();
        });
        */
    });

</script>

<div class="container">
    <div>
        <div>
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url() ?>">หน้าหลัก</a></li>
                <li><a href="<?php echo site_url('admin/index') ?>">ผู้ดูแลระบบ</a></li>
                <li class='active'>นำเข้าข้อมูล</li>
            </ol>
        </div>
        <div class='submenu'>
            <ul class="nav nav-pills">
                <li><a href="<?php echo site_url('admin/index') ?>">ผู้ดูแลระบบ</a></li>
                <li><a href="#">จัดการผู้ใช้</a></li>
                <li><a href="#">พักการใช้งาน</a></li>
                <li><a href="<?php echo site_url('admin/upload') ?>">นำเข้าข้อมูล</a></li>
                <li><a href="#">ดูข้อมูลรหัสผ่าน</a></li>
                <li><a href="#">รายชื่อผู้กำลังใช้งาน</a></li>
                <li><a href="#">ประวัติการใช้งาน</a></li>
            </ul>
        </div>
    </div>
    
<div class="page-header" style="margin-top: 0px;"><h4>นำเข้าข้อมูลผู้ใช้ใหม่จากโปรแกรม std</h4></div>
<?php
if (isset($_SESSION['err'])) {
    echo show_error($_SESSION['err']);
    unset($_SESSION['err']);
}
if (isset($_SESSION['info'])) {
    echo show_info($_SESSION['info']);
    unset($_SESSION['info']);
}
?> 

    <?php
    // check new user register        
    $sql = "SELECT register.gid,group_desc,count(id) num  FROM register LEFT JOIN group_config ON register.gid = group_config.gid WHERE register.comfirm = 'N' GROUP BY register.gid;";
//echo $sql."<br />";
    $rs = mysqli_query($db, $sql);
    if (mysqli_num_rows($rs) > 0) : 
        while ($row = mysqli_fetch_array($rs))
            echo "<div>จำนวน<strong>" . $row['group_desc'] . "</strong>ที่ลงทะเบียนใหม่ " . $row['num'] . " รายการ</div>";
        // select user group
        $sql = "SELECT gid,group_desc FROM group_config";
        ?>
    <div class="container">
        <form method="post" id="form_gid" role="form" >
            <div class="col-xs-6 col-md-2" style="margin: 2px 0 5px 0px">
                <select class="form-control" id="sel_gid" name="gid">
                    <option value="">-เลือกกลุ่มผู้ใช้-</option>
                    <?php
                    echo gen_option($sql, isset($_POST['gid']) ? $_POST['gid'] : "");
                    ?>
                </select>
            </div>
        </form>
    </div>
    <?php else: ?>
       <div class='alert-warning'><p style="padding: 5px 0 5px 10px">ไม่มีข้อมูลผู้ลงทะเบียนใหม่ครับ</p></div>
    <?php
    endif;
    ?>

    <?php
    // list new user by gid
    if (isset($_POST['gid'])) {
        $data = $_POST;
        $sql = "SELECT * FROM register WHERE comfirm LIKE 'N' AND gid = " . pq($data['gid']) . ";";
        //$sql = "SELECT * FROM register WHERE gid = ".pq($data['gid']).";";
        $rs = mysqli_query($db, $sql);
        if (mysqli_num_rows($rs) > 0) { 
    ?>
         <div class="table-responsive">
            <form action="" method="post" id="transfer_form">

                    <table class="table table-bordered table-hover">
                        <?php
                        $array = array(
                            'ลำดับ',
                            'ชื่อผู้ใช้',
                            'ชื่อ',
                            'นามสกุล',
                            'วันที่ลงทะเบียน',
                            'IP Address',
                            'แผนก/งาน',
                            'โอน',
                            'ลบ'
                        );
                        echo gen_thead($array);
                        $n = 1;
                        $i = 0;
                        while ($row = mysqli_fetch_array($rs)) {
                            //var_dump($row);
                            $array = array(
                                $n++,
                                $row['username'],
                                $row['fname'],
                                $row['lname'],
                                $row['created'],
                                $row['hostname'],
                                $row['department']
                            );
                            $s = gen_td($array);
                            $s .= '<td><input type="checkbox" class="tid" name="tid[' . $i++ . ']" id="tid"';  // <-- transfer data
                            $s .= ' value="' . $row['id'] . '" onclick=tidcheck(this) /></td>';
                            $s .= '<td><input type="checkbox" class="did" name="did[' . $i++ . ']" id="did"';  // <-- delete data
                            $s .= ' value="' . $row['id'] . '" onclick=didcheck(this) /></td>';
                            $s .= '</tr>';
                            echo $s;

                            //echo json_encode($row);
                        }

                        echo '</table>';
                        echo '<p><input type="submit" value="โอน/ลบ ข้อมูล"/></p>';
                        echo '</form>';
                        echo "</div>";
                    } else {
                        echo "<div class='alert-warning'><p>ยังไม่มีข้อมูลครับ</p></div>";
                    }
                }
                ?>        

          
</div> <!-- Main contianer -->
<?php

function do_transfer($data) {
    global $db;
    foreach ($data['tid'] as $k => $v) {
        /* --------ข้อมูลการลงทะเบียน----------- */
        $sql = "SELECT * FROM register WHERE id = " . pq($v) . ";";
        $rs = mysqli_query($db, $sql);
        $register = mysqli_fetch_array($rs);
        foreach ($register as $key => $value) {
            $$key = pq($value);
        }
        /* ---- อ่านค่าคอนฟิกของกลุ่ม ----------- */
        $sql = "SELECT * FROM group_config WHERE gid = $gid;";
        $rs = mysqli_query($db, $sql);
        $group_config = mysqli_fetch_array($rs);
        foreach ($group_config as $key => $value) {
            $$key = pq($value);
        }       
        /* --add attribute Password -- */
        $query = "INSERT INTO radcheck (UserName, Attribute, Op, Value)
                  SELECT * FROM (SELECT $username, 'Password', '==', $password) AS tmp
                  WHERE NOT EXISTS 
                  (SELECT username FROM radcheck WHERE username = $username AND Attribute = 'Password') LIMIT 1;";
        //$sql = "REPLACE INTO radcheck (UserName, Attribute, Op, Value) VALUES (" . pq($register['username']) . ", 'Password', '==', " . pq($register['password']) . ");";
        mysqli_query($db, $query);
        if (mysqli_affected_rows($db) > 0)
            $info[] = 'โอนย้ายข้อมูล Password เข้าสู่ตาราง radcheck จำนวน ' . mysqli_affected_rows($db) . ' เสร็จเรียบร้อย';
        else
            $err[] = "ไม่สามารถโอนข้อมูล Password เข้าสู่ตาราง radcheck ได้  : " . mysqli_error($db).$query;
        //  add attribute idle-timeout
        $query = "INSERT INTO radreply (UserName, Attribute, Op, Value)
                  SELECT * FROM (SELECT $username, 'Idle-Timeout', ':=', '900') AS tmp
                  WHERE NOT EXISTS 
                  (SELECT username FROM radreply WHERE username = $username AND Attribute = 'Idle-Timeout') LIMIT 1;";
        //$sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($register['username']) . ", 'Idle-Timeout', ':=', '900');";
        //echo $sql.'<br />';
        mysqli_query($db, $query);
        if (mysqli_affected_rows($db) > 0)
            $info[] = 'โอนย้ายข้อมูล Idle-timeout เข้าสู่ตาราง radreply จำนวน ' . mysqli_affected_rows($db) . ' เสร็จเรียบร้อย';
        else
            $err[] = "ไม่สามารถโอนข้อมูล Idle-timeout เข้าสู่ตาราง radreply ได้  : " . mysqli_error($db);

        $query = "INSERT INTO radreply (UserName, Attribute, Op, Value)
                  SELECT * FROM (SELECT $username, 'WISPr-Bandwidth-Max-Down', ':=',  $download) AS tmp
                  WHERE NOT EXISTS 
                  (SELECT username FROM radreply WHERE username = $username AND Attribute = 'WISPr-Bandwidth-Max-Down') LIMIT 1;";        
        //$sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($register['username']) . ", 'WISPr-Bandwidth-Max-Down', ':=', " . pq($group_config['download']) . ");";
        //echo $sql.'<br />';
        mysqli_query($db, $query);
        if (mysqli_affected_rows($db) > 0)
            $info[] = 'โอนย้ายข้อมูล Download เข้าสู่ตาราง radreply จำนวน ' . mysqli_affected_rows($db) . ' เสร็จเรียบร้อย';
        else
            $err[] = "ไม่สามารถโอนข้อมูล Download เข้าสู่ตาราง radreply ได้  : " . mysqli_error($db);

        $query = "INSERT INTO radreply (UserName, Attribute, Op, Value)
                  SELECT * FROM (SELECT $username, 'WISPr-Bandwidth-Max-Up', ':=',  $upload) AS tmp
                  WHERE NOT EXISTS 
                  (SELECT username FROM radreply WHERE username = $username AND Attribute = 'WISPr-Bandwidth-Max-Up') LIMIT 1;";        
        //$sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($register['username']) . ", 'WISPr-Bandwidth-Max-Up', ':=', " . pq($group_config['upload']) . ");";
        //echo $sql.'<br />';
        mysqli_query($db, $query);
        if (mysqli_affected_rows($db) > 0)
            $info[] = 'โอนย้ายข้อมูล Upload เข้าสู่ตาราง radreply จำนวน ' . mysqli_affected_rows($db) . ' เสร็จเรียบร้อย';
        else
            $err[] = "ไม่สามารถโอนข้อมูล  Upload เข้าสู่ตาราง radreply ได้  : " . mysqli_error($db);
        //---- UserGroup Info ---//
        $query = "INSERT INTO usergroup (UserName,GroupName)
                  SELECT * FROM (SELECT $username, $groupname) AS tmp
                  WHERE NOT EXISTS 
                  (SELECT username FROM usergroup WHERE username = $username) LIMIT 1;";        
        
        //$sql = "REPLACE INTO usergroup (UserName, GroupName) VALUES (" . pq($register['username']) . ", " . pq($group_config['groupname']) . ");";
        //echo $sql.'<br />';
        $rs = mysqli_query($db, $query);
        if (mysqli_affected_rows($db) > 0)
            $info[] = 'โอนย้ายข้อมูลเข้าสู่ตาราง usergroup จำนวน ' . mysqli_affected_rows($db) . ' เสร็จเรียบร้อย';
        else
            $err[] = "ไม่สามารถโอนข้อมูลเข้าสู่ตาราง usergroup ได้  : " . mysqli_error($db);
        //  transfer new data from tmp to users
        //$sql = "UPDATE `register` SET `comfirm` =  'Y' WHERE `register`.`username` IN (SELECT username FROM radcheck);";
        $sql = "UPDATE `register` SET `comfirm` =  'Y' WHERE `username` = $username;";
        mysqli_query($db, $sql);
        if (mysqli_affected_rows($db) > 0)
            $info[] = 'ปรับปรุงข้อมูลเข้าสู่ register จำนวน ' . mysqli_affected_rows($db) . ' เสร็จเรียบร้อย';
        else
            $err[] = "ไม่สามารถปรับปรุงข้อมูล register ได้  : " . mysqli_error($db);
        
        // insert register to user
        $query = "INSERT INTO users (username,password,fname,lname,groupname,pid)
                  SELECT username,password,fname,lname,$groupname,pid 
                  FROM register WHERE username = $username
                  AND NOT EXISTS (SELECT * FROM users WHERE username = $username)";
                      
        //$sql = "REPLACE INTO users (username,password,fname,lname,groupname,pid) SELECT register.username,register.password,register.fname,register.lname,register.gid,register.pid FROM `register` WHERE username NOT IN (SELECT username FROM users) AND `comfirm` =  'Y';";
        mysqli_query($db, $query);
        if (mysqli_affected_rows($db) > 0)
            $info[] = 'โอนย้ายข้อมูลเข้าสู่ตาราง users จำนวน ' . mysqli_affected_rows($db) . ' เสร็จเรียบร้อย';
        else
            $err[] = 'ไม่มีการโอนข้อมูลเข้าตาราง users '. mysqli_error($db). $query;
        if (count($err) > 0) {
            $_SESSION['err'] = $err; //print_r($row2);
        }
        $_SESSION['info'] = $info;        //}
    }
}
function do_delete($data){
    global $db;
    /*$glue = "','id=";
    $did = "'id=".implode($glue, $data['did'])."'";
    $query = "DELETE FROM ";
    
            /*
             * 
        foreach ($data as $key => $value) {
            $$key = pq($value); // clean data and quote
        }
         * 
         */
      
        $n = 0;
        foreach ($data['did'] as $v) {
            //echo $v."<br />";
            //$sql = "SELECT * FROM register WHERE id = " . pq($v);
           // $rs = mysqli_query($db, $sql);
           /*
            *  if ($rs) {
            
                $row = mysqli_fetch_array($rs);
                $sql = "DELETE FROM `users` WHERE username = " . pq($row['username']);
                mysqli_query($db, $sql);
            }
            * 
            */
            $sql = "DELETE FROM `register` WHERE `id` = " . pq($v);
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db)) {               
                //echo "<p> ลบข้อมูลจำนวน". ++$n. " รายการ</p>";
                //die();
                $n++;
            }
        }
        $info[] = "<p> ลบข้อมูลจำนวน $n รายการ </p>";
        $_SESSION['info'] = $info;        //}
}
?>