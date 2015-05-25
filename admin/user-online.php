<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ผู้ใช้ที่กำลังใช้งาน";
$active = 'user-online';
//$subactive = 'user-online';
is_admin('home/index');
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 25;
$order = isset($_GET['order']) ? $_GET['order'] : '';
    $url = site_url('admin/user-online');
?>
<?php require_once INC_PATH . 'header.php'; ?>
<div class='container'>
    <?php show_message() ?>
    <div class="page-header"><h4>รายชื่อผู้ใช้ที่กำลังออนไลน์</h4></div>
    <?php 
    $total = get_total();
    if($total > 0):
        ?>
        <?php echo pagination($total, $url, $page, $order) ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อผู้ใช้</th>
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
                    <td><?php echo $user['RadAcctId'] ?></td>
                    <td><?php echo $user['UserName'] ?></td>
                    <td><?php echo $user['FramedIPAddress'] ?></td>
                    <td><?php echo $user['AcctStartTime'] ?></td>
                    <td><?php echo $user['AcctSessionTime'] ?></td>
                    <td><?php echo $user['AcctInputOctets'] ?></td>
                    <td><?php echo $user['AcctOutputOctets'] ?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo pagination($total, 'admin/user-online', $page); ?>
    <?php else : ?>
    <p>ไม่มีผู้ใช้ที่กำลังออนไลน์</p>
    <?php endif; ?>
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
