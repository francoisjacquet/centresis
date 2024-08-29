<?php
if(CONFIG_INC==0)
{
	define('CONFIG_INC',1);
	// IgnoreFiles should contain any names of files or folders
	// which should be ignored by the function inclusion system.
	$IgnoreFiles = Array('.DS_Store','CVS','.svn');

	// Database Setup
	$DatabaseType = 'postgres';		// oracle, postgres
	$DatabaseANSI = true;			// ANSI compliant flag.
	$DatabaseServer = 'localhost';	// postgres = host, oracle=SID
	$DatabaseUsername = 'username';
	$DatabasePassword = 'password';
	$DatabaseName = 'centre';
	$DatabasePort = '5432';

	// Server Names and Paths
	$CentrePath = dirname(__FILE__).'/';
	$htmldocPath = '/usr/bin/htmldoc';	// empty string means htmldoc will not be called and reports will be rendered in htlm instead of pdf
	$htmldocAssetsPath = $CentrePath.'assets/';
	$StudentPicturesPath = 'assets/StudentPhotos/';
	$UserPicturesPath = 'assets/UserPhotos/';
	$FS_IconsPath = 'assets/FS_icons/';

	$CentreTitle = 'Centre School Information System';
	$SchoolLogo = '';               // needs to be an image file located in the assets directory, ie. 'MySchool.jpg'
	$CentreAdmins = '1';			// can be list such as '1,23,50' - note, these should be id's in the DefaultSyear,
							// otherwise they can't login anyway
	$CentreNotifyAddress = 'root@localhost';
	$DefaultSyear = '2010';
    date_default_timezone_set('America/Denver');
	$CentreLocales = array('en_US');	// Add other languages you want to support here, ex: 'fr_FR', 'es_ES', 'it_IT', ...
							// Language packs can be obtained by sending an email to info@centresis.org
	$LocalePath = $staticpath.'locale'; // Path were the language packs are stored. You need to restart Apache at each change in this directory

	// You get a CentreInstallKey when registering you installation on the centresis.org website in the Centre Directory
	// This will enable access to online resources (documentation, newsgroup, translations, etc.) directly from within Centre
	$CentreInstallKey = '';

	$CentreModules = array(
		'School_Setup'=>true,
		'Students'=>true,
		'Users'=>true,
		'Scheduling'=>true,
		'Grades'=>true,
		'Attendance'=>true,
		'Eligibility'=>true,
		'Food_Service'=>true,
		'Resources'=>true,
		'Discipline'=>false,
		'Student_Billing'=>false,
		'State_Reports'=>false,
		'Library'=>false,
		'Custom'=>false
	);

	// If session isn't started, start it.
	if(!isset($SessionStart))
		$SessionStart = 1;
}
?>
