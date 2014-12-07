<?php

session_start();

if ($_SESSION['userOrganisation'] != "2") {
        header("Location: access.php");
}

$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error());
        exit();
}
$query = "SELECT * FROM rabedb_case WHERE case_id = '".$_GET['case']."';";
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$mysqli->close();

$error = "";

if (isset($_POST['submit'])) {
	$organisation=3;
	
	$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
	if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error());
		exit();
	}
	$query1 = "UPDATE rabedb_request SET request_status = 2, request_organisation = '".$organisation."' WHERE request_id = '".$_GET['request']."';";
	if ($mysqli->query($query1) === TRUE) {
		$query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
		if ($mysqli->query($query2) === TRUE) {
			header("Location: home_".$_SESSION['userOrganisation'].".php");
		}
                else {
                       	$error = "Record add failed (log):\n".$mysqli->error;
		}
	}
	else {
        	$error = "Record add failed (request):\n".$mysqli->error;
	}
	$mysqli->close();
}

echo "<html>";
echo "<head><title>RABE forward request</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | Forward request</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label>".$row['case_id']."<br />";
echo "<label>Number: </label>".$row['case_number']."<br />";
echo "<label>Name: </label>".$row['case_name']."<br />";
echo "<label>Description: </label><br /><textarea readonly cols='75' rows='5' id='description' name='description'>".$row['case_description']."</textarea><br />";
echo "<label>Path: </label>".$row['case_path']."<br />";
echo "<label>Created by: </label>".$row['case_createdBy']."<br />";
echo "<label>Created on: </label>".$row['case_createdOn']."</p>";

echo "<h3>Location:</h3>";
if ($row['case_locationType'] == '2') {
        $locationType = "company";
}
elseif ($row['case_locationType'] == '3') {
        $locationType = "residence";
}
else {
        $locationType = "";
}
echo "<p><label>Type: </label>".$locationType."<br />";
echo "<label>Name: </label>".$row['case_locationName']."<br />";
echo "<label>Address: </label>".$row['case_locationAddress']."<br />";
echo "<label>City: </label>".$row['case_locationCity']."</p>";

echo "<p><input name='submit' type='submit' value=' forward '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
