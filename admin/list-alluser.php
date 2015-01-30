<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ผู้ดูแลระบบ";
$active = 'admin';
$subactive = 'list-alluser';
is_admin('home/index');
?>


<?php require_once INC_PATH . 'header.php'; ?>
<script charset="utf8">
    $(document).ready(function() {
        $("#sel_gid").change(function() {
            $("#form_gid").submit();
        });
    });
</script>    
<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <div class="page-header" style="margin-top: 0px;"><h4>จัดการข้อมูลลงทะเบียนผู้ใช้ใหม่</h4></div>
    <?php
    show_message();
    ?> 

<?php   $sql = "SELECT gid,group_desc FROM group_config";?>
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
<?php 
/*
            //$ses = $_SESSION;
//echo $_POST['gid'];
            $sql = "SELECT gid,group_desc FROM group_config";
            echo '<form method="post" action="" >';
            echo '<select id="gid" name="gid" onchange="this.form.submit();">';
            echo '<option value="">-เลือกกลุ่มผู้ใช้-</option>';
            echo gen_option_sql($sql, isset($_POST['gid']) ? $_POST['gid'] : "");
            echo '</select>';
            echo '</form>';
 * 
 */

            if (isset($_POST['gid'])) {
                $data = $_POST;
                $sql = "SELECT * FROM register WHERE comfirm LIKE 'Y' AND gid = " . pq($data['gid']) . ";";
                $rs = mysqli_query($db, $sql);
                if (mysqli_num_rows($rs) > 0) {
                    echo '<form action="transfer_action.php" method="post" id="transfer_form">';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered">';                    
                    //echo '<form action="" method="post">';
                    //echo '<table>';
                    $array = array(
                        'ลำดับ',
                        'ชื่อผู้ใช้',
                        'ชื่อ',
                        'นามสกุล',
                        //'รหัสผ่าน',
                        'วันที่ลงทะเบียน',
                        'IP Address',
                        'แผนก/งาน',
                        'ลบ'
                    );
                    echo gen_thead($array);                    $n = 0;
                    while ($row = mysqli_fetch_array($rs)) {
                        $class = ($n++) % 2 ? "even" : "odd";
                        $s ="<tr class='" . $class . "'>";
                        $array = array(
                            $n,
                            $row['username'],
                            $row['fname'],
                            $row['lname'],
                            //$row['password'],
                            $row['created'],
                            $row['hostname'],
                            $row['department']
                            );
                        $s .= gen_td($array);
                        $s .= '<td><input type="checkbox" name="username[]" id="username"';
                        $s .= '" value="' . $row['username'] . '"/></td>';
                        $s .= '</tr>';
                        echo $s;
                        //echo json_encode($row);
                    }
                    //echo '<tr><td colspan="3"><input type="submit" value="ลบข้อมูลออกจากระบบ" onclick="javascript:return confirm(\'ข้อมูลจะถูกลบออกจากระบบ แน่ใจหรือไม่\')"/></td></tr>';
                    echo '</table>';
                    echo '</div>'; //
                    echo '<input type="submit" value="ลบข้อมูลออกจากระบบ" onclick="javascript:return confirm(\'ข้อมูลจะถูกลบออกจากระบบ แน่ใจหรือไม่\')"/>';
                    echo '</form>';
                } else {
                    echo "<p>ยังไม่มีข้อมูลครับ</p>";
                }
            }
            //echo '<p><a href="../main/index.php">กลับหน้าหลัก</a></p>';

            if (isset($_POST['username'])) {
                $data = $_POST;
                $n = 0;
                foreach ($data['username'] as $v) {
                    $sql = "DELETE FROM radcheck WHERE username LIKE " . pq($v);
                    mysqli_query($db, $sql);
                    $sql = "DELETE FROM register WHERE username LIKE " . pq($v);
                    mysqli_query($db, $sql);
                    $sql = "DELETE FROM users WHERE username LIKE " . pq($v);
                    mysqli_query($db, $sql);
                    $sql = "DELETE FROM radreply WHERE username LIKE " . pq($v);
                    mysqli_query($db, $sql);
                    $sql = "DELETE FROM users WHERE username LIKE " . pq($v);
                    mysqli_query($db, $sql);
                    //echo $sql."<br />";
                    $n++;
                }
                echo "<p> ลบข้อมูลจำนวน $n รายการ </p>";
            }
?>

</div> <!-- Main contianer -->
<?php require_once INC_PATH . 'footer.php'; ?>
