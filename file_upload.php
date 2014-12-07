<?php

$target_dir = $filePath;
$target_file = $target_dir.basename($_FILES["fileToUpload"]["name"]);;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
$uploadOk = 1;

// check if file already exists
if (file_exists($target_file)) {
	echo "File already exists!";
	$uploadOk = 0;
}
// check file size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
	echo "File too large!";
	$uploadOk = 0;
}
// check file extension
if($fileType != $fileExtension) {
	echo "Only $fileExtension files allowed!";
	$uploadOk = 0;
}

// check for error and upload file
if ($uploadOk == 0) {
	echo "File not uploaded!";
}
else {
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_dir.$fileName)) {
		echo "File ".basename($_FILES["fileToUpload"]["name"])." uploaded!";
	}
	else {
		echo "Error uploading file!";
	}
}

?>
