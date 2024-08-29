<?php

function Config($item)
{	global $_CENTRE,$CentreTitle,$DefaultSyear;

	if(!$_CENTRE['Config'])
	{
		$QI=DBQuery("SELECT LOGIN,TITLE,SYEAR FROM config");
		$_CENTRE['Config'] = DBGet($QI);
		$_CENTRE['Config'][1]['TITLE'] = $CentreTitle;
		$_CENTRE['Config'][1]['SYEAR'] = $DefaultSyear;
	}

	return $_CENTRE['Config'][1][$item];
}

function CustomConfig($title)
{	global $_CENTRE,$CentreTitle,$DefaultSyear;

	if(!$_CENTRE['CustomConfig'])
	{
		$QI=DBQuery("SELECT DESCRIPTION FROM config WHERE TITLE = '$title'");
		$SD=DBGet($QI);
	}

	return $SD[1]['DESCRIPTION'];
}
?>