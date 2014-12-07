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
$query = "UPDATE rabedb_targetTemp SET targetTemp_status = '1' WHERE targetTemp_id = '".$_GET['targetTemp']."';";
if ($mysqli->query($query) === TRUE) {
        echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
        echo "<body><p>Setting status: closed...<br />Record update succesful!</p></body>";
}
else {
	printf("Record update failed: %s\n", $mysqli->error);
}
$mysqli->close();

header("Refresh: 1; url=home_".$_SESSION['userOrganisation'].".php");

?>
