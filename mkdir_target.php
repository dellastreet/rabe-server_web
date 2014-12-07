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
$query = "SELECT target_number FROM rabedb_target WHERE target_id = '".$_GET['target']."';";
$result = $mysqli->query($query);
$row_t = $result->fetch_array(MYSQLI_ASSOC);
$mysqli->close();

$shellCommand = shell_exec("sudo ./rabe-server_mkdir_target.sh ".$row_c['case_path']." ".$row_t['target_number']);

header("Location: case.php?case=".$_GET['case']);

?>
