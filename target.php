<?php

session_start();

if ($_SESSION['userOrganisation'] != "1") {
        header("Location: access.php");
}

$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
if ($mysqli->connect_errno) {
	printf("Connect failed: %s\n", $mysqli->connect_error());
	exit();
}
$query = "SELECT case_path FROM rabedb_case WHERE case_id = '".$_GET['case']."';";
$result = $mysqli->query($query);
$row_c = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT * FROM rabedb_target WHERE target_case = '".$_GET['case']."' AND target_id = '".$_GET['target']."';";
$result = $mysqli->query($query);
$row_t = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT * FROM rabedb_targetConnect WHERE targetConnect_target = '".$_GET['target']."';";
$result = $mysqli->query($query);
while($row_tc = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows_tc[] = $row_tc;
}
$mysqli->close();

echo "<html>";
echo "<head><title>RABE target: ".$row_t['target_ip']."</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | <a id='nav' href='case.php?case=".$_GET['case']."'>Case</a> | Target: ".$row_t['target_ip']."</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label>".$row_t['target_case']."<br />";
echo "<label>Target ID: </label>".$row_t['target_id']."<br />";
echo "<label>Number: </label>".$row_t['target_number']."<br />";
echo "<label>Created by: </label>".$row_t['target_createdBy']."<br />";
echo "<label>Created on: </label>".$row_t['target_createdOn']."</p>";

echo "<p><label>IP Address: </label>".$row_t['target_ip']."<br />";
echo "<label>VPN IP Address: </label>".$row_t['target_ipVpn']."<br />";
echo "<label>MAC Address: </label>".$row_t['target_mac']."</p>";

echo "<p><input type='button' id='createLiveImage' name='createLiveImage' value=' create live image '></p>";

echo "<p><label>Disk Information:</label></p>";
echo "<textarea readonly cols='75' rows='5' name='diskInformation'>".$row_t['target_diskInformation']."</textarea>";

echo "<p><label>Block Devices:</label></p>";
echo "<table>";

$disconnect = "";

if (empty($rows_tc)) {
        echo "<tr>no block devices</tr>";
}
else {
        echo "<tr><th></th><th>Mounted As</th><th>Acquisition</th><th>Re-export Name</th><th></th><th></th><th></th></tr>";
        foreach($rows_tc as $row_tc) {
                echo "<tr>";
		
		# acquire / stop acquisition
		if ($row_tc['targetConnect_acquisition'] == "0") {
			$acquisition = "";
			$acquire = "<td><input type='button' onClick=javascript:location.href='acquire.php?targetConnect=".$row_tc['targetConnect_id']."' id='acquireTarget' name='acquireTarget' value=' acquire '></td>";
		}
		elseif ($row_tc['targetConnect_acquisition'] == "1") {
			$acquisition = "<a href='acquire_log.php?targetConnect=".$row_tc['targetConnect_id']."' target='_blank' alt='show acquisition log'>running</a>";
			$acquire = "<td><input type='button' onClick=javascript:location.href='stop_acquire.php?targetConnect=".$row_tc['targetConnect_id']."' id='stop_acquireTarget' name='stop_acquireTarget' value=' stop acquisition '></td>";
		}
		else {
			$acquisition = "<a href='acquire_log.php?targetConnect=".$row_tc['targetConnect_id']."' target='_blank' alt='show acquisition log'>aborted</a>";
			$acquire = "<td><input type='button' onClick=javascript:location.href='acquire.php?targetConnect=".$row_tc['targetConnect_id']."' id='acquireTarget' name='acquireTarget' value=' acquire '></td>";
		}
		
                echo "<td>".$row_tc['targetConnect_blockDevice']."</td><td>".$row_tc['targetConnect_blockDeviceMount']."</td><td>".$acquisition."</td><td>".$row_tc['targetConnect_reexportName']."</td>";
		
		# connect / disconnect
		if (empty($row_tc['targetConnect_blockDeviceMount'])) {
			echo "<td><input type='button' onClick=javascript:location.href='connect.php?targetConnect=".$row_tc['targetConnect_id']."' id='connectTarget' name='connectTarget' value=' connect '></td><td></td><td></td>";
		}
		else {
			echo "<td>connected</td>";
			$disconnect = "<p><input type='button' onClick=javascript:location.href='disconnect.php?target=".$_GET['target']."' id='disconnectTarget' name='disconnectTarget' value=' disconnect all '></p>";
			
			# acquire / stop acquisition
                	echo $acquire;
			
			# re-export / stop re-export
                	if (empty($row_tc['targetConnect_reexportName'])) {
                        	echo "<td><input type='button' onClick=javascript:location.href='re-export.php?targetConnect=".$row_tc['targetConnect_id']."' id='reexportTarget' name='reexportTarget' value=' re-export '></td>";
                	}
                	else {
                        	echo "<td><input type='button' onClick=javascript:location.href='stop_re-export.php?targetConnect=".$row_tc['targetConnect_id']."' id='stop_reexportTarget' name='stop_reexportTarget' value=' stop re-export '></td>";
                	}
		}
                echo "</tr>";
        }
}
echo "</table>";

echo $disconnect;

$shellFile = "file://".$row_c['case_path'].$row_t['target_number']."/meta/logs/shell.log";
$shellOutput = file_get_contents($shellFile);

echo "<p><label>Shell:</label></p>";
echo "<pre><textarea readonly style='color:white; background-color:black;' cols='100' rows='10' name='shellOutput'>".$shellOutput."</textarea></pre>";

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
