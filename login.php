<?php

session_start();

$error = "";

if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
		$error = "Username or password is invalid!";
	}
	else {
		$username=$_POST['username'];
		$password=md5($_POST['password']);
		
		$mysqli = new mysqli("localhost", "rabe", "ebar", "rabe_db");
		if ($mysqli->connect_errno) {
        		printf("Connect failed: %s\n", $mysqli->connect_error());
        		exit();
		}
		$query1 = "SELECT * FROM rabedb_user WHERE user_userName='".$username."' AND user_password = '".$password."';";
		$result = $mysqli->query($query1);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		
		$query2 = "INSERT INTO rabedb_log (log_by, log_on, log_content) VALUES ('RABE login', NOW(), '".str_replace("'", "", $query1)."');";
                $mysqli->query($query2);
		
		if (($username == $row['user_userName']) && ($password == $row['user_password'])) {
			$_SESSION['user'] = $row['user_initials']." ".$row['user_lastName'];
			$_SESSION['userName'] = $row['user_userName'];
			$_SESSION['userOrganisation'] = $row['user_organisation'];
			header("Location: home_".$row['user_organisation'].".php");
		}
		else {
			$error = "Username or password is invalid!";
		}
	$mysqli->close();
	}
}

echo "<html>";
echo "<head><title>RABE login</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "</div>";

echo "<div id='content'>";
echo "<h1>Login</h1>";

echo "<form action='' method='post'>";

echo "<p><label>Username:</label><br />";
echo "<input type='text' id='username' name='username'></p>";
echo "<p><label>Password:</label><br />";
echo "<input type='password' id='password' name='password'></p>";
echo "<p><input name='submit' type='submit' value=' login '></p>";
echo $error;

echo "</form>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
