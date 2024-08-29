<?php
	error_reporting(0);
	$staticpath = dirname(__FILE__).'/';

    if (file_exists ($staticpath."config.inc.php")) :
	require_once($staticpath."config.inc.php");
    $CentrePath = $staticpath;
	require_once("database.inc.php");
    
	// Check if Tables have been imported
	if(!defined('TBL_STAFF')) { define('TBL_STAFF', "staff"); }
	$res = db_fetch_row(DBQuery("SHOW TABLES LIKE '".TBL_STAFF."'"));
		if( count($res) <= 0 ):	header('Location: cs-admin/install.php'); endif;

	endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Centre/SIS &#8250; ReadMe</title>
	<link rel="stylesheet" href="cs-admin/css/install.css" type="text/css" />
</head>
<body>
<h1 id="logo">
	<?php echo (count($res)==1)?"<center><h1>Centre/SIS already INSTALLED</h1>Please go to <span><a href='index.php'>Admin Page</a></span><br><br></center>":""; ?>
	<center><a target="_blank" href="http://centresis.org/"><img alt="Centre/SIS" src="assets/themes/Modern/centre_logo.png" /></a>
	<br /> Version 4.0</center>
</h1>
<p style="text-align: center">Centre School Information System</p>

<h1>First Things First</h1>
<p>Welcome. Centre/SIS, the premier Open Source Student Information System,is designed to meet the needs of all stakeholder groups, administrators, teachers, parents and students. Centre/SIS is easily customizable, easy to use, and powerful, without relying on large resource expenditures.</p>
<p style="text-align: right">&#8212; Jack Miller</p>

<h1>Installation: Famous 4-minute install</h1>
<ol>
	<li>Unzip the package in an empty directory and upload everything.</li>
    <li>Open up <code>config.inc-sample.php</code> with a text editor like WordPad or similar and fill in your database connection details. Save the file as <code>config.inc.php</code> and upload it.</li>
	<li>Unzip the package in an empty directory and upload everything.</li>
	<li>Open <span class="file"><a href="cs-admin/install.php">cs-admin/install.php</a></span> in your browser. 
	</li>
	<li>Once the configuration file is set up, the installer will set up the tables needed for your site. If there is an error, double check your <code>config.inc.php</code> file, and try again. If it fails again, please go to the <a href="http://centresis.org" title="Centre/SIS support">support forums</a> with as much data as you can gather.</li>
	<li>If you did not provide a username nor password, it will be <code>admin</code> with password: <code>admin</code>.</li>
	<li>The installer should then send you to the <a href="index.php">login page</a>. Sign in with the username and password.</li>
</ol>

<h1>System Requirements</h1>
<ul>
	<li><a target="_blank" href="http://php.net/">PHP</a> version <strong>5.2.4</strong> or higher.</li>
	<li><a target="_blank" href="http://www.mysql.com/">MySQL</a> version <strong>5.0</strong> or higher.</li>
</ul>

<h2>System Recommendations</h2>
<ul>
	<li>The <a target="_blank" href="http://wiki.processmaker.com/index.php/Migrating_from_Windows_to_Linux/UNIX">lower_case_table_names</a> MySQL: Case Insensitive.</li>
	<li>The <a target="_blank" href="http://www.htmldoc.org/">htmldoc</a> HTMLDoc.</li>
	<li>The <a target="_blank" href="http://php.net/manual/en/mcrypt.setup.php">mcrypt</a> PHP: Mcrypt.</li>
</ul>

<h1>License</h1>
<p>Centre/SIS is free software, and is released under the terms of the <abbr title="GNU General Public License">GPL</abbr> version 2 or (at your option) any later version. See <a target="_blank" href="license.txt">license.txt</a>.</p>

</body>
</html>
