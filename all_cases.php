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
$query = "SELECT *, status_name FROM rabedb_case INNER JOIN rabedb_status ON case_status = status_id;";
$result = $mysqli->query($query);
while($row_c = $result->fetch_array(MYSQLI_ASSOC)) {
	$rows_c[] = $row_c;
}
$mysqli->close();

echo "<html>";
echo "<head><title>RABE all cases</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | All cases</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Cases:</label></p>";
echo "<table>";
if (empty($rows_c)) {
        echo "<tr>no open cases</tr>";
}
else {
	echo "<tr><th>ID</th><th>Status</th><th>Number</th><th>Name</th><th></th><th></th><th></th></tr>";
	foreach($rows_c as $row_c) {
		echo "<tr>";
		echo "<td>".$row_c['case_id']."</td><td>".$row_c['status_name']."</td><td>".$row_c['case_number']."</td><td>".$row_c['case_name']."</td>";
		echo "<td><input type='button' onClick=javascript:location.href='case.php?case=".$row_c['case_id']."' id='viewCase' name='viewCase' value=' view case '></td>";
		switch ($row_c['case_status']) {
			case 0:
				echo "<td><input type='button' onClick=javascript:location.href='edit_case.php?case=".$row_c['case_id']."' id='editCase' name='editCase' value=' edit case '></td>";
				echo "<td><input type='button' onClick=javascript:location.href='request_juridical.php?case=".$row_c['case_id']."' id='sendRequest' name='sendRequest' value=' send juridical request '></td>";
				break;
			case 2:
				echo "<td>not editable</td><td>request sent</td>";
				break;
			case 3:
				echo "<td>not editable</td>";
				echo "<td><input type='button' onClick=javascript:location.href='request_technical.php?case=".$row_c['case_id']."' id='sendRequest' name='sendRequest' value=' send technical request '></td>";
				break;
			case 4:
				echo "<td><input type='button' onClick=javascript:location.href='edit_case.php?case=".$row_c['case_id']."' id='editCase' name='editCase' value=' edit case '></td>";
				echo "<td><input type='button' onClick=javascript:location.href='request_juridical.php?case=".$row_c['case_id']."' id='sendRequest' name='sendRequest' value=' send juridical request '></td>";
				break;
			default:
				echo "<td colspan='2'>not editable</td>";
		}
		echo "</tr>";
	}
}

echo "</table>";

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
