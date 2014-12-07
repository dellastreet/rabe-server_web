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
$query = "SELECT * FROM rabedb_targetConnect WHERE targetConnect_id = '".$_GET['targetConnect']."';";
$result = $mysqli->query($query);
$row_tc = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT * FROM rabedb_target WHERE target_id = '".$row_tc['targetConnect_target']."';";
$result = $mysqli->query($query);
$row_t = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT * FROM rabedb_case WHERE case_id = '".$row_t['target_case']."';";
$result = $mysqli->query($query);
$row_c = $result->fetch_array(MYSQLI_ASSOC);

$shellCommand = shell_exec("sudo ./rabe-server_stop_acquire.sh ".$_SESSION['user']." ".$row_c['case_id']." ".$row_c['case_path']." ".$row_t['target_number']." ".$row_t['target_ip']." ".str_replace(':', '', strtolower($row_t['target_mac']))." ".$row_tc['targetConnect_blockDevice']." ".$row_tc['targetConnect_blockDeviceMount']);

$query = "UPDATE rabedb_targetConnect SET targetConnect_acquisition = '3' WHERE targetConnect_id = '".$row_tc['targetConnect_id']."';";
if ($mysqli->query($query) === TRUE) {
        echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
        echo "<body><p>Stopping acquisition...<br />Record update succesful!</p></body>";
}
else {
        printf("Record update failed: %s\n", $mysqli->error);
}
$mysqli->close();

header("Refresh: 2; url=target.php?case=".$row_t['target_case']."&target=".$row_t['target_id']);

?>
