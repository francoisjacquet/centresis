<?php

function GetMP($mp,$column='TITLE')
{	global $_CENTRE;

	// mab - need to translate marking_period_id to title to be useful as a function call from dbget
	// also, it doesn't make sense to ask for same thing you give
	if($column=='MARKING_PERIOD_ID')
		$column='TITLE';

	if(!$_CENTRE['GetMP'])
	{
		$_CENTRE['GetMP'] = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,MP,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM SCHOOL_MARKING_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"),array(),array('MARKING_PERIOD_ID'));
	}
	if(substr($mp,0,1)=='E')
	{
		if($column=='TITLE' || $column=='SHORT_NAME')
			$suffix = ' Exam';
		$mp = substr($mp,1);
	}

	if($mp==0 && $column=='TITLE')
		return 'Full Year'.$suffix;
	else
		return $_CENTRE['GetMP'][$mp][1][$column].$suffix;
}

function GetCurrentMP($mp,$date,$error=true)
{	global $_CENTRE;

	if(!$_CENTRE['GetCurrentMP'][$date][$mp])
	 	$_CENTRE['GetCurrentMP'][$date][$mp] = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_MARKING_PERIODS WHERE MP='$mp' AND '$date' BETWEEN START_DATE AND END_DATE AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));

	if($_CENTRE['GetCurrentMP'][$date][$mp][1]['MARKING_PERIOD_ID'])
		return $_CENTRE['GetCurrentMP'][$date][$mp][1]['MARKING_PERIOD_ID'];
	elseif($error)
		ErrorMessage(array(_('You are not currently in a marking period')),'fatal');
}
?>