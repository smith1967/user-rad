<?php
include_once 'include/config.php';
//include_once 'include/menu.php';
$req = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME'])+1);
$param = explode('&', $req);
//var_dump($param);


$ctrl_act = array_shift($param);
$file = dirname(__FILE__) . '/' . $ctrl_act . '.php';
if (!is_file($file)) {
	$file = dirname(__FILE__) . '/home/index.php';
}
//var_dump($file);
//die();
ob_start();
include $file;
$content = ob_get_contents();
ob_end_clean();
echo $content;
//require_once INC_PATH. 'template.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
