<?php
    if (!file_exists ("../config.inc.php")):
        die ('config.inc.php not found. Please check <a href="../README.php">README.php</a> file or read the configuration guide at <a href="http://doc.centresis.org">http://doc.centresis.org</a>'); endif;

require("../config.inc.php");
global $DatabaseServer, $DatabaseUsername, $DatabasePassword, $DatabaseName;

if(!function_exists('parse_mysql_dump')) :
function parse_mysql_dump(){
	 global $DatabaseServer, $DatabaseUsername, $DatabasePassword, $DatabaseName;
     // Load and explode the sql file
     /*$f = fopen('centre-db.sql',"r+");
     $sqlFile = fread($f,filesize('centre-db.sql'));
     $sqlArray = explode(';',$sqlFile);
           
     //Process the sql file by statements
     foreach ($sqlArray as $stmt) {
       if (strlen($stmt)>3){
            $result = mysql_query($stmt);
              if (!$result){
                 $sqlErrorCode = mysql_errno();
                 $sqlErrorText = mysql_error();
                 $sqlStmt      = $stmt;
                 break;
              }
           }
      }*/
	  
	$db = new PDO('mysql:host='.$DatabaseServer.';dbname='.$DatabaseName, $DatabaseUsername, $DatabasePassword);
	$sql = file_get_contents('centre-db.sql');
	$qr = $db->exec($sql);	
		  
}
endif;
	// check for db connection 
	$link = mysql_connect($DatabaseServer, $DatabaseUsername, $DatabasePassword);
	if (!$link) {
		die('Failed: Cannot establish connection. Please check your db configuration. <br>Could not connect: ' . mysql_error());
	}
	else {
		if(!defined('TBL_STAFF')) { define('TBL_STAFF', "staff"); }
		$mydb = @mysql_select_db($DatabaseName, $link);
		if (!$mydb) { die('Failed: Cannot establish connection. Unknown database "'.$DatabaseName.'"'); }
			if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".TBL_STAFF."'")) <= 0 ) { 
				//parse_mysql_dump(); 
				require("execimport.php");
				header( 'Location: ../success.html'); 
			}
			else { echo 'DATABASE TABLES are already setup. Please use the <a href="../index.php">Log In</a> area or read the configuration guide at <a href="http://doc.centresis.org">http://doc.centresis.org</a>'; }
				
	}

?>