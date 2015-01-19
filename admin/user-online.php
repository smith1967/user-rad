<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "ผู้ดูแลระบบ";
$active = 'admin';
is_admin('home/index');
?>

<?php
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

/* Use disconect-user
if (isset($_GET['username'])) {
    $sql = "UPDATE radacct SET AcctStopTime = NOW() WHERE username = " . qs($_GET['username']) . " AND AcctStopTime = '0000-00-00 00:00:00'";
    $rs = mysql_query($sql);
    if (!$rs) {
        echo "" . mysql_error();
        die($$sql);
    }
    redirect('user_online.php');
    die();
}
if (isset($_GET['clearall'])) {
    $sql = "UPDATE radacct SET AcctStopTime = NOW() WHERE AcctStopTime = '0000-00-00 00:00:00'";
    $rs = mysql_query($sql);
    if (!$rs) {
        echo "" . mysql_error();
        die($$sql);
    }
    redirect('user_online.php');
    die();
}
 * 
 */
?>

        <div class="container">
            <div class="page-header"><h3>รายชื่อผู้ที่กำลังใช้งานอยู่</h3></div>

            <?php
            if(isset($_GET['order'])){
               $sql = "select * from radacct,users where radacct.AcctStopTime = '0000-00-00 00:00:00' and radacct.UserName = users.username order by radacct.FramedIPAddress LIMIT 0,100 ";
            }else{
               $sql = "select * from radacct,users where radacct.AcctStopTime = '0000-00-00 00:00:00' and radacct.UserName = users.username order by radacct.AcctStartTime LIMIT 0,100";                      
            }
            $result = mysqli_query($sql);
            $totals = mysqli_num_rows($db, $result);
            ?>
            <span style="font-size: 16px;text-align: center;display: block;margin: 0 20 10 20;margin: 0 0 10px 0;"><h4>
	จำนวนผู้ใช้งานในช่วงเวลานี้ มีทั้งสิ้น <strong style="color: #FF0000"><?= $totals ?></strong> คน</h4>
            </span>

            <table width="95%" align="center" cellspacing="1" class="admintable">
                <thead>
                    <th width="100" height="24" align="center" class="key">ลำดับที่</th>
                    <th width="150" height="24" align="center" class="key">ชื่อผู้ใช้งาน</th>
                    <th width="300" height="24" align="center" class="key">ชื่อ - นามสกุล</th>
                    <th width="250" height="24" align="center" class="key">เริ่มต้นใช้งาน</th>
                    <th width="100" height="24" align="center" class="key">เป็นเวลา</th>
                    <th width="150" height="24" align="center" class="key"><a href="./user_online.php?order=ipaddress">หมายเลขไอพี</a></th>
                    <th width="150" height="24" align="center" class="key">ลบ session</th>    </thead>
                    <?
                    $count = 0;
                    while ($data = mysql_fetch_object($result)) {
                        $count++;
                        ($count % 2 != 0) ? $bgcolor = "#FFFFFF" : $bgcolor = "#F6F6F6";
                    ?>
                    <tr>
                        <td align="rigth" valign="top" bgcolor="<?= $bgcolor
                    ?>"><?= $count
                    ?></td>
                    <td  align="left" valign="top" bgcolor="<?= $bgcolor
                    ?>"><?= $data->username
                    ?></td>
                     <td align="left" valign="top" bgcolor="<?= $bgcolor
                    ?>">&nbsp;<?= $data->fname ?> <?= $data->lname ?></td>
                    <td align="left" valign="top" bgcolor="<?= $bgcolor
                    ?>"><?= $data->AcctStartTime
                    ?></td>

                    <td align="left" valign="top" bgcolor="<?= $bgcolor
                    ?>">&nbsp;<?
                        $hours = floor($data->AcctSessionTime / 60.0 / 60.0);
                        $mins = floor(($data->AcctSessionTime - $hours * 60.0 * 60.0) / 60.0);
                        $secs = $data->AcctSessionTime - ($hours * 60.0 * 60.0) - ($mins * 60.0);
                        printf("%d:%02d:%02d", $hours, $mins, $secs);
                    ?>&nbsp;&nbsp;</td>
                    <td align="left" valign="top" bgcolor="<?= $bgcolor
                    ?>">&nbsp;<?= $data->FramedIPAddress ?>&nbsp;&nbsp;</td>
                    <td align="left" valign="top" bgcolor="<?= $bgcolor
                    ?>"><a href=<?php site_url('admin/disconnect-user') ?>"&username=<?=$data->username
                    ?>" onclick="return confirm('ต้องการลบใช่หรือไม่')">ลบ</a></td>
                </tr>
                <?
                    }
                ?>
				<tr><td></td></tr>
            </table>
            <BR /><BR />
        </div>

