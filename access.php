<?php

session_start();

if (isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
}
else {
	$user = "unauthorized user";
}

echo "<html>";
echo "<head><title>RABE access denied</title></head>";
echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
echo "<body>";

echo "<div id='header'>";
echo "</div>";

echo "<div id='content'>";
echo "<h2>Access denied for ".$user."!</h2>";

echo "</div>";
echo "</body>";
echo "</html>";

session_destroy();

header("Refresh: 3; url=login.php");

?>
