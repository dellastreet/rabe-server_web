<?php

session_start();

if ($_SESSION['userOrganisation'] != "3") {
        header("Location: access.php");
}

$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error());
        exit();
}
$query = "SELECT request_status FROM rabedb_request WHERE request_id = '".$_GET['request']."';";
$result = $mysqli->query($query);
$row_r = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT * FROM rabedb_case WHERE case_id = '".$_GET['case']."';";
$result = $mysqli->query($query);
$row_c = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT * FROM rabedb_target WHERE target_case = '".$_GET['case']."'";
$result = $mysqli->query($query);
while($row_t = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows_t[] = $row_t;
}
$mysqli->close();

$error = "";

if (isset($_POST['submit'])) {
        if (empty($_POST['comment']) || empty($_POST['decision'])) {
                $error = "Please supply a comment and decision (required)!";
        }
        else {
                $comment=$_POST['comment'];
		if ($_POST['decision'] == 'accept') {
                        $decision=3;
                }
                elseif ($_POST['decision'] == 'reject') {
                        $decision=4;
                }
                else {
                        $decision=NULL;
                }		
		
		$filePath = $row_c['case_path']."meta/";
		$fileName = "warrant.pdf";
		$fileExtension = "pdf";
		include "file_upload.php";
		
                $mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
                if ($mysqli->connect_errno) {
                        printf("Connect failed: %s\n", $mysqli->connect_error());
                        exit();
                }
                $query1 = "UPDATE rabedb_case SET case_status = '".$decision."', case_juridicalComment = '".$comment."', case_juridicalDecision = '".$decision."', case_juridicalDecisionBy = '".$_SESSION['userName']."', case_juridicalDecisionOn = NOW() WHERE case_id = '".$_GET['case']."';";
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
		$query3 = "UPDATE rabedb_request SET request_status = '1' WHERE request_id = '".$_GET['request']."';";
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
                        $error = "Record add failed (request):\n".$mysqli->error;
                }
        $mysqli->close();
        }
}

echo "<html>";
echo "<head><title>RABE request: ".$_GET['request']."</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | Request: ".$_GET['request']."</h1>";

echo "<form action='' method='post' enctype='multipart/form-data'>";

echo "<p><label>Case ID: </label>".$row_c['case_id']."<br />";
echo "<label>Number: </label>".$row_c['case_number']."<br />";
echo "<label>Name: </label>".$row_c['case_name']."<br />";
echo "<label>Description: </label><br /><textarea readonly cols='75' rows='5' id='description' name='description'>".$row_c['case_description']."</textarea><br />";
echo "<label>Created by: </label>".$row_c['case_createdBy']."<br />";
echo "<label>Created on: </label>".$row_c['case_createdOn']."</p>";

echo "<h3>Targets:</h3>";
if (empty($rows_t)) {
        echo " no targets";
}
else {
        echo "<ol start='1'>";
        foreach($rows_t as $row_t) {
                echo "<li>".$row_t['target_ip']."</li>";
        }
        echo "</ol>";
}

echo "<h3>Location:</h3>";
if ($row_c['case_locationType'] == '2') {
	$locationType = "company";
}
elseif ($row_c['case_locationType'] == '3') {
	$locationType = "residence";
}
else {
	$locationType = "unknown";
}
echo "<p><label>Type: </label>".$locationType."<br />";
echo "<label>Name: </label>".$row_c['case_locationName']."<br />";
echo "<label>Address: </label>".$row_c['case_locationAddress']."<br />";
echo "<label>City: </label>".$row_c['case_locationCity']."</p>";

echo "<h3>Juridical:</h3>";
if ($row_r['request_status'] == '1') {
 	echo "<p><label>Comment: </label><br /><textarea readonly cols='75' rows='5' id='comment' name='comment'>".$row_c['case_juridicalComment']."</textarea><br />";
	if ($row_c['case_juridicalDecision'] == '3') {
        	$decision = "accepted";
		$warrant = "<img src='pictures/pdf_icon.png' alt='pdf' /> <a href='warrant_download.php?case=".$row_c['case_id']."'>warrant.pdf</a>";
	}
	elseif ($row_c['case_juridicalDecision'] == '4') {
        	$decision = "rejected";
		$warrant = "no warrant";
	}
	else {
        	$decision = "unknown";
		$warrant = "no warrant";
	}
	echo "<label>Decision: </label>".$decision."<br />";
	echo "<label>Warrant: </label>".$warrant."<br />";
	echo "<label>Decision by: </label>".$row_c['case_juridicalDecisionBy']."<br />";
	echo "<label>Decision on: </label>".$row_c['case_juridicalDecisionOn']."</p>";
}
else {
	echo "<p><label>Comment: </label><br /><textarea cols='75' rows='10' id='comment' name='comment'></textarea></p>";
	echo "<p><label>Decision: </label><input type='radio' id='decision' name='decision' value='accept'>accept<input type='radio' id='decision' name='decision' value='reject'>reject</p>";
	echo "<p><label>Warrant: </label><input type='file' id='fileToUpload' name='fileToUpload'></p>";
	echo "<p><input name='submit' type='submit' value=' submit '></p>";
	echo $error;
}

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
