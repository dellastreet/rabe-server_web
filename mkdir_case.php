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
$row = $result->fetch_array(MYSQLI_ASSOC);
$mysqli->close();

$shellCommand = shell_exec("sudo ./rabe-server_mkdir_case.sh ".$row['case_path']);

header("Location: home_".$_SESSION['userOrganisation'].".php");

?>
