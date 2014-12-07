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
$query = "SELECT *, status_name FROM rabedb_request INNER JOIN rabedb_status ON request_status = status_id;";
$result = $mysqli->query($query);
while($row_r = $result->fetch_array(MYSQLI_ASSOC)) {
	$rows_r[] = $row_r;
}
$mysqli->close();

echo "<html>";
echo "<head><title>RABE all requests</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | All requests</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Requests:</label></p>";
echo "<table>";
if (empty($rows_r)) {
        echo "<tr>no open requests</tr>";
}
else {
	echo "<tr><th>ID</th><th>Status</th><th>Number</th><th>Name</th><th>Investigator:</th><th></th></tr>";
	foreach($rows_r as $row_r) {
		$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
		if ($mysqli->connect_errno) {
        		printf("Connect failed: %s\n", $mysqli->connect_error());
        		exit();
		}
		$query = "SELECT * FROM rabedb_case WHERE case_id = '".$row_r['request_case']."';";
		$result = $mysqli->query($query);
		$row_c = $result->fetch_array(MYSQLI_ASSOC);
		
		echo "<tr>";
		echo "<td>".$row_r['request_id']."</td><td>".$row_r['status_name']."</td><td>".$row_c['case_number']."</td><td>".$row_c['case_name']."</td><td>".$row_c['case_createdBy']."</td>";
		echo "<td><input type='button' onClick=javascript:location.href='request_".$_SESSION['userOrganisation'].".php?request=".$row_r['request_id']."&case=".$row_c['case_id']."' id='viewRequest' name='viewRequest' value=' view request '></td>";
		echo "</tr>";
	}
}

echo "</table>";
echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
