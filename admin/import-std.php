f <?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "นำเข้าข้อมูล";
$active = 'admin';
$subactive = 'import-std';
is_admin('home/index');
?>
<?php
$err = array();
$info = array();
// -- fields std
$stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
// -- table cols
$dbcol = array('std_id', 'pid', 'fname', 'lname', 'groupname');
// check submit
if (isset($_POST['submit'])) :
    if (count($err) > 0) {
        $_SESSION['err'] = $err;
        redirect('admin/import-std');
    }


    /* insert data to table tmp */
//$handle = fopen($stdfile, "r");
    $lines = file($stdfile);
    $rows = array();
    foreach ($lines as $line_num => $line) {    //-- get line from array
        if ($line_num == 0) {
            $cols = explode(",", $line);        // --- get header from file;
        } else {
            //$line = tis2utf($line);
            $rows[] = tis2utf($line);  // -- put data to array
        }
    }
    /*
      echo '<pre>';
      print_r ($rows);
      echo '</pre>';
      unset($row);
      die();
     */
    $colindex = array();   // --- get index of array
    for ($i = 0; $i < count($stdcol); $i++) {
        for ($j = 0; $j < count($cols); $j++) {
            if ($stdcol[$i] == $cols[$j]) {
                $colindex[] = $j;
            }
        }
    }
//echo '<pre>';
//print_r ($colindex);
//echo '</pre>';
//unset($arr);
//unset ($rows);//var_dump($colindex);
//die();      //var_dump($arr);
// get data to array
    foreach ($rows as $value) {
        $row = explode(",", $value);
        $val = array();
        foreach ($colindex as $v) {
            $val[] = pq($row[$v]);
        }
        $arr[] = '(' . implode(",", $val) . ')';    //  set of data array((1,2,3),(4,5,6),..);
    }

    $values = implode(",", $arr);                   // -- group set data  (1,2,3),(4,5,6),...
    $col = "(" . implode(",", $dbcol) . ")";
    $sql = "TRUNCATE TABLE `stdtemp`";
    mysqli_query($db, $sql);
    $query = "INSERT INTO stdtemp " . $col . " VALUES " . $values;
//echo  $query;
//die();
    mysqli_query($db, $query);
//or die("ไม่สามารถโอนข้อมูลเข้าตาราง " . mysqli_error());
//die();
//}else {  //----------------- linux ---------------//
    /*
      $tmp = fgetcsv($handle, 0, ",");
      $col = "(@" . implode(",@", $tmp) . ")";  // -- create col for load data
      $n = 0;
      // -- check cols match
      foreach ($stdcol as $a) {
      foreach ($tmp as $v) {
      if ($a == $v) {
      $n++;
      }
      }
      }
      if ($n != count($dbcol)) {
      $_SESSION['msg'] = "จำนวน fields ไม่ถูกต้องกรุณาตรวจสอบอีกครั้ง";
      redirect('form.php');
      die();
      }
      // -- creat set for load data
      foreach ($dbcol as $k => $a) {
      foreach ($stdcol as $j => $v) {
      if ($k == $j)
      $s[] = $a . '=@' . $v;
      }
      }
      $set = implode(",", $s);
      //---
      $sql = "TRUNCATE TABLE `stdtemp`";
      mysql_query($sql);
      //mysql_query("SET NAMES latin1");
      $sql = "LOAD DATA INFILE " . pq(str_replace("\\", "/", $stdfile));
      $sql .= " INTO TABLE stdtemp";
      //$sql .= " CHARSET latin1";
      $sql .= " FIELDS TERMINATED BY ','";
      //$sql .= ' LINES TERMINATED BY "\r\b"';
      $sql .= " IGNORE 1 LINES " . $col . " SET " . $set . ";";
      //echo $sql;
      //die();
      $rs = mysql_query($sql);
      }
     * 
     */
    if (mysqli_affected_rows($db)) {
        $info[] = '<p><h4>โอนข้อมูลจำนวน ' . mysqli_affected_rows($db) . ' ใส่ตารางชั่วคราว<h4><p>';
        //redirect('form.php', 3);
    } else {
        $_SESSION['err'][] = "การโอนข้อมูลใส่ตารางชั่วคราวผิดพลาด : " . mysqli_error($db);
        redirect('admin/std-import');
        die();
    }


