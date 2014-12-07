<?php

session_start();

if ($_SESSION['userOrganisation'] != "4") {
        header("Location: access.php");
}

$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error());
        exit();
}
$query = "SELECT request_id, request_status FROM rabedb_request WHERE request_id = '".$_GET['request']."';";
$result = $mysqli->query($query);
$row_r = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT * FROM rabedb_case WHERE case_id = '".$_GET['case']."';";
$result = $mysqli->query($query);
$row_c = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT *, status_name FROM rabedb_target INNER JOIN rabedb_status ON target_status = status_id WHERE target_case = '".$_GET['case']."';";
$result = $mysqli->query($query);
while($row_t = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows_t[] = $row_t;
}
$mysqli->close();

$error = "";

if (isset($_POST['submit'])) {
        if (empty($_POST['comment'])) {
                $error = "Please supply a comment (required)!";
        }
        else {
		$comment=$_POST['comment'];
		
                $mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
                if ($mysqli->connect_errno) {
                        printf("Connect failed: %s\n", $mysqli->connect_error());
                        exit();
                }
                $query1 = "UPDATE rabedb_case SET case_technicalComment = '".$comment."', case_technicalCommentBy = '".$_SESSION['userName']."', case_technicalCommentOn = NOW() WHERE case_id = '".$_GET['case']."';";
                if ($mysqli->query($query1) === TRUE) {
                        $query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
                        if ($mysqli->query($query2) === TRUE) {
                                header("Location: request_".$_SESSION['userOrganisation'].".php?request=".$row_r['request_id']."&case=".$row_c['case_id']."");
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

if (isset($_POST['submitCloseRequest'])) {
        if (empty($_POST['comment'])) {
		$error = "Please supply a comment (required)!";
        }
        else {
                $comment=$_POST['comment'];
		
                $mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
                if ($mysqli->connect_errno) {
                        printf("Connect failed: %s\n", $mysqli->connect_error());
                        exit();
                }
                $query1 = "UPDATE rabedb_case SET case_technicalComment = '".$comment."', case_technicalCommentBy = '".$_SESSION['userName']."', case_technicalCommentOn = NOW() WHERE case_id = '".$_GET['case']."';";
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

echo "<h3>Juridical:</h3>";
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

echo "<h3>Technical:</h3>";
if ($row_r['request_status'] == '1') {
	echo "<p><label>Comment: </label><br /><textarea readonly cols='75' rows='5' id='comment' name='comment'>".$row_c['case_technicalComment']."</textarea></p>";
	
	echo "<h3>Targets:</h3>";
	echo "<table>";
	if (empty($rows_t)) {
        	echo "<tr>no targets</tr>";
	}
	else {
        	echo "<tr><th>ID</th><th>Status</th><th>Number</th><th>IP Address</th><th></th><th></th></tr>";
        	foreach($rows_t as $row_t) {
                	echo "<tr>";
                	echo "<td>".$row_t['target_id']."</td><td>".$row_t['status_name']."</td><td>".$row_t['target_number']."</td><td>".$row_t['target_ip']."</td>";
			switch ($row_t['target_status']) {
                                case 0:
                                        echo "<td></td><td></td>";
                                        break;
                                case 3:
                                        echo "<td>accepted</td><td></td>";
                                        break;
                                case 4:
                                        echo "<td></td><td>rejected</td>";
                                        break;
                                default:
                                        echo "<td>accepted</td><td></td>";
                        }
                        echo "</tr>";
                }
        }
        echo "</table>";
}
else {
        echo "<p><label>Comment: </label><br /><textarea cols='75' rows='10' id='comment' name='comment'>".$row_c['case_technicalComment']."</textarea><br />";

        echo "<h3>Targets:</h3>";
        echo "<table>";
        if (empty($rows_t)) {
                echo "<tr>no targets</tr>";
        }
        else {
                echo "<tr><th>ID</th><th>Status</th><th>Number</th><th>IP Address</th><th></th><th></th><th></th></tr>";
                foreach($rows_t as $row_t) {
                        echo "<tr>";
                        echo "<td>".$row_t['target_id']."</td><td>".$row_t['status_name']."</td><td>".$row_t['target_number']."</td><td>".$row_t['target_ip']."</td>";
			switch ($row_t['target_status']) {
                                case 0:
                                        echo "<td><input type='button' onClick=javascript:location.href='set_accepted.php?request=".$row_r['request_id']."&case=".$row_c['case_id']."&target=".$row_t['target_id']."' id='setAccepted' name='setAccepted' value=' accept '></td>";
                                        echo "<td><input type='button' onClick=javascript:location.href='set_rejected.php?request=".$row_r['request_id']."&case=".$row_c['case_id']."&target=".$row_t['target_id']."' id='setRejected' name='setRejected' value=' reject '></td>";
                                        echo "<td></td>";
					break;
                                case 3:
                                        echo "<td>accepted</td>";
                                        echo "<td><input type='button' id='createLiveImage' name='createLiveImage' value=' create live image ' readonly></td>";
					echo "<td><input type='button' onClick=javascript:location.href='set_booted.php?request=".$row_r['request_id']."&case=".$row_c['case_id']."&target=".$row_t['target_id']."' id='setBooted' name='setBooted' value=' set booted '></td>";
					break;
                                case 4:
                                        echo "<td><input type='button' onClick=javascript:location.href='set_accepted.php?request=".$row_r['request_id']."&case=".$row_c['case_id']."&target=".$row_t['target_id']."' id='setAccepted' name='setAccepted' value=' accept '></td>";
                                        echo "<td>rejected</td>";
					echo "<td></td>";
                                        break;
                                default:
                                        echo "<td>accepted</td><td></td><td></td>";
                        }
                        echo "</tr>";
                }
        }
        echo "</table>";
        echo "<p><input name='submit' type='submit' value=' submit '><input name='submitCloseRequest' type='submit' value=' submit & close request '></p>";
        echo $error;
}

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
