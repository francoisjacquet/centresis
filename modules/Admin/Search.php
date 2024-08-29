<?php
$_REQUEST['modname'] = "Admin/Schools.php?new_school=true"; 
$js_extra = "window.location.href = window.location.href.replace('Search.php','Schools.php?new_school=true');";

$modcat = 'Admin';
if(AllowUse($_REQUEST['modname']))
{
	echo "<SCRIPT language=javascript>".$js_extra."parent.help.location=\"Bottom.php?modcat=$modcat&modname=$_REQUEST[modname]\";</SCRIPT>";
	include("modules/$_REQUEST[modname]");
}
?>
