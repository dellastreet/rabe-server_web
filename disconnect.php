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
$query = "SELECT * FROM rabedb_target WHERE target_id = '".$_GET['target']."';";
$result = $mysqli->query($query);
$row_t = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT case_path FROM rabedb_case WHERE case_id = '".$row_t['target_case']."';";
$result = $mysqli->query($query);
$row_c = $result->fetch_array(MYSQLI_ASSOC);

$shellCommand = shell_exec("sudo ./rabe-server_disconnect.sh ".$row_t['target_ipVpn']." ".$row_c['case_path'].$row_t['target_number']."");

$query = "UPDATE rabedb_targetConnect SET targetConnect_blockDeviceMount = '', targetConnect_acquisition = '', targetConnect_reexportName = '' WHERE targetConnect_target = '".$row_t['target_id']."';";
if ($mysqli->query($query) === TRUE) {
        echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
        echo "<body><p>Disconnecting all...<br />Record update succesful!</p></body>";
}
else {
        printf("Record update failed: %s\n", $mysqli->error);
}
$mysqli->close();

header("Refresh: 2; url=target.php?case=".$row_t['target_case']."&target=".$row_t['target_id']."");

?>
