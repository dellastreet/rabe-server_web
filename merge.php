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
$query = "SELECT * FROM rabedb_targetTemp WHERE targetTemp_id = '".$_GET['targetTemp']."';";
$result = $mysqli->query($query);
$row_tt = $result->fetch_array(MYSQLI_ASSOC);
$query = "SELECT *, status_name FROM rabedb_target INNER JOIN rabedb_status ON target_status = status_id WHERE target_status != '1';";
$result = $mysqli->query($query);
while($row_t = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows_t[] = $row_t;
}
$mysqli->close();

$error = "";

if (isset($_POST['submit'])) {
	if (empty($_POST['merge'])) {
		$error = "Please select a target to merge with (required)!";
	}
	else {
		$ip=$row_tt['targetTemp_ip'];
                $ipVpn=$row_tt['targetTemp_ipVpn'];
		$mac=$row_tt['targetTemp_mac'];
                $blockDevices=$row_tt['targetTemp_blockDevices'];
		$diskInformation=$row_tt['targetTemp_diskInformation'];
		$targetId=$_POST['merge'];
		
                $mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
                if ($mysqli->connect_errno) {
                        printf("Connect failed: %s\n", $mysqli->connect_error());
                        exit();
                }
		
		$query1 = "UPDATE rabedb_target SET target_ip = '".$ip."', target_ipVpn = '".$ipVpn."', target_mac = '".$mac."', target_blockDevices = '".$blockDevices."', target_diskInformation = '".$diskInformation."' WHERE target_id = '".$targetId."';";
                if ($mysqli->query($query1) === TRUE) {
                        $query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query1)."');";
                        if ($mysqli->query($query2) === TRUE) {
                                echo "Record added (target)!";
                        }
                        else {
                                $error = "Record add failed (log):\n".$mysqli->error;
                        }
                }
                else {
                        $error = "Record add failed (target):\n".$mysqli->error;
                }
		
		$query3 = "UPDATE rabedb_targetTemp SET targetTemp_status = '1' WHERE targetTemp_id = '".$_GET['targetTemp']."';";
                if ($mysqli->query($query3) === TRUE) {
                        $query4 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query3)."');";
                        if ($mysqli->query($query4) === TRUE) {
				echo "Record added (targetTemp)!"; 
                        }
                        else {
                                $error = "Record add failed (log):\n".$mysqli->error;
                        }
                }
                else {
                        $error = "Record add failed (targetTemp):\n".$mysqli->error;
                }
		
		foreach (explode(",", $blockDevices) as $blockDevice) {
			$query5 = "INSERT INTO rabedb_targetConnect (targetConnect_target, targetConnect_blockDevice) VALUES ('".$targetId."', '".$blockDevice."');";
			if ($mysqli->query($query5) === TRUE) {
				$query6 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('".$_SESSION['userName']."', NOW(), '".str_replace("'", "", $query5)."');";
				if ($mysqli->query($query6) === TRUE) {
					header("Location: home_".$_SESSION['userOrganisation'].".php");
				}
				else {
					$error = "Record add failed (log):\n".$mysqli->error;
				}
			}
			else {
				$error = "Record add failed (targetConnect):\n".$mysqli->error;
			}
		}
		
		$mysqli->close();
	}
}

echo "<html>";
echo "<head><title>RABE merge</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "<div id='header_left'><br />".$_SESSION['user']."<br /><a href='logout.php'>logout</a></div>";
echo "<div id='header_right'><img id='user' src='pictures/".$_SESSION['userOrganisation'].".png' alt='user' /></div>";
echo "</div>";

echo "<div id='content'>";
echo "<h1><a id='nav' href='home_".$_SESSION['userOrganisation'].".php'>Home</a> | Merge</h1>";

echo "<form action='' method='post'>";

echo "<p><label>IP Address: </label>".$row_tt['targetTemp_ip']."<br />";
echo "<label>VPN IP Address: </label>".$row_tt['targetTemp_ipVpn']."<br />";
echo "<label>MAC Address: </label>".$row_tt['targetTemp_mac']."<br />";
echo "<label>Block Devices: </label>".$row_tt['targetTemp_blockDevices']."<br />";
echo "<label>Disk Information: </label><br /><textarea readonly cols='75' rows='5' id='diskInformation' name='diskInformation'>".$row_tt['targetTemp_diskInformation']."</textarea></p>";

echo "<p><label>With target:</label></p>";
echo "<table>";
if (empty($rows_t)) {
        echo "<tr>no targets</tr>";
}
else {
        echo "<tr><th>Select</th><th>Case ID</th><th>Status</th><th>Number</th><th>IP Address</th><th>VPN IP Address</th><th>MAC address</th><th>Block Devices</th></tr>";
        foreach($rows_t as $row_t) {
                echo "<tr>";
		echo "<td><input type='checkbox' id='merge' name='merge' value='".$row_t['target_id']."' /></td>";
                echo "<td>".$row_t['target_case']."</td><td>".$row_t['status_name']."</td><td>".$row_t['target_number']."</td><td>".$row_t['target_ip']."</td><td>".$row_t['target_ipVpn']."</td><td>".$row_t['target_mac']."</td><td>".$row_t['target_blockDevices']."</td>";
                echo "</tr>";
        }
}
echo "</table>";

echo "<p><input name='submit' type='submit' value=' merge '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
