<?php
//require_once('auth.php');
//check_auth();
is_admin();
?>
<?php
//accept POST and GET paramaters
isset($_REQUEST['username']) ? $username = $_REQUEST['username'] : $username = "";
isset($_REQUEST['nasaddr']) ? $nasaddr = $_REQUEST['nasaddr'] : $nasaddr = "";
isset($_REQUEST['coaport']) ? $coaport = $_REQUEST['coaport'] : $coaport = "";
isset($_REQUEST['nassecret']) ? $nassecret = $_REQUEST['nassecret'] : $nassecret = "";
	
if (isset($_POST['submit'])) {
		include_once('library/libTestNDisconnect.php');
		
		$username = $_POST['username'];
		$nasaddr = $_POST['nasaddr'];
		$coaport = $_POST['coaport'];
		$sharedsecret = $_POST['nassecret'];
		
		//fix mysql database: update radacct set AcctTerminateCause='Admin-Reset', AcctStopTime=now() where username=$username and acctStopTime is null
		$updateSQL = sprintf("UPDATE radacct SET AcctTerminateCause='%s', AcctStopTime=NOW() WHERE UserName='%s' and AcctStopTime IS NULL","Admin-Reset", $username);

  		mysql_select_db($database_cnRadius, $cnRadius);
  		$Result1 = mysql_query($updateSQL, $cnRadius) or die(mysql_error());
		
		//disconnect using radclient
		$result = disconnect_user($username, $nasaddr, $coaport, $sharedsecret);
}
?>
