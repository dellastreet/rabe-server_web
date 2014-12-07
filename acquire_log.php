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
$mysqli->close();

echo "<html>";
echo "<head><title>RABE acquire: ".$row_tc['targetConnect_blockDevice']." of ".$row_t['target_ip']."</title><head>";
echo "<link rel='stylesheet' type='text/css' href='shell.css'>";
echo "<body>";

header("Refresh: 5; url=acquire_log.php?targetConnect=".$row_tc['targetConnect_id']."");

$acquisitionFile = "file://".$row_c['case_path'].$row_t['target_number']."/meta/logs/acquisition.log";
$shellOutput = file_get_contents($acquisitionFile);
echo "<pre>".$shellOutput."</pre>";

echo "</body>";
echo "</html>";

?>
