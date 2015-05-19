<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ผู้ดูแลระบบ";
$active = 'admin';
$subactive = 'list-alluser';
is_admin('home/index');
?>
<?php 
if(isset($_GET['action']) && $_GET['action'] == 'list'){
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    $action = isset($_GET['action']) ? $_GET['action'] : "list";
    $group = isset($_GET['group']) ? $_GET['group'] : 'all';
    $params = array(
        action => $action,
        group => $group
    );
    $params = http_build_query($params);
    $userlist = get_userlist($page,$group);
    $total = get_total($group);
    $url = site_url('admin/list-user&').$params;
    //var_dump($userlist);
}
?>

<?php require_once INC_PATH . 'header.php'; ?>

<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <div class="page-header" style="margin-top: 0px;"><h4>รายชื่อผู้ใช้</h4></div>
    <?php
    show_message();
    ?> 
        <div class="table-responsive"> 
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Row</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Biography</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($userlist as $user) :
                ?>                            
                <tr>
                    <td><?php echo $user['username'] ?></td>
                    <td><?php echo $user['fname'] ?></td>
                    <td><?php echo $user['lname'] ?></td>
                    <td><?php echo $user['groupname'] ?></td>
                    <td><?php echo $user['id'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo pagination($total, $url, $page, $order) ?>

    

</div> <!-- Main contianer -->
<?php require_once INC_PATH . 'footer.php'; ?>
<?php 
function get_userlist($page=0,$group,$limit=10){
    global $db;
    $start = $page*$limit;
    $val = $group."%";
    $query = "SELECT * FROM users WHERE username LIKE ".pq($val)." LIMIT ".$start.",".$limit;
   $result = mysqli_query($db, $query);
   $userlist = array();
   while ($row = mysqli_fetch_array($result)) {
       $userlist[] = $row;
   }
   return $userlist;            
}
function get_total($group){
    global $db;
    $val = $group."%";
    $query = "SELECT * FROM users WHERE username LIKE ".pq($val);
   $result = mysqli_query($db, $query);
   return mysqli_num_rows($result);            
}