<?php
/*
Uploadify v3.1.0
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
//$targetFolder = '/sandbox/uploads'; // Relative to the root

if (!empty($_FILES)) {
    //$tempFile = $_FILES['Filedata']['tmp_name'];
    //$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
    //$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];

    // Validate the file type
    $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
    $fileParts = pathinfo($_FILES['Filedata']['name']);

    if (in_array($fileParts['extension'],$fileTypes)) {
    	# Insert to DB once upload successful
		require("../../../../config.inc.php");
		include("../../../../database.inc.php");
		global $DatabaseServer, $DatabaseUsername, $DatabasePassword, $DatabaseName;	
		$link = mysql_connect($DatabaseServer, $DatabaseUsername, $DatabasePassword);
		$mydb = @mysql_select_db($DatabaseName, $link);
		$logosql = "SELECT * FROM config WHERE title ='sitelogo'";
		$logosql = mysql_query($logosql);
		$do_exists = mysql_num_rows($logosql);
		if($do_exists>0):
			mysql_query("UPDATE config SET description='modules/Admin/includes/uploadify/uploads/".$_FILES["Filedata"]["name"]."' WHERE title='sitelogo'");
		else:
			mysql_query("INSERT INTO config (title, description) VALUES('sitelogo', 'modules/Admin/includes/uploadify/uploads/".$_FILES["Filedata"]["name"]."')");
		endif;

        move_uploaded_file($_FILES["Filedata"]["tmp_name"], "uploads/" . $_FILES["Filedata"]["name"]);
        echo '1';
    } else {
        echo 'Invalid file type.';
    }
}
?>