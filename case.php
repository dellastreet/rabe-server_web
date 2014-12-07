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
$row_c = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT *, status_name FROM rabedb_target INNER JOIN rabedb_status ON target_status = status_id WHERE target_case = '".$_GET['case']."';";
$result = $mysqli->query($query);
while($row_t = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows_t[] = $row_t;
}
$mysqli->close();

echo "<html>";
echo "<head><title>RABE case: ".$_GET['case']."</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | Case: ".$_GET['case']."</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Case ID: </label>".$row_c['case_id']."<br />";
echo "<label>Number: </label>".$row_c['case_number']."<br />";
echo "<label>Name: </label>".$row_c['case_name']."<br />";
echo "<label>Description: </label><br /><textarea readonly cols='75' rows='5' id='description' name='description'>".$row_c['case_description']."</textarea><br />";
echo "<label>Path: </label>".$row_c['case_path']."<br />";
echo "<label>Created by: </label>".$row_c['case_createdBy']."<br />";
echo "<label>Created on: </label>".$row_c['case_createdOn']."</p>";

echo "<h3>Location:</h3>";
if ($row_c['case_locationType'] == '2') {
	$locationType = "company";
}
elseif ($row_c['case_locationType'] == '3') {
	$locationType = "residence";
}
else {
	$locationType = "";
}
echo "<p><label>Type: </label>".$locationType."<br />";
echo "<label>Name: </label>".$row_c['case_locationName']."<br />";
echo "<label>Address: </label>".$row_c['case_locationAddress']."<br />";
echo "<label>City: </label>".$row_c['case_locationCity']."</p>";

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
	$decision = "";
	$warrant = "";
}
echo "<label>Decision: </label>".$decision."<br />";
echo "<label>Warrant: </label>".$warrant."<br />";
echo "<label>Decision by: </label>".$row_c['case_juridicalDecisionBy']."<br />";
echo "<label>Decision on: </label>".$row_c['case_juridicalDecisionOn']."</p>";

echo "<h3>Technical:</h3>";
echo "<p><label>Comment: </label><br /><textarea readonly cols='75' rows='5' id='comment' name='comment'>".$row_c['case_technicalComment']."</textarea><br />";
echo "<label>Comment by: </label>".$row_c['case_technicalCommentBy']."<br />";
echo "<label>Comment on: </label>".$row_c['case_technicalCommentOn']."</p>";


echo "<h3>Targets:</h3>";
echo "<table>";
if (empty($rows_t)) {
	echo "<tr>no targets</tr>";
}
else {
	echo "<tr><th>ID</th><th>Status</th><th>Number</th><th>IP Address</th><th>VPN IP Address</th><th>MAC address</th><th>Block Devices</th><th></th><th></th></tr>";
	foreach($rows_t as $row_t) {
		echo "<tr>";
		echo "<td>".$row_t['target_id']."</td><td>".$row_t['status_name']."</td><td>".$row_t['target_number']."</td><td>".$row_t['target_ip']."</td><td>".$row_t['target_ipVpn']."</td><td>".$row_t['target_mac']."</td><td>".$row_t['target_blockDevices']."</td>";
		echo "<td><input type='button' onClick=javascript:location.href='target.php?case=".$row_t['target_case']."&target=".$row_t['target_id']."' id='viewTarget' name='viewTarget' value=' view target '></td>";
		echo "<td><input type='button' onClick=javascript:location.href='edit_target.php?case=".$row_t['target_case']."&target=".$row_t['target_id']."' id='editTarget' name='editTarget' value=' edit target '></td>";
		echo "</tr>";
	}
}
echo "</table>";

echo "<p><input type='button' onClick=javascript:location.href='new_target.php?case=".$_GET['case']."' id='newTarget' name='newTarget' value=' new target '></p>";

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
