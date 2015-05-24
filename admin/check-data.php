f <?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ตรวจสอบข้อมูล";
$active = 'admin';
$subactive = 'check-data';
if (!isset($_GET['filename']))
    redirect('admin/file-manager');
is_admin('home/index');
?>
<?php
?>
<?php require_once INC_PATH . 'header.php'; ?>
<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <?php
    show_message();
    if (isset($_GET['action']) && $_GET['action'] == 'import' && $_GET['type'] == 'std') {
        $filename = UPLOAD_DIR . $_GET['filename'];
        do_transfer_std($filename);
    }
    if (isset($_GET['action']) && $_GET['action'] == 'import' && $_GET['type'] == 'users') {
        $filename = UPLOAD_DIR . $_GET['filename'];
        do_transfer_users($filename);
    }
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'check') {
            $filename = UPLOAD_DIR . $_GET['filename'];
            if (validate_std_file($filename)) {
                $importlink = site_url('admin/check-data') . '&action=import&type=std&filename=' . $_GET['filename'];
                echo '<div class="alert alert-success col-md-4">ข้อมูลแฟ้ม std ถูกต้อง <a href= ' . $importlink . '>โอนแฟ้มข้อมูล </a></div>';
            } elseif (validate_users_file($filename)) {
                $importlink = site_url('admin/check-data') . '&action=import&type=users&filename=' . $_GET['filename'];
                echo '<div class="alert alert-success col-md-4">ข้อมูลแฟ้มผู้ใช้งานถูกต้อง <a href= ' . $importlink . '>โอนแฟ้มข้อมูล </a></div>';
            } else {
                $uploadlink = site_url('admin/file-manager');
                echo '<div class="alert alert-warning col-md-4">ข้อมูลไม่ถูกต้องกลับไป <a href= ' . $uploadlink . '>จัดการแฟ้มข้อมูล </a></div>';
                //die("not valid");
            }
            }
        }
        ?>     
    </div>

</div> <!--End Main container -->
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function validate_std_file($filename) {
    $handle = fopen($filename, "r");
    //print_r(fgetcsv($file));
    $col_names = fgetcsv($handle);
    $valid = TRUE;
    // -- fields std
    $stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
    // check header csv
    foreach ($stdcol as $col) {
        if (!in_array($col, $col_names)) {
            $valid = FALSE;
        }
    }
    fclose($handle);
    return $valid;
}

function validate_users_file($filename) {
    $handle = fopen($filename, "r");
    //print_r(fgetcsv($file));
    $col_names = fgetcsv($handle);
    //var_dump($col_names);
    $valid = TRUE;
// -- table cols
    $dbcol = array('username', 'password', 'fname', 'lname', 'groupname');
    // check header csv
    foreach ($dbcol as $col) {
        if (!in_array($col, $col_names)) {
            $valid = FALSE;
        }
    }
    fclose($handle);
    return $valid;
}

function do_transfer_std($stdfile) {
    global $db;
// -- fields std
    $stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
// -- table cols
    $dbcol = array('std_id', 'pid', 'fname', 'lname', 'groupname');
    /* insert data to table tmp */
    $handle = fopen($stdfile, "r");
// get header column from file     
    $cols = fgetcsv($handle);
    $colindex = array();   // --- get index of array
    foreach ($stdcol as $value) {
        $colindex[] = array_search($value, $cols);
    }
    $stdcharset = "";
    while (!feof($handle)) {
        $str = fgetcsv($handle);
        $str_comma = implode(",", $str);
        if (empty($stdcharset))
            $stdcharset = mb_detect_encoding($str_comma, "UTF-8", TRUE) ? "UTF-8" : "TIS-620";
        $line = ($stdcharset == 'TIS-620') ? iconv("tis-620", "utf-8", $str_comma) : $line = $str_comma;
        //die($line);
        if (strlen($line)) {
            $row = array();
            $row = explode(",", $line);
            $val = array();
            foreach ($colindex as $v) {
                $val[] = pq($row[$v]);
            }
            $arr[] = '(' . implode(",", $val) . ')';    //  set of data array((1,2,3),(4,5,6),..);
        }
    }
    fclose($handle);
    $values = implode(",", $arr);                   // -- group set data  (1,2,3),(4,5,6),...
    $cols = "(" . implode(",", $dbcol) . ")";
    $sql = "TRUNCATE TABLE `stdtemp`";
    mysqli_query($db, $sql);
    $query = "INSERT INTO stdtemp " . $cols . " VALUES " . $values;
    //die($query);
    mysqli_query($db, $query);
    if (mysqli_affected_rows($db)) {
        set_info('โอนข้อมูลจำนวน ' . mysqli_affected_rows($db) . ' ใส่ตารางชั่วคราว');
        //redirect('admin/file-manager');
    } else {
        set_err("การโอนข้อมูลใส่ตารางชั่วคราวผิดพลาด : " . mysqli_error($db));
        //die();
    }
    redirect('admin/file-manager');
}

function do_transfer_users($usersfile) {
    global $db;
    $stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
// -- table cols
    $dbcol = array('username', 'password', 'fname', 'lname', 'groupname');
    /* insert data to table tmp */
    $handle = fopen($usersfile, "r");
// get header column from file     
    $cols = fgetcsv($handle);
    $colindex = array();   // --- get index of array
    foreach ($dbcol as $value) {
        $colindex[] = array_search($value, $cols);
    }
    $stdcharset = "";
    while (!feof($handle)) {
        $str = fgetcsv($handle);
        $str_comma = implode(",", $str);
        if (empty($stdcharset))
            $stdcharset = mb_detect_encoding($str_comma, "UTF-8", TRUE) ? "UTF-8" : "TIS-620";
        $line = ($stdcharset == 'TIS-620') ? iconv("tis-620", "utf-8", $str_comma) : $line = $str_comma;
        //die($line);
        if (strlen($line)) {
            $row = array();
            $row = explode(",", $line);
            $val = array();
            foreach ($colindex as $v) {
                $val[] = pq($row[$v]);
            }
            $arr[] = '(' . implode(",", $val) . ')';    //  set of data array((1,2,3),(4,5,6),..);
        }
    }
    fclose($handle);
    $values = implode(",", $arr);                   // -- group set data  (1,2,3),(4,5,6),...
    $cols = "(" . implode(",", $dbcol) . ")";
    $sql = "TRUNCATE TABLE `users_temp`";
    mysqli_query($db, $sql);
    $query = "INSERT INTO users_temp " . $cols . " VALUES " . $values;
   // die($query);
    mysqli_query($db, $query);
    if (mysqli_affected_rows($db)) {
        set_info('โอนข้อมูลจำนวน ' . mysqli_affected_rows($db) . ' ใส่ตารางชั่วคราว');
        //redirect('admin/file-manager');
    } else {
        set_err("การโอนข้อมูลใส่ตารางชั่วคราวผิดพลาด : " . mysqli_error($db));
        //die();
    }
    redirect('admin/file-manager');
}
