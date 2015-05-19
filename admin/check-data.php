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
// -- fields std
$stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
// -- table cols
$dbcol = array('std_id', 'pid', 'fname', 'lname', 'groupname');
?>
<?php require_once INC_PATH . 'header.php'; ?>
<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <?php
    show_message();
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'import') {
            $filename = UPLOAD_DIR . $_GET['filename'];
            do_transfer($filename);
        }
    }
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'check') {
            $filename = UPLOAD_DIR . $_GET['filename'];
            $handle = fopen($filename, "r");
            //print_r(fgetcsv($file));
            $str = fgetcsv($handle);
            $i = 0;
            $status = TRUE;
            // check header csv
            ?>
            <div class="table-responsive col-md-4">
                <table class="table">
                    <thead>
                    <th>คอลัมน์</th><th>สถานะ</th>
                    </thead>
                    <?php
                    foreach ($stdcol as $col) {
                        if (in_array($col, $str)) {
                            echo '<tr><td>' . $col . '</td><td> <span class="glyphicon glyphicon-ok"></span></td></tr>';
                        } else {
                            echo '<tr class="alert-danger"><td>' . $col . '</td><td> <span class="glyphicon glyphicon-remove"></span></td></tr>';
                            $status = FALSE;
                        }
                    }
                    ?>
                </table>
            </div>
            <?php
            $importlink = site_url('admin/check-data') . '&action=import&filename=' . $_GET['filename'];
            $uploadlink = site_url('admin/file-manager');
            ?>
            <span class="clearfix"></span>
            <?php if ($status): ?>
                <div class="alert alert-success col-md-4">ข้อมูลถูกต้องสามารถ <a href="<?php echo $importlink; ?>">โอนแฟ้มข้อมูล </a></div>
            <?php else: ?>
                <div class="alert alert-warning col-md-4">ข้อมูลไม่ถูกต้องกลับไป <a href="<?php echo $uploadlink; ?>">จัดการแฟ้มข้อมูล </a></div>  
            <?php endif; ?>
            <span class="clearfix"></span>
            <div class="table table-responsive">
                <?php
                $str = fgetcsv($handle);
                if (is_array($str)) {
                    $str_comma = implode(",", $str);
                    //echo $str_comma . '<br />';
                    $stdcharset = mb_detect_encoding($str_comma, "UTF-8", TRUE) ? "UTF-8" : "TIS-620";
                    echo 'charset = ' . $stdcharset . ' <br />';
                    if ($stdcharset == "UTF-8") {
                        foreach ($str as $value) {
                            if ($i < 10)
                                echo $i++ . " :  " . $value . "<br />";
                        }
                    } else {
                        foreach ($str as $value) {
                            // if (!mb_detect_encoding($value,"utf-8")){
                            $value = iconv("tis-620", "utf-8", $value);
                            // }
                            if ($i < 10)
                                echo $i++ . " :  " . $value . "<br />";
                        }
                    }
                }
                fclose($handle);
            }
        }
        ?>     
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
    $handle = fopen($stdfile, "r");
// get header column from file     
    $cols= fgetcsv($handle); 
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
