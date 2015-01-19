<?php
/*
  if (!isset($_SESSION['username'])) {
  header('Content-type: text/html; charset=utf-8');
  $_SESSION['msg'] = "สำหรับผู้ดูแลระบบเท่านั้นครับ";
  redirect('../main/index.php', 2);
  die();
  }
  if ($_SESSION['username'] != 'admin') {
  header('Content-type: text/html; charset=utf-8');
  $_SESSION['msg'] = "สำหรับผู้ดูแลระบบเท่านั้นครับ";
  redirect('../main/index.php', 2);
  die();
  }

  if (isset($_SESSION['msg'])) {
  echo $_SESSION['msg'];
  unset($_SESSION['msg']);
  }
 * 
 */
?>

<script>
    $(document).ready(function() {
        $("#sel_gid").change(function() {
            $("#form_gid").submit();
        });
    });

</script>
<div class="container">
    <div>
        <div>
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url() ?>">หน้าหลัก</a></li>
                <li class='active'>ดูแลระบบ</li>
            </ol>
        </div>
        <div class='submenu'>
            <ul class="nav nav-pills">
                <li><a href="#">จัดการผู้ใช้ใหม่</a></li>
                <li><a href="#">จัดการผู้ใช้</a></li>
                <li><a href="#">พักการใช้งาน</a></li>
                <li><a href="#">ดูข้อมูลรหัสผ่าน</a></li>
                <li><a href="#">รายชื่อผู้กำลังใช้งาน</a></li>
                <li><a href="#">ประวัติการใช้งาน</a></li>
            </ul>
        </div>
        <div class="page-header" style="margin-top: 0px;"><h4>จัดการข้อมูลลงทะเบียนผู้ใช้ใหม่</h4></div>
        <?php
        $sql = "SELECT register.gid,group_desc,count(id) num  FROM register LEFT JOIN group_config ON register.gid = group_config.gid WHERE register.comfirm = 'N' GROUP BY register.gid;";
//echo $sql."<br />";
        $rs = mysqli_query($db, $sql);
        if (mysqli_num_rows($rs) > 0) {
            while ($row = mysqli_fetch_array($rs))
                echo "<div>จำนวน<strong>" . $row['group_desc'] . "</strong>ที่ลงทะเบียนใหม่ " . $row['num'] . " รายการ</div>";
        } else {
            echo "<div>ไม่พบรายการลงทะเบียน</div>";
        }

        $sql = "SELECT gid,group_desc FROM group_config";

        echo '<form method="post" id="form_gid" role="form" >';
        ?>
        <div class="col-xs-6 col-md-2" style="margin: 5px 0 5px -8px">
        <?php
        echo '<select class="form-control" id="sel_gid" name="gid">';
        echo '<option value="">-เลือกกลุ่มผู้ใช้-</option>';
        echo gen_option($sql, isset($_POST['gid']) ? $_POST['gid'] : "");
        echo '</select>';
        ?>
        </div>
        <?php    
        echo '</form>';

        if (isset($_POST['gid'])) {
            $data = $_POST;
            $sql = "SELECT * FROM register WHERE comfirm LIKE 'N' AND gid = " . pq($data['gid']) . ";";
            //$sql = "SELECT * FROM register WHERE gid = ".pq($data['gid']).";";
            $rs = mysqli_query($db, $sql);
            if (mysqli_num_rows($rs) > 0) {
                echo '<form action="transfer_action.php" method="post" id="transfer_form">';
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered">';
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
                $n = 0;
                $i = 0;
                while ($row = mysqli_fetch_array($rs)) {
                    $class = ($n++) % 2 ? "even" : "odd";
                    $s = "<tr class='" . $class . "'>";
                    $array = array(
                        $n,
                        $row['username'],
                        $row['fname'],
                        $row['lname'],
                        $row['created'],
                        $row['hostname'],
                        $row['department']
                    );
                    $s .= gen_td($array);
                    $s .= '<td><input type="checkbox" name="tid[' . $i . ']" id="tid"';  // <-- transfer data
                    $s .= ' value="' . $row['id'] . '" onclick=tidcheck(this) /></td>';
                    $s .= '<td><input type="checkbox" name="did[' . $i . ']" id="did"';  // <-- delete data
                    $s .= ' value="' . $row['id'] . '" onclick=didcheck(this) /></td>';
                    $s .= '</tr>';
                    echo $s;
                    $i++;
                    //echo json_encode($row);
                }
                echo '</table>';
                echo '</div>';
                echo '<p><input type="submit" value="โอน/ลบ ข้อมูล"/></p>';
                echo '</form>';
            } else {
                echo "<div class='alert-warning'><p>ยังไม่มีข้อมูลครับ</p></div>";
            }
        }
        ?>        
    </div>

</div>



