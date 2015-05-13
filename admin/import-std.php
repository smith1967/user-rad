<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "นำข้อมูล std เข้าระบบ";
$active = 'admin';
$subactive = 'import-std';
is_admin('home/index');
if (!isset($_GET['filename'])) {
    redirect('admin/file-manager');
}
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
    ?>     

</div> <!--End Main container -->
<?php require_once INC_PATH . 'footer.php'; ?>

<?php
function do_transfer($stdfile) {
    global $db;
    $err = array();
    $info = array();
// -- fields std
    $stdcol = array('student_id', 'people_id', 'stu_fname', 'stu_lname', 'group_id');
// -- table cols
    $dbcol = array('std_id', 'pid', 'fname', 'lname', 'groupname');
    /* insert data to table tmp */
    // die($stdfile);
    $handle = fopen($stdfile, "r");
// get header column from file    
    $line = fgets($handle);
    $cols = explode(",", $line);
    // print_r($cols);


    $colindex = array();   // --- get index of array
    for ($i = 0; $i < count($stdcol); $i++) {
        for ($j = 0; $j < count($cols); $j++) {
            if ($stdcol[$i] == $cols[$j]) {
                $colindex[] = $j;
            }
        }
    }
// get data to array
//     fclose($handle);
//    die();    
//    print_r($colindex);

    while (!feof($handle)) {
        $line = iconv("tis-620", "utf-8", fgets($handle));
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
