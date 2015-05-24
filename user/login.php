<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$active = 'login';
?>
<?php
if (isset($_POST['submit'])) {
    $data = $_POST;
    do_login($data);
}
?>
<?php require_once INC_PATH . 'header.php'; ?>
<script>
    $(document).ready(function() {
        $("#username").focus();
    });
</script>
<?php
?>
<div class='container'>
    <?php show_message() ?>
    <div class="col-md-offset-3 col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <label class="panel-title">ลงชื่อเข้าระบบ</label>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" id="signupfrm" method="post" action="">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="username">ชื่อผู้ใช้</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value='<?php echo isset($username) ? $username : ''; ?>'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="password">รหัสผ่าน</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="password" name="password" value='<?php echo isset($password) ? $password : ''; ?>'>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-default" name='submit'>เข้าระบบ</button>
                            </div>
                        </div>
                </form>
            </div>      
        </div>
    </div>
</div>
<?php require_once INC_PATH . 'footer.php'; ?>
<?php
function get_info($mem_id) {
    global $db;
    $query = "SELECT * FROM member WHERE mem_id='" . pq($mem_id + 0) . "'";
    $res = mysqli_query($db, $query);
    return $res;
}

function do_login($data) {
    global $db;
    if ($data['username'] == 'admin') {
        $sql = "SELECT * FROM users WHERE username = " . pq($data['username']) . " AND password = " . pq($data['password']) . ";";
        $result = mysqli_query($db, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $_SESSION['user'] = $row;
            $_SESSION['user']['role'] = 'admin';
            set_info("ยินดีต้อนรับคุณ " . $_SESSION['user']['fname']);
            redirect('admin/index');
        } else {
            set_err("ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง ");
            //redirect('user/login');
        }
    } else {
        $sql = "SELECT * FROM users WHERE username = " . pq($data['username']) . " AND password = " . pq($data['password']) . ";";
        //echo $sql;
        $rs = mysqli_query($db, $sql);
        if (mysqli_num_rows($rs) > 0) {
            $row = mysqli_fetch_array($rs);
            $_SESSION['user'] = $row;
            $_SESSION['user']['role'] = 'other';
            set_info("ยินดีต้อนรับคุณ " . $_SESSION['user']['fname']);
            redirect('home/index');
        } else {
            set_err("ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง ");
            //redirect('user/login');
        }
    }
}
?>


