<?php

session_start();

if ($_SESSION['userOrganisation'] != "4") {
        header("Location: access.php");
}
$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
if ($mysqli->connect_errno) {
	printf("Connect failed: %s\n", $mysqli->connect_error());
	exit();
}
$query = "UPDATE rabedb_target SET target_status = '5' WHERE target_id = '".$_GET['target']."';";
if ($mysqli->query($query) === TRUE) {
        echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
        echo "<body><p>Setting status: booted...<br />Record update succesful!</p></body>";
}
else {
	printf("Record update failed: %s\n", $mysqli->error);
}
$mysqli->close();

header("Refresh: 1; url=request_".$_SESSION['userOrganisation'].".php?request=".$_GET['request']."&case=".$_GET['case']."");

?>
