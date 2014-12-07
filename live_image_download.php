<?php

session_start();

$file = "file:///mnt/cases/default_images/rabe-client-i386_0.6_dhcp.iso";

if (file_exists($file)) {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: '.filesize($file));
	readfile($file);
	exit;
}
else {
	echo "<link rel='stylesheet' type='text/css' href='rabe.css'>";
	echo "<body><p>File not found!</p></body>";
	
        header("Refresh: 2; url=home_".$_SESSION['userOrganisation'].".php");
}

?>
