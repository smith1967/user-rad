<?php
$sidemenu = array(
	'home' => array(
		'title'=>'หน้าหลัก',
		'url'=>'home/index',
		'cond'=>true,
	),
	'register' => array(
		'title'=>'สมัครสมาชิก',
		'url'=>'home/register',
		'cond'=> !isset($_SESSION['user']),
	),
	'profile' => array(
		'title'=>'แก้ไขข้อมูลสมาชิก',
		'url'=>'home/edit_profile',
		'cond'=> isset($_SESSION['user']),
	),    
	'news' => array(
		'title'=>'ข่าวสาร',
		'url'=>'club/club_news',
		'cond'=> isset($_SESSION['user']),
	),	
	'signin' => array(
		'title'=>'เข้าสู่ระบบ',
		'url'=>'home/login',
		'cond'=> !isset($_SESSION['user']),
	),
	'signout' => array(
		'title' => 'ออกระบบ',
		'url' => 'home/logout',
		'cond' => isset($_SESSION['user']),
	),
);
echo gen_menu('sidemenu', $sidemenu, '');

