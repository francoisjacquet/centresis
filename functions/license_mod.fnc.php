<?php
function curl_get_file_contents($URL){
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_URL, $URL);
	$contents = curl_exec($c);
	curl_close($c);
	if ($contents) {
		return $contents;
	} else {
		return false;
	}
}

function CheckLicense() {
	if (!get_option('CSIS_v4_active') >0 || !get_option('CSIS_v4_licensekey') >0) {
		echo 'This software is not yet activated, please enter the license key to activate Centre/SIS.';
		exit;
	}
}

function CheckLicenseAdminMod($LicKey, $LicDomain, $Modname) {
	$LicFile='http://my.centresis.org/licences/'.$LicKey.'.xml';
	$LicContents = curl_get_file_contents($LicFile);
	if ($LicContents) {
	#if (@fopen($LicFile, "r")) {
		#$LicDomain = $LicDomain;
		#$LicContents = file_get_contents($LicFile);
		if ((!strstr($LicContents, $LicDomain)) || (!strstr($LicContents, $Modname))) {			
			$issue = 1;
		} else {
			$issue = 3;
		}
	} else {
		$issue = 2;
	}
  return $issue;	
}

function CheckStatesMod($LicKey) {
	$LicFile='http://my.centresis.org/licences/'.$LicKey.'.xml';
	$LicContents = curl_get_file_contents($LicFile);
	$xml = new SimpleXMLElement($LicContents);
	return (string)$xml->centresisv4->states;
}
?>