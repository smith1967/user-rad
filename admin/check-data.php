f <?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ตรวจสอบข้อมูล";
$active = 'admin';
$subactive = 'check-data';
if(!isset($_GET['filename']))
    redirect ('admin/file-manager');
is_admin('home/index');
?>
<?php
// -- fields std
$stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
// -- table cols
$dbcol = array('std_id', 'pid', 'fname', 'lname', 'groupname');
// check submit
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
            $importlink = site_url('admin/import-std') . '&action=import&filename=' . $_GET['filename'];
            $uploadlink = site_url('admin/file-manager') ;
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
                        if($i<10)
                            echo $i++ . " :  " . $value . "<br />";
                    }
                } else {
                    foreach ($str as $value) {
                        // if (!mb_detect_encoding($value,"utf-8")){
                        $value = iconv("tis-620", "utf-8", $value);
                        // }
                        if($i<10)
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
        set_info('โอนข้อมูลจำนวน ' . mysqli_affected_rows($db) . ' ใส่ตารางชั่วคราว');
    } else {
        set_err("การโอนข้อมูลใส่ตารางชั่วคราวผิดพลาด : " . mysqli_error($db));
    }
}
