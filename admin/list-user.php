<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ผู้ดูแลระบบ";
$active = 'admin';
$subactive = 'list-user';
is_admin('home/index');
?>
<?php 
if(isset($_GET['action']) && $_GET['action'] == 'list-std'){
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    $action = isset($_GET['action']) ? $_GET['action'] : "list";
    $group = isset($_GET['group']) ? $_GET['group'] : '';
    $order = isset($_GET['order']) ? $_GET['order'] : '';
    $params = array(
        'action' => $action,
        'group' => $group
    );
    $params = http_build_query($params);
    $userslist = get_std_list($page,$group);
    $total = get_total($group);
    $url = site_url('admin/list-user&').$params;
    //var_dump($userlist);
}

if(isset($_GET['action']) && $_GET['action'] == 'list-users'){
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    $action = isset($_GET['action']) ? $_GET['action'] : "list";
    $group = isset($_GET['group']) ? $_GET['group'] : '';
    $order = isset($_GET['order']) ? $_GET['order'] : '';
    $params = array(
        'action' => $action,
        'group' => $group
    );
    $params = http_build_query($params);
    $userslist = get_users_list($page,$group);
    $total = get_users_total($group);
    $url = site_url('admin/list-user&').$params;
//    var_dump($userlist);
//    if(empty($total))redirect("/home/");
//    die();
}
//if(!isset($total))redirect("/home/");
?>

<?php require_once INC_PATH . 'header.php'; ?>

<div class="container">
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <?php
    show_message();
    ?> 
    <?php echo pagination($total, $url, $page, $order) ?>
        <div class="table-responsive"> 
        <table class="table table-striped table-condensed table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Groupname</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($userslist as $user) :
                ?>                            
                <tr>
                    <td><?php echo $user['id'] ?></td>
                    <td><?php echo $user['username'] ?></td>
                    <td><?php echo $user['fname'] ?></td>
                    <td><?php echo $user['lname'] ?></td>
                    <td><?php echo $user['groupname'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
</div> <!-- Main contianer -->
<?php require_once INC_PATH . 'footer.php'; ?>
<?php 
function get_std_list($page=0,$group,$limit=10){
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
function get_users_list($page=0,$group,$limit=10){
    global $db;
    $start = $page*$limit;
    $val = $group."%";
    $query = "SELECT * FROM users WHERE groupname LIKE ".pq($val)." LIMIT ".$start.",".$limit;
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
function get_users_total($group){
    global $db;
    $val = $group."%";
    $query = "SELECT * FROM users WHERE groupname LIKE ".pq($val);
   $result = mysqli_query($db, $query);
   return mysqli_num_rows($result);            
}
