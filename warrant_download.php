<?php

session_start();

$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error());
        exit();
}
$query = "SELECT case_path FROM rabedb_case WHERE case_id = '".$_GET['case']."';";
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);

$file = "file://".$row['case_path']."meta/warrant.pdf";

if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($file));
        readfile($file);
        exit;
}
else {
        echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
        echo "<body><p>File not found!</p></body>";
	
	header("Refresh: 2; url=home_".$_SESSION['userOrganisation'].".php");
}

?>
