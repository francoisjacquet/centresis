<?php
if(User('PROFILE')=='teacher')
	$_REQUEST['modname'] = 'Eligibility/EnterEligibility.php';
else
	$_REQUEST['modname'] = 'Eligibility/Student.php';

$modcat = 'Eligibility';
if(AllowUse($_REQUEST['modname']))
{
	echo "<SCRIPT language=javascript>parent.help.location=\"Bottom.php?modcat=$modcat&modname=$_REQUEST[modname]\";</SCRIPT>";
	include("modules/$_REQUEST[modname]");
}
?>