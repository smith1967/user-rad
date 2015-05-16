<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ผู้ใช้ที่กำลังใช้งาน";
$active = 'admin';
$subactive = 'user-online';
is_admin('home/index');
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 25;
$order = isset($_GET['order']) ? $_GET['order'] : '';
?>
<?php require_once INC_PATH . 'header.php'; ?>
<div class='container'>
    <?php show_message() ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อผู้ใช้</th>
                    <th>Package</th>
                    <th>IP Address</th>
                    <th>เวลาเริ่มต้น</th>
                    <th>เวลาที่ใช้</th>
                    <th>Uploads</th>
                    <th>Downloads</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $users = get_users($page, $limit, $order);
                foreach ($users as $user) :
                ?>
                <tr>
                    <td><?php echo $user['RadAccId'] ?></td>
                    <td><?php echo $user['UserName'] ?></td>
                    <td>Package</td>
                    <td><?php echo $user['FramedIPAddress'] ?></td>
                    <td><?php echo $user['AcctSessionTime'] ?></td>
                    <td><?php echo $user['AcctInputOctets'] ?></td>
                    <td><?php echo $user['AcctOutputOctets'] ?></td>
                    <td><?php echo $user['FramedIPAddress'] ?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo pagination(250, 'admin/user-online', $page); ?>
</div> <!-- end container -->
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function get_total() {
    global $db;
    $query = "SELECT UserName, AcctInputOctets, AcctOutputOctets, AcctStartTime, AcctSessionTime, FramedIPAddress, RadAcctId FROM radacct WHERE (AcctStopTime is NULL)";
    $result = mysqli_query($db, $query);
    return mysqli_num_rows($result);
}

function get_users($page = 0, $limit = 25, $order = '') {
    global $db;
    $users = array();
    $start = $page * $limit;
    if (empty($order)) {
        $query = "SELECT UserName, AcctInputOctets, AcctOutputOctets, AcctStartTime, AcctSessionTime, FramedIPAddress, RadAcctId FROM radacct WHERE (AcctStopTime is NULL) LIMIT $start,$limit";
    } else {
        $query = "SELECT UserName, AcctInputOctets, AcctOutputOctets, AcctStartTime, AcctSessionTime, FramedIPAddress, RadAcctId FROM radacct WHERE (AcctStopTime is NULL) ORDER BY $order LIMIT $start,$limit";
    }
    $result = mysqli_query($db, $query);
    if (!$result) {
        set_err(mysqli_error($db));
        redirect('admin/user-online');
        return;
    }
    while ($row = mysqli_fetch_array($result)) {
        $users[] = $row;
    }
    return $users;
}
