<?php

function hs($s) {
    return htmlspecialchars($s);
}

function pq($s) {
    global $db;
    $str = mysqli_real_escape_string($db, $s);
    return "'" . trim($str) . "'";
}

function site_url($url = '', $direct = false) {
    if (!$direct)
        return SITE_URL . 'index.php?' . $url;
    else
        return SITE_URL . $url;
}

function redirect($url = '') {
    $url = site_url($url);
    header('Location: ' . $url);
    exit;
    echo '<meta http-equiv="Refresh" content="0; url=' . $url . '" />';
    //echo '<meta http-equiv="refresh" content="0" url="'.$url.'">';
    //echo "<script>window.location.href='".$url."'</script>";
}

function is_auth($url='') {
    if(empty($url)){
        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {            
            return TRUE;
        }else{
            return FALSE;
        }
    }else{
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) 
            redirect($url);
    }
}
function is_admin($url='') {
    if(empty($url)){
        if (isset($_SESSION['user']) && $_SESSION['user']['username'] == 'admin') {
            return TRUE;
        }else{
            return FALSE;
        }
    }else{
        if (!isset($_SESSION['user']) || $_SESSION['user']['username'] != 'admin') 
            redirect($url);
    }    
}

function gen_option($sql, $def) {
    global $db;
    if (is_array($sql)) {
        foreach ($sql as $k => $v) {
            $sel = $k == $def ? ' selected="selected"' : '';
            $a[] = "<option value=\"$k\"{$sel}>$v</option>";
        }
    } else {
        $res = mysqli_query($db, $sql);
        $a = array();
        while ($row = mysqli_fetch_row($res)) {
            $sel = $row[0] == $def ? ' selected="selected"' : '';
            $a[] = "<option value=\"{$row[0]}\"{$sel}>{$row[1]}</option>";
        }
    }
    return implode('', $a);
}

function gen_radio($name, $data, $def = '', $sep = '') {
    global $db;
    $a = array();
    if (!is_array($data)) {
        $data = array();
        $res = mysqli_query($db, $data);
        while ($row = mysqli_fetch_row($res)) {
            $data[$row[0]] = $row[1];
        }
    }
    foreach ($data as $k => $v) {
        $id = $name . '_' . $k;
        $chk = $k == $def ? ' checked="checked"' : '';
        $a[] = "<input type=\"radio\" name=\"{$name}\" id=\"{$id}\" value=\"{$k}\"{$chk}><label for=\"{$id}\">{$v}</label>";
    }
    return implode($sep, $a);
}

function resize_image_data($img_data, $to_file, $width, $height) {
    $image = imagecreatefromstring($img_data);
    $width_orig = imagesx($image);
    $height_orig = imagesy($image);
    $ratio_orig = $width_orig / $height_orig;
    if ($width / $height > $ratio_orig) {
        $width = $height * $ratio_orig;
    } else {
        $height = $width / $ratio_orig;
    }
    $image_p = imagecreatetruecolor($width, $height);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    imagejpeg($image_p, $to_file, 100);
    imagedestroy($image_p);
    imagedestroy($image);
    return true;
}

function gen_menu($menu_class, $menu = array(), $active = 'home/index') {

    $a = array();
    foreach ($menu as $k => $m) {
        if ($m['cond'] === false)
            continue;
        $sel = $k == $active ? ' class="active"' : '';
        $href = site_url($m['url']);
        if (isset($m['param']))
            $href .= '&' . $m['param'];
        $a[] = '<li' . $sel . '><a href="' . $href . '">' . $m['title'] . '</a></li>';
    }
    return '<ul class="' . $menu_class . '">' . implode('', $a) . '</ul>';
}

function set_err($error=''){
    $_SESSION['err'][]=$error;
}
function set_info($info=''){
    $_SESSION['info'][]=$info;
}

function show_error($err) {
    echo '<div class="container">';
    echo '<div class="alert alert-danger">';
    if (is_array($err) && count($err) > 0)
        echo "<ul><li>" . implode('</li><li>', $err) . "</li></ul>";
    echo '</div></div>';
}

function show_info($info) {
    echo '<div class="container">';
    echo '<div class="alert alert-info">';
    if (is_array($info) && count($info) > 0)
        echo "<ul><li>" . implode('</li><li>', $info) . "</li></ul>";
    echo '</div></div>';
}

function check_pid($pid) {
    $pattern = '/\d{13}/';
    //$pid = $_POST['pid'];
    $result = true;
    if (preg_match($pattern, $pid)) {
        $sum = 0;
        for ($i = 0; $i < 12; $i++)
            $sum += (float) $pid[$i] * (13 - $i);
        if ((11 - $sum % 11) % 10 == (float) $pid[12])
            $result = false;
    }
    return $result;
}

function check_passwd($password, $pattern = '/^[a-zA-Z]{1}\w{4,13}[a-zA-Z]{1}$/') {
    //$pattern = '/^[a-zA-Z]{1}\w{4,13}[a-zA-Z]{1}$/';
    //$pid = $_POST['pid'];
    $result = false;
    $result = preg_match($pattern, $password);
    return $result;
}

function check_uname($username) {
    $pattern = '/^[a-z0-9]{5,12}$/';
    //$pid = $_POST['pid'];
    $result = false;
    if (preg_match($pattern, $username))
        $result = true;
    return $result;
}

function check_thai($s) {
    //$tis = utf2tis($s);
    //$pattern = '/^[ก-๙]{3,}$/';
    //$pattern = '/^[ก-๛]{3,}$/';
    $pattern = '#^[ก-๛]{3,}$#u';
    //$pid = $_POST['pid'];
    $result = false;
    if (preg_match($pattern, $s))
        $result = true;
    return $result;
}

function get_ip() {
    $ip = array();
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
        $ip[] = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {  //to check ip is pass from proxy
        $ip[] = $_SERVER['REMOTE_ADDR'];
    }
    if (count($ip) > 1) {
        $s = implode("/", $ip);
    } else {
        $s = $ip[0];
    }
    return $s;
}

function gen_thead($array) {
    $s = '<thead><th>' . implode('</th><th>', $array) . '</th></thead>';
    return $s;
}

function gen_td($array) {
    $s = '<td>' . implode('</td><td>', $array) . '</td>';
    return $s;
}

//contains function to test user connectivity and to force logout user

//function to test user connectivity
function test_user_connectivity($user, $password, $radiusserver, $radiusport, $nasportnumber, $nassecret) {
	//test user connectivity using radtest command
	$command = "radtest $user $password $radiusserver:$radiusport $nasportnumber $nassecret";
	$result = `$command`;
	
	$output="<b>Command</b>: $command<br /><b>Output:</b><br />".nl2br($result);
	return $output;
}

//function to force disconnect user
function disconnect_user ($theUser, $nasaddr, $coaport, $sharedsecret) {
	//disconnect user using radclient
	$command = "echo \"User-Name=$theUser\"|radclient -x $nasaddr:$coaport disconnect $sharedsecret";
	$result=`$command`;
	
	$output="<b>Command</b>: $command<br /><b>Output:</b><br />".nl2br($result)."<br />";
	return $output;
}

function show_message(){
    if (isset($_SESSION['err'])) {
        echo show_error($_SESSION['err']);
        unset($_SESSION['err']);
    }
    if (isset($_SESSION['info'])) {
        echo show_info($_SESSION['info']);
        unset($_SESSION['info']);
    }    
}