//$_SESSION['msg'] = $col;
//redirect('form.php');
//die();
//  delete data in stdtemp   //





    /* ---- อ่านค่าคอนฟิกของกลุ่ม 4=นักเรียน ----------- */

    $sql = "SELECT * FROM group_config WHERE gid = 4 ;";
    $rs = mysql_query($sql);
    $row = mysql_fetch_array($rs);
    $cfg['upload'] = $row['upload'];
    $cfg['download'] = $row['download'];
    $cfg['groupname'] = $row['groupname'];

    /* ------------------------ */


    /* tranfer from tmp to radius */
    $sql = "SELECT * FROM stdtemp WHERE std_id NOT IN (SELECT UserName FROM radcheck)";
    $result = mysqli_query($db, $sql);
//echo "<table>";
    if (mysqli_num_rows($result) > 0) {
        $count = 0;
        while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $sql = "REPLACE INTO radcheck (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'Password', '==', " . pq($line['pid']) . ");";
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                $err[] = "การเพิ่มข้อมูลเข้าตาราง radcheck ผิดพลาด : " . mysqli_error($db);
                //redirect('admin/import-std');
            }
            /*
              $sql = "REPLACE INTO radcheck (UserName, Attribute, Op, Value) VALUES (".pq($line['std_id']).", 'Simultaneous-Use', ':=', ".pq(1).");";
              $rs = mysql_query($sql);
              if(!$rs){
              $_SESSION['msg'] = "ไม่สามารถโอนข้อมูลเข้าสู่ตารางผู้ใช้งานได้  : ".mysqli_error($db);
              redirect('transfer_form.php');
              }
             */

            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'Idle-Timeout', ':=', '900');";
            //echo $sql.'<br />';
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                $err[] = "การเพิ่มข้อมูล Idle-Timeout ผิดพลาด : " . mysqli_error($db);
                //redirect('form.php');
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'WISPr-Bandwidth-Max-Down', ':=', " . pq($cfg['download']) . ");";
            //echo $sql.'<br />';
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                $err[] = "การเพิ่มข้อมูลเข้าตาราง radreply ผิดพลาด  : " . mysqli_error($db);
            }
            $sql = "REPLACE INTO radreply (UserName, Attribute, Op, Value) VALUES (" . pq($line['std_id']) . ", 'WISPr-Bandwidth-Max-Up', ':=', " . pq($cfg['upload']) . ");";
            //echo $sql.'<br />';
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                $err[] = "การเพิ่มข้อมูลเข้าตาราง radreply ผิดพลาด  : " . mysqli_error($db);
            }
            //---- UserGroup Info ---//
            $sql = "REPLACE INTO usergroup (UserName, GroupName) VALUES (" . pq($line['std_id']) . ", 'student');";
            //echo $sql.'<br />';
            mysqli_query($db, $sql);
            if (mysqli_affected_rows($db) < 1) {
                $err[] = "การเพิ่มข้อมูลเข้าตาราง usergroup ผิดพลาด  : " . mysqli_error($db);
            }
            $count++;
        }
    } else {
        $info[] = '<h4>ไม่มีการโอนข้อมูลเข้าตาราง radius เนื่องจากมีข้อมูลในระบบอยู่แล้ว<h4>';
    }
//  transfer new data from tmp to users
    $sql = "REPLACE INTO users (username,password,fname,lname,groupname,pid) 
    SELECT stdtemp.std_id,stdtemp.pid,stdtemp.fname,stdtemp.lname,stdtemp.groupname,stdtemp.pid 
    FROM 
    `stdtemp` WHERE std_id NOT IN (SELECT username FROM users);";
    mysqli_query($db, $sql);
    if (mysqli_affected_rows($db) < 1) {
        $err[] = "การเพิ่มข้อมูลเข้าตาราง users ผิดพลาด  : " . mysqli_error($db);
        //redirect('form.php');
    } else {
        $info[] = '<h4>โอนข้อมูลเข้าตาราง users จำนวน ' . mysqli_affected_rows($db) . ' แถว<h4>';
    }
    if (count($err) > 0) {
        $_SESSION['err'][] = $err;
    }
    if (count($info) > 0) {
        $_SESSION['info'][] = $info;
    }
