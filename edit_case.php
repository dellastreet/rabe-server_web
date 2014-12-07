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
	if (empty($_POST['number']) || empty($_POST['description'])) {
		$error = "Please supply the case number and description (required)!";
	}
	else {
		$number=$_POST['number'];
		$name=$_POST['name'];
		$description=$_POST['description'];
		$path=$_POST['path'];
		
		$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
		if ($mysqli->connect_errno) {
        		printf("Connect failed: %s\n", $mysqli->connect_error());
        		exit();
		}
		$query1 = "UPDATE rabedb_case SET case_number = '".$number."', case_name = '".$name."', case_description = '".$description."', case_path = '".$path."' WHERE case_id = '".$_GET['case']."';";
		if ($mysqli->query($query1) === TRUE) {
			$query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
			if ($mysqli->query($query2) === TRUE) {
				header("Location: case.php?case=".$_GET['case']);
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
echo "<head><title>RABE edit case: ".$_GET['case']."</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | Edit case: ".$_GET['case']."</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label><input type='text' id='id' name='id' value='".$row['case_id']."' readonly></p>";
echo "<p><label>Number: </label><input type='text' id='number' name='number' value='".$row['case_number']."'></p>";
echo "<p><label>Name: </label><input type='text' id='name' name='name' value='".$row['case_name']."'></p>";
echo "<p><label>Description: </label><br /><textarea cols='75' rows='10' id='description' name='description'>".$row['case_description']."</textarea>";
echo "<p><label>Path: </label><input type='text' id='path' name='path' value='".$row['case_path']."'></p>";
echo "<p><label>Created by: </label><input type='text' id='createdBy' name='createdBy' value='".$row['case_createdBy']."' readonly></p>";
echo "<p><label>Created on: </label><input type='text' id='createdOn' name='createdOn' value='".$row['case_createdOn']."' readonly></p>";
echo "<p><input name='submit' type='submit' value=' save '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
