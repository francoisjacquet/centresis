<?php

function GetSyear($date)
{	global $_CENTRE;

	//$RET = DBGet(DBQuery("SELECT SYEAR FROM attendance_calendar WHERE SCHOOL_DATE = '$date' AND DEFAULT_CALENDAR='Y'"));
	//$RET = DBGet(DBQuery("SELECT SYEAR FROM SCHOOL_MARKING_PERIODS WHERE MP='FY' AND '".$date."' BETWEEN START_DATE AND END_DATE"));
	$RET = DBGet(DBQuery("SELECT max(SYEAR) AS SYEAR FROM SCHOOL_MARKING_PERIODS WHERE MP='FY' AND START_DATE<='".$date."'"));

	return $RET[1]['SYEAR'];
}
?>