endif;
?>
<?php require_once INC_PATH . 'header.php'; ?>
<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <?php
    show_message();
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'del') {
            $filename = UPLOAD_DIR . $_GET['filename'];
            if (is_file($filename))
                unlink($filename);
            else
                $_SESSION['err'][] = 'ไม่สามารถลบไฟล์ ' . $filename;
        }
        if ($_GET['action'] == 'import') {
            $filename = UPLOAD_DIR . $_GET['filename'];
            $file = fopen($filename, "r");
            echo "<div>";
            //print_r(fgetcsv($file));
            $str = fgetcsv($file);
            $i = 0;
            $status = TRUE;
            // check header csv
            foreach ($stdcol as $col) {
                if (in_array($col, $str)) {
                    echo $col . ' <span class="glyphicon glyphicon-ok"></span><br />';
                } else {
                    echo $col . ' <span class="glyphicon glyphicon-remove"></span><br />';
                    $status = FALSE;
                }
            }
            /*
              if (is_array($str)) {
              $charset = mb_detect_encoding($str[1], "utf-8") ? "utf-8" : "tis-620";
              if ($charset == "utf-8") {
              foreach ($str as $value) {
              echo $i++ . " :  " . $value . "<br />";
              }
              } else {
              foreach ($str as $value) {
              // if (!mb_detect_encoding($value,"utf-8")){
              $value = iconv("tis-620", "utf-8", $value);
              // }
              echo $i++ . " :  " . $value . "<br />";
              }
              }

              }
             * 
             */
            echo "</div>";
            fclose($file);
        }
    }
    ?>     
    <div class="table-responsive col-md-4">
        <table class="table" >
            <thead><th>ชื่อไฟล์</th><th>โอนข้อมูล</th><th>ลบไฟล์</th></thead>

            <?php
            if ($handle = opendir(UPLOAD_DIR)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        ?>
                        <tr>
                            <td> <?php echo "$entry\n"; ?></td>
                            <?php
                            $unlink = site_url('admin/import-std') . '&action=del&filename=' . $entry;
                            $trlink = site_url('admin/import-std') . '&action=import&filename=' . $entry;
                            ?>
                            <td class="text-center"><a href="<?php echo $trlink ?>"><span class="glyphicon glyphicon-ok"></span></a></td>
                            <td class="text-center"><a href="<?php echo $unlink ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
                        </tr>
                        <?php
                    }
                }
                closedir($handle);
            }
            ?>
        </table>
    </div>  
</div> <!--End Main container -->
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function do_transfer($stdfile) {
    global $db;
// -- fields std
    $stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
// -- table cols
    $dbcol = array('std_id', 'pid', 'fname', 'lname', 'groupname');
    /* insert data to table tmp */
//$handle = fopen($stdfile, "r");
    $lines = file($stdfile);
    $rows = array();
    foreach ($lines as $line_num => $line) {    //-- get line from array
        if ($line_num == 0) {
            $cols = explode(",", $line);        // --- get header from file;
        } else {
            //$line = tis2utf($line);
            $rows[] = tis2utf($line);  // -- put data to array
        }
    }

    $colindex = array();   // --- get index of array
    for ($i = 0; $i < count($stdcol); $i++) {
        for ($j = 0; $j < count($cols); $j++) {
            if ($stdcol[$i] == $cols[$j]) {
                $colindex[] = $j;
            }
        }
    }
// get data to array
    foreach ($rows as $value) {
        $row = explode(",", $value);
        $val = array();
        foreach ($colindex as $v) {
            $val[] = pq($row[$v]);
        }
        $arr[] = '(' . implode(",", $val) . ')';    //  set of data array((1,2,3),(4,5,6),..);
    }

    $values = implode(",", $arr);                   // -- group set data  (1,2,3),(4,5,6),...
    $cols = "(" . implode(",", $dbcol) . ")";
    $sql = "TRUNCATE TABLE `stdtemp`";
    mysqli_query($db, $sql);
    $query = "INSERT INTO stdtemp " . $cols . " VALUES " . $values;

    mysqli_query($db, $query);
    if (mysqli_affected_rows($db)) {
        $info[] = '<p><h4>โอนข้อมูลจำนวน ' . mysqli_affected_rows($db) . ' ใส่ตารางชั่วคราว<h4><p>';
        //redirect('form.php', 3);
    } else {
        $_SESSION['err'][] = "การโอนข้อมูลใส่ตารางชั่วคราวผิดพลาด : " . mysqli_error($db);
        //redirect('admin/std-import');
        //die();
    }
    if (count($err) > 0) {
        $_SESSION['err'][] = $err;
    }
    if (count($info) > 0) {
        $_SESSION['info'][] = $info;
    }
}
