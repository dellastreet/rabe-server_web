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
	if (empty($_POST['number']) || empty($_POST['description']) || empty($_POST['locationType']) || empty($_POST['locationAddress']) || empty($_POST['locationCity'])) {
		$error = "Please supply the case number, description and location information (required)!";
	}
	else {
		$number=$_POST['number'];
                $name=$_POST['name'];
                $description=$_POST['description'];
		if ($_POST['locationType'] == 'company') {
			$locationType=2;
			$organisation=2;
		}
		elseif ($_POST['locationType'] == 'residence') {
                        $locationType=3;
			$organisation=2;
                }
		else {
			$locationType=NULL;
		}
		$locationName=$_POST['locationName'];
		$locationAddress=$_POST['locationAddress'];
		$locationCity=$_POST['locationCity'];
		
                $mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
                if ($mysqli->connect_errno) {
                        printf("Connect failed: %s\n", $mysqli->connect_error());
                        exit();
                }
                $query1 = "UPDATE rabedb_case SET case_status = 2, case_number = '".$number."', case_name = '".$name."', case_description = '".$description."', case_locationType = '".$locationType."', case_locationName = '".$locationName."', case_locationAddress = '".$locationAddress."', case_locationCity = '".$locationCity."' WHERE case_id = '".$_GET['case']."';";
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
		$query3 = "INSERT INTO rabedb_request (request_organisation, request_case, request_createdBy, request_createdOn) VALUES ('".$organisation."', '".$_GET['case']."', '".$_SESSION['userName']."', NOW());";
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
}

echo "<html>";
echo "<head><title>RABE send juridical request</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | Send juridical request</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label><input type='text' id='id' name='id' value='".$row['case_id']."' readonly></p>";
echo "<p><label>Number: </label><input type='text' id='number' name='number' value='".$row['case_number']."'></p>";
echo "<p><label>Name: </label><input type='text' id='name' name='name' value='".$row['case_name']."'></p>";
echo "<p><label>Description: </label><br /><textarea cols='75' rows='10' id='description' name='description'>".$row['case_description']."</textarea>";
echo "<p><label>Location type: </label><input type='radio' id='locationType' name='locationType' value='company'>company<input type='radio' id='locationType' name='locationType' value='residence'>residence</p>";
echo "<p><label>Location name: </label><input type='text' id='locationName' name='locationName' value='".$row['case_locationName']."'></p>";
echo "<p><label>Location address: </label><input type='text' id='locationAddress' name='locationAddress' value='".$row['case_locationAddress']."'></p>";
echo "<p><label>Location city: </label><input type='text' id='locationCity' name='locationCity' value='".$row['case_locationCity']."'></p>";
echo "<p><input name='submit' type='submit' value=' send '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
