<?php
$_REQUEST['modname'] = "Notes/Notes.php"; 
$js_extra = "window.location.href = window.location.href.replace('Search.php','Notes.php');";

$modcat = 'Notes';
if(AllowUse($_REQUEST['modname']))
{
	echo "<SCRIPT language=javascript>".$js_extra."parent.help.location=\"Bottom.php?modcat=$modcat&modname=$_REQUEST[modname]\";</SCRIPT>";
	include("modules/$_REQUEST[modname]");
}
?>
