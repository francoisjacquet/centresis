<?php
/**
 * The base configurations of the Centre/SIS.
 * @package Centre/SIS
 */

	// ** MySQL settings - You can get this info from your web host ** //
	/** The name of the database for Centre/SIS */
	
	/** MySQL hostname */
	$DatabaseServer = 'localhost';

	/** MySQL database username */
	$DatabaseUsername = 'username_here';

	/** MySQL database password */
	$DatabasePassword = 'password_here';

	/** The name of the database for Centre/SIS */
	$DatabaseName = 'database_name_here';

	/** Used for older Centre/SIS versions */
	$DatabasePort = ''; // leave this blank

	/** Enabling the Centre/SIS + Moodle integration */
	/** Read the manual instruction on how to setup moodle configuration */
	$Moodle = ''; // set '1' to activate

	/* That's all, stop editing! Happy managing. */
	define('CONFIG_INC',1);
	$IgnoreFiles = Array('.DS_Store','CVS','.svn');

	// Database Setup
	$DatabaseType = 'mysql';
	$DatabaseANSI = true;
	
	/** Absolute path to the Centre/SIS directory. */
	// Server Names and Paths
	$CentrePath = dirname(__FILE__).'/';
	putenv("HTMLDOC_NOCGI=1");
	$htmldocPath = '/usr/bin/htmldoc'; 	// empty string means htmldoc will not be called and reports will be rendered in htlm instead of pdf
	$htmldocAssetsPath = $CentrePath.'assets/';
	$StudentPicturesPath = 'assets/StudentPhotos/';
	$UserPicturesPath = 'assets/UserPhotos/';
	$FS_IconsPath = 'assets/FS_icons/';
	$MoodlePath = 'modules/Moodle/';

	$CentreTitle = 'Centre School Information System';
	$CentreAdmins = '1';	// can be list such as '1,23,50' - note, these should be id's in the DefaultSyear,
							// otherwise they can't login anyway
	$CentreNotifyAddress = '';
	$DefaultSyear = '2014';
	$CentreLocales = array('en_US');	// Add other languages you want to support here, ex: 'fr_FR', 'es_ES', 'it_IT', ...
							// Language packs can be obtained by sending an email to info@centresis.org
	$LocalePath = $staticpath.'locale'; // Path were the language packs are stored. You need to restart Apache at each change in this directory

	// You get a CentreInstallKey when registering you installation on the centresis.org website in the Centre Directory
	// This will enable access to online resources (documentation, newsgroup, translations, etc.) directly from within Centre
	$CentreInstallKey = '';

	$CentreModules = array(
		'Admin'=>true,
		'School_Setup'=>true,
		'Students'=>true,
		'Users'=>true,
		'Scheduling'=>true,
		'Grades'=>true,
		'Attendance'=>true,
		'Eligibility'=>true,
		'Food_Service'=>true,
		'Resources'=>false,
		'Discipline'=>false,
		'Student_Billing'=>false,
		'State_Reports'=>false,
		'Custom'=>true,
		'Notes'=>true
	);

	// If session isn't started, start it.
	if(!isset($SessionStart))
		$SessionStart = 1;
		
	// If Moodle is set
	if(isset($Moodle) && $Moodle == '1') {
		global $course_suffix, $user_suffix, $token;
		include $MoodlePath."hooks.php";
		$MoodleActive = '1';
	}

?>
