<?php

function BackPrompt($message)
{
	echo "<SCRIPT language=javascript>history.back();alert(\"$message\");</SCRIPT>";
	exit();
}
function BackPromptMsg($title, $message)
{
	echo "<SCRIPT language=javascript>alert(\"$message\");</SCRIPT>";
	return false;
}
?>