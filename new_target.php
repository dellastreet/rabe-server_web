<?php

session_start();

if ($_SESSION['userOrganisation'] != "1") {
        header("Location: access.php");
}

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
		$query1 = "INSERT INTO rabedb_target (target_case, target_number, target_ip, target_createdBy, target_createdOn) VALUES ('".$_GET['case']."', '".$number."', '".$ip."', '".$_SESSION['userName']."', NOW());";
		if ($mysqli->query($query1) === TRUE) {
			$targetId=$mysqli->insert_id;
			$query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
			if ($mysqli->query($query2) === TRUE) {
				header("Location: mkdir_target.php?case=".$_GET['case']."&target=".$targetId);
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
echo "<head><title>RABE new target</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | <a id='nav' href='case.php?case=".$_GET['case']."'>Case</a> | New target</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label><input type='text' id='caseId' name='caseId' value='".$_GET['case']."' readonly></p>";
echo "<p><label>Number: </label><input type='text' id='number' name='number'></p>";
echo "<p><label>IP Address: </label><input type='text' id='ip' name='ip'></p>";
echo "<p><input name='submit' type='submit' value=' create '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
