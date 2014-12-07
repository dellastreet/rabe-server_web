<?php

session_start();

if ($_SESSION['userOrganisation'] != "1") {
        header("Location: access.php");
}

$error = "";

if (isset($_POST['submit'])) {
	if (empty($_POST['number']) || empty($_POST['description'])) {
		$error = "Please supply the case number and description (required)!";
	}
	else {
		$number=$_POST['number'];
		$name=$_POST['name'];
		$description=$_POST['description'];
		$path="/mnt/cases/".$number."/";
		
		$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
		if ($mysqli->connect_errno) {
        		printf("Connect failed: %s\n", $mysqli->connect_error());
        		exit();
		}
		$query1 = "INSERT INTO rabedb_case (case_number, case_name, case_description, case_path, case_createdBy, case_createdOn) VALUES ('".$number."', '".$name."', '".$description."', '".$path."', '".$_SESSION['userName']."', NOW());";
		if ($mysqli->query($query1) === TRUE) {
			$caseId=$mysqli->insert_id;
			$query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
			if ($mysqli->query($query2) === TRUE) {
				header("Location: mkdir_case.php?case=".$caseId);
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
echo "<head><title>RABE new case</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | New case</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Number: </label><input type='text' id='number' name='number'></p>";
echo "<p><label>Name: </label><input type='text' id='name' name='name'></p>";
echo "<p><label>Description: </label><br /><textarea cols='75' rows='10' id='description' name='description'></textarea>";
echo "<p><input name='submit' type='submit' value=' create '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
