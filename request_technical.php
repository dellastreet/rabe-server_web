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
$query = "SELECT * FROM rabedb_case WHERE case_id = '".$_GET['case']."';";
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$mysqli->close();

$error = "";

if (isset($_POST['submit'])) {
	$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
	if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error());
		exit();
	}
	$query1 = "UPDATE rabedb_case SET case_status = 2 WHERE case_id = '".$_GET['case']."';";
	if ($mysqli->query($query1) === TRUE) {
		$query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
		if ($mysqli->query($query2) === TRUE) {
			$error = "Record added!";
		}
		else {
			$error = "Record add failed (log):\n".$mysqli->error;
		}
	}
	else {
		$error = "Record add failed (case):\n".$mysqli->error;
	}
	
	$query3 = "INSERT INTO rabedb_request (request_organisation, request_case, request_createdBy, request_createdOn) VALUES ('4', '".$_GET['case']."', '".$_SESSION['userName']."', NOW());";
	if ($mysqli->query($query3) === TRUE) {
		$query4 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query3)."');";
		if ($mysqli->query($query4) === TRUE) {
			header("Location: home_".$_SESSION['userOrganisation'].".php");
		}
		else {
			$error = "Record add failed (log):\n".$mysqli->error;
		}
	}
	else {
		$error = "Record add failed (case):\n".$mysqli->error;
	}
	$mysqli->close();
}

echo "<html>";
echo "<head><title>RABE send technical request</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | Send technical request</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label>".$row['case_id']."<br />";
echo "<label>Number: </label>".$row['case_number']."<br />";
echo "<label>Name: </label>".$row['case_name']."<br />";
echo "<label>Description: </label><br /><textarea readonly cols='75' rows='5' id='description' name='description'>".$row['case_description']."</textarea><br />";
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
        $locationType = "unknown";
}
echo "<p><label>Type: </label>".$locationType."<br />";
echo "<label>Name: </label>".$row['case_locationName']."<br />";
echo "<label>Address: </label>".$row['case_locationAddress']."<br />";
echo "<label>City: </label>".$row['case_locationCity']."</p>";

echo "<h3>Juridical:</h3>";
echo "<p><label>Comment: </label><br /><textarea readonly cols='75' rows='5' id='comment' name='comment'>".$row['case_juridicalComment']."</textarea><br />";
if ($row['case_juridicalDecision'] == '3') {
	$decision = "accepted";
	$warrant = "<img src='pictures/pdf_icon.png' alt='pdf' /> <a href='warrant_download.php?case=".$row['case_id']."'>warrant.pdf</a>";
}
elseif ($row['case_juridicalDecision'] == '4') {
	$decision = "rejected";
	$warrant = "no warrant";
}
else {
	$decision = "unknown";
	$warrant = "no warrant";
}
echo "<label>Decision: </label>".$decision."<br />";
echo "<label>Warrant: </label>".$warrant."<br />";
echo "<label>Decision by: </label>".$row['case_juridicalDecisionBy']."<br />";
echo "<label>Decision on: </label>".$row['case_juridicalDecisionOn']."</p>";

echo "<p><input name='submit' type='submit' value=' send '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
