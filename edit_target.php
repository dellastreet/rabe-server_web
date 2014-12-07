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
$query = "SELECT * FROM rabedb_target WHERE target_case = '".$_GET['case']."' AND target_id = '".$_GET['target']."';";
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$mysqli->close();

$error = "";

if (isset($_POST['submit'])) {
	if (empty($_POST['number']) || empty($_POST['ip'])) {
		$error = "Please supply the target number and IP address (required)!";
	}
	else {
		$number=$_POST['number'];
		$ip=$_POST['ip'];
		
		$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
		if ($mysqli->connect_errno) {
        		printf("Connect failed: %s\n", $mysqli->connect_error());
        		exit();
		}
		$query1 = "UPDATE rabedb_target SET target_number = '".$number."', target_ip = '".$ip."' WHERE target_case = '".$row['target_case']."' AND target_id = '".$row['target_id']."';";
		if ($mysqli->query($query1) === TRUE) {
			$query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
			if ($mysqli->query($query2) === TRUE) {
				header("Location: target.php?case=".$_GET['case']."&target=".$row['target_id']);
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
}

echo "<html>";
echo "<head><title>RABE edit target: ".$row['target_ip']."</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | <a id='nav' href='case.php?case=".$_GET['case']."'>Case</a> | Edit target: ".$row['target_ip']."</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label><input type='text' id='caseId' name='caseId' value='".$row['target_case']."' readonly></p>";
echo "<p><label>Target ID: </label><input type='text' id='id' name='id' value='".$row['target_id']."' readonly></p>";
echo "<p><label>Number: </label><input type='text' id='number' name='number' value='".$row['target_number']."'></p>";
echo "<p><label>IP: </label><input type='text' id='ip' name='ip' value='".$row['target_ip']."'></p>";
echo "<p><label>Created by: </label><input type='text' id='createdBy' name='createdBy' value='".$row['target_createdBy']."' readonly></p>";
echo "<p><label>Created on: </label><input type='text' id='createdOn' name='createdOn' value='".$row['target_createdOn']."' readonly></p>";
echo "<p><input name='submit' type='submit' value=' save '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
