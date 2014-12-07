<?php

$mysqli = new mysqli("localhost", "rabeTemp", "pmet", "rabe_db");
if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error());
        exit();
}
$query = "SELECT COUNT(targetTemp_ip) FROM rabedb_targetTemp WHERE targetTemp_status != '1' AND targetTemp_ip = '".$_GET['ip']."';";
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);

if ((int)$row['COUNT(targetTemp_ip)'] <= 3) {
	$ip=$_GET['ip'];
	$ipVpn=$_GET['ipVpn'];
	$mac=$_GET['mac'];
	$blockDevices=$_GET['blockDevices'];
	$diskInfo=$_GET['diskInfo'];
	
	$query = "INSERT INTO rabedb_targetTemp (targetTemp_ip, targetTemp_ipVpn, targetTemp_mac, targetTemp_blockDevices, targetTemp_diskInformation, targetTemp_on) VALUES ('".$ip."', '".$ipVpn."', '".$mac."', '".$blockDevices."', '".$diskInfo."', NOW());";
	if ($mysqli->query($query) === TRUE) {
		echo "Record added!";
	}
	else {
		$error = "Record add failed:\n".$mysqli->error;
	}
}
else {
	echo "Record not added! Target already in database!";
}
$mysqli->close();

?>
