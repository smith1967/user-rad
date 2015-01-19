<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$active='login';
?>
<?php
if (isset($_POST['submit'])) {
    $data = $_POST;
    do_login($data);
}
?>
<?php require_once INC_PATH.'header.php'; ?>
<script>
    $(document).ready(function() {
        $("#username").focus();
    });
</script>
<?php
if (isset($_SESSION['err'])):
    echo show_error($_SESSION['err']);
    unset($_SESSION['err']);
    //var_dump($_SESSION);
endif;
if (isset($_SESSION['info'])):
    echo show_info($_SESSION['info']);
    unset($_SESSION['info']);
endif;
?>
<div class='container'>
    <div class="page-header">
        <h3>เข้าใช้ระบบ</h3>
    </div>

    <form class="form-horizontal" id="signupfrm" method="post" action="">
        <fieldset>
            <div class="form-group">
                <label class="control-label col-md-2" for="username">ชื่อผู้ใช้</label>
                <div class="col-md-3">
                    <input type="text" class="input-xlarge" id="username" name="username" placeholder="Username" value='<?php echo isset($username) ? $username : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2" for="password">รหัสผ่าน</label>
                <div class="col-md-3">
                    <input type="password" class="input-xlarge" id="password" name="password" value='<?php echo isset($password) ? $password : ''; ?>'>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <button type="submit" class="btn btn-primary btn-lg" name='submit'>เข้าระบบ</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<?php require_once INC_PATH.'footer.php'; ?>
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
        //echo $sql;
        $result = mysqli_query($db, $sql);
        //var_dump($result);
        //die();
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $_SESSION['user'] = $row;
            $_SESSION['user']['role'] = 'admin';
            //$_SESSION['info'][] = "ยินดีต้อนรับคุณ ".$_SESSION['user']['fname'];
            redirect('admin/index');
        } else {
            $_SESSION['err'][] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง ";
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
            //$_SESSION['info'][] = "ยินดีต้อนรับคุณ ".$_SESSION['user']['fname'];
            redirect('home/index');
        } else {
            $_SESSION['err'][] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง ";
            //redirect('user/login');
        }
    }
}
?>


