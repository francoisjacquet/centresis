<?php
DrawHeader(ProgramTitle());
if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
	$start_date = $_REQUEST['year_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['day_start'];
else
	$start_date = date("Y").'-'.date("m").'-01';

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
	$end_date = date("Y-m-d", strtotime($_REQUEST['year_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['day_end']));
else
	$end_date = DBDate();
echo $start_date;
if($_REQUEST['modfunc']=='search')
{
	echo '<BR>';
	PopTable('header','Advanced');
	echo "<FORM name=percentform action=Modules.php?modname=$_REQUEST[modname]&list_by_day=$_REQUEST[list_by_day]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]&advanced=$_REQUEST[advanced] method=POST>";
	echo '<TABLE border=0>';

	echo '<TR valign=top><TD>';
	echo '<TABLE bgcolor=#f8f8f9 width=100% id=general_table>';
	Search('general_info',$extra['grades']);
	if(!isset($extra))
		$extra = array();
	Widgets('user',$extra);
	if($extra['search'])
		echo $extra['search'];
	Search('student_fields',is_array($extra['student_fields'])?$extra['student_fields']:array());
	echo '</TABLE>';
	echo '</TD><TD>';
	echo '<TABLE width=100%><TR><TD align=center><BR>';
	if($extra['search_second_col'])
		echo $extra['search_second_col'];
	if(User('PROFILE')=='admin')
		echo '<INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'><font color=black>'._('Search All Schools').'</font><BR>';
	//echo '<INPUT type=checkbox name=include_inactive value=Y><font color=black>Include Inactive Students</font><BR>';
	echo '<BR>';
	echo Buttons(_('Submit'),_('Reset'));
	echo '</TD></TR>';
	echo '</TABLE>';
	echo '</TD></TR>';

	echo '<TR valign=top><TD><TABLE cellpadding=0 cellspacing=0 width=100%>';
	if($_REQUEST['advanced']=='Y')
	{
		$extra['search'] = '';
		Widgets('all',$extra);
		echo '<TR><TD>';
		echo '<FONT COLOR='.Preferences('HEADER').'><B>'._('Widgets').'</B></FONT><BR>';
		echo $extra['search'];
		echo '</TD></TR>';

		echo '<TR><TD>';
		echo '<FONT COLOR='.Preferences('HEADER').'><B>'._('Student Fields').'</B></FONT><BR>';
		Search('student_fields_all',is_array($extra['student_fields'])?$extra['student_fields']:array());
		echo '</TD></TR>';
		echo '<TR><TD><BR><A href='.PreparePHP_SELF($_REQUEST,array(),array('advanced'=>'N')).'>'._('Basic Search').'</A></TD></TR>';
	}
	else
		echo '<TR><TD><BR><A href='.PreparePHP_SELF($_REQUEST,array(),array('advanced'=>'Y')).'>'._('Advanced Search').'</A></TD></TR>';
	echo '</TABLE></TD>';
	echo '</TR>';

	echo '</TABLE>';
	echo '</FORM>';
	// set focus to last name text box
        echo '<script type="text/javascript"><!--
		document.percentform.last.focus();
		--></script>';
	PopTable('footer');
}

if(!$_REQUEST['modfunc'])
{
	if(!isset($extra))
		$extra = array();
	Widgets('user');
	if($_REQUEST['advanced']=='Y')
		Widgets('all');
	$extra['WHERE'] .= appendSQL('');
	$extra['WHERE'] .= CustomFields('where');

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&list_by_day=$_REQUEST[list_by_day] method=POST>";
	$advanced_link = " <A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=search&list_by_day=$_REQUEST[list_by_day]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]>Advanced</A>";
	DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($start_date))),'_start').' - '.PrepareDate(strtoupper(date("Y-m-d",strtotime($end_date))),'_end').$advanced_link,'<INPUT type=submit value=Go>');
	echo '</FORM>';
	if($_CENTRE['SearchTerms'])
		DrawHeader(str_replace('<BR>','<BR> &nbsp;',substr($_CENTRE['SearchTerms'],0,-4)));

	if($_REQUEST['list_by_day']=='true')
	{
		$cal_days = 1;

		$student_days_absent = DBGet(DBQuery("SELECT ad.SCHOOL_DATE,ssm.GRADE_ID,COALESCE(sum(ad.STATE_VALUE-1)*-1,0) AS STATE_VALUE FROM attendance_day ad,STUDENT_ENROLLMENT ssm,STUDENTS s".$extra['FROM']." WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra['WHERE']." GROUP BY ad.SCHOOL_DATE,ssm.GRADE_ID"),array(''),array('SCHOOL_DATE','GRADE_ID'));
		$student_days_possible = DBGet(DBQuery("SELECT ac.SCHOOL_DATE,ssm.GRADE_ID,'' AS DAYS_POSSIBLE,count(*) AS ATTENDANCE_POSSIBLE,count(*) AS STUDENTS,'' AS PRESENT,'' AS ABSENT,'' AS ADA,'' AS AVERAGE_ATTENDANCE,'' AS AVERAGE_ABSENT FROM STUDENT_ENROLLMENT ssm,ATTENDANCE_CALENDAR ac,STUDENTS s".$extra['FROM']." WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ac.SYEAR=ssm.SYEAR AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SCHOOL_ID=ac.SCHOOL_ID AND (ac.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ac.SCHOOL_DATE)) AND ac.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' ".$extra['WHERE']." GROUP BY ac.SCHOOL_DATE,ssm.GRADE_ID"),array('SCHOOL_DATE'=>'ProperDate','GRADE_ID'=>'GetGrade','STUDENTS'=>'_makeByDay','PRESENT'=>'_makeByDay','ABSENT'=>'_makeByDay','ADA'=>'_makeByDay','AVERAGE_ATTENDANCE'=>'_makeByDay','AVERAGE_ABSENT'=>'_makeByDay','DAYS_POSSIBLE'=>'_makeByDay'));

		$columns = array('SCHOOL_DATE'=>_('Date'),'GRADE_ID'=>_('Grade'),'STUDENTS'=>_('Students'),'DAYS_POSSIBLE'=>_('Days Possible'),'PRESENT'=>_('Present'),'ABSENT'=>_('Absent'),'ADA'=>_('ADA'),'AVERAGE_ATTENDANCE'=>_('Average Attendance'),'AVERAGE_ABSENT'=>_('Average Absent'));

		ListOutput($student_days_possible,$columns,'.','.',$link);
	}
	else
	{
		$cal_days = DBGet(DBQuery("SELECT count(*) AS COUNT,CALENDAR_ID FROM attendance_calendar WHERE ".($_REQUEST['_search_all_schools']!='Y'?"SCHOOL_ID='".UserSchool()."' AND ":'')." SYEAR='".UserSyear()."' AND SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' GROUP BY CALENDAR_ID"),array(),array('CALENDAR_ID'));
		$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID,TITLE FROM attendance_calendars WHERE SYEAR='".UserSyear()."' ".($_REQUEST['_search_all_schools']!='Y'?" AND SCHOOL_ID='".UserSchool()."'":'')),array(),array('CALENDAR_ID'));

		$extra['WHERE'] .= " GROUP BY ssm.GRADE_ID,ssm.CALENDAR_ID";

		$student_days_absent = DBGet(DBQuery("SELECT ssm.GRADE_ID,ssm.CALENDAR_ID,COALESCE(sum(ad.STATE_VALUE-1)*-1,0) AS STATE_VALUE FROM attendance_day ad,STUDENT_ENROLLMENT ssm,STUDENTS s".$extra['FROM']." WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra['WHERE']),array(''),array('GRADE_ID','CALENDAR_ID'));
		$student_days_possible = DBGet(DBQuery("SELECT ssm.GRADE_ID,ssm.CALENDAR_ID,'' AS DAYS_POSSIBLE,count(*) AS ATTENDANCE_POSSIBLE,count(*) AS STUDENTS,'' AS PRESENT,'' AS ABSENT,'' AS ADA,'' AS AVERAGE_ATTENDANCE,'' AS AVERAGE_ABSENT FROM STUDENT_ENROLLMENT ssm,ATTENDANCE_CALENDAR ac,STUDENTS s".$extra['FROM']." WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ac.SYEAR=ssm.SYEAR AND ac.CALENDAR_ID=ssm.CALENDAR_ID AND ".($_REQUEST['_search_all_schools']!='Y'?"ssm.SCHOOL_ID='".UserSchool()."' AND ":'')." ssm.SCHOOL_ID=ac.SCHOOL_ID AND (ac.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ac.SCHOOL_DATE)) AND ac.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' ".$extra['WHERE']),array('GRADE_ID'=>'_make','STUDENTS'=>'_make','PRESENT'=>'_make','ABSENT'=>'_make','ADA'=>'_make','AVERAGE_ATTENDANCE'=>'_make','AVERAGE_ABSENT'=>'_make','DAYS_POSSIBLE'=>'_make'));

		$columns = array('GRADE_ID'=>_('Grade'),'STUDENTS'=>_('Students'),'DAYS_POSSIBLE'=>_('Days Possible'),'PRESENT'=>_('Present'),'ABSENT'=>_('Absent'),'ADA'=>_('ADA'),'AVERAGE_ATTENDANCE'=>_('Average Attendance'),'AVERAGE_ABSENT'=>_('Average Absent'));
		$link['add']['html'] = array('GRADE_ID'=>'<b>'._('Total').'</b>','STUDENTS'=>round($sum['STUDENTS'],1),'DAYS_POSSIBLE'=>$cal_days[key($cal_days)][1]['COUNT'],'PRESENT'=>$sum['PRESENT'],'ADA'=>Percent(($sum['PRESENT'])/($sum['PRESENT']+$sum['ABSENT'])),'ABSENT'=>$sum['ABSENT'],'AVERAGE_ATTENDANCE'=>round($sum['AVERAGE_ATTENDANCE'],1),'AVERAGE_ABSENT'=>round($sum['AVERAGE_ABSENT'],1));

		ListOutput($student_days_possible,$columns,'.','.',$link);
	}
}

function _make($value,$column)
{	global $THIS_RET,$student_days_absent,$cal_days,$sum,$calendars_RET;

	switch($column)
	{
		case 'STUDENTS':
			$sum['STUDENTS'] += $value/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'];
			return round($value/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'],1);
		break;

		case 'DAYS_POSSIBLE':
			return $cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'];
		break;

		case 'PRESENT':
			$sum['PRESENT'] += ($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']);
			return $THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'];
		break;
        case 'ABSENT':
			$sum['ABSENT'] += ($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']);
			return $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'];
		break;

		case 'ADA':
			return Percent((($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']))/$THIS_RET['STUDENTS']);
		break;

		case 'AVERAGE_ATTENDANCE':
			$sum['AVERAGE_ATTENDANCE'] += (($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'])/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT']);
			return round(($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'])/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'],1);
		break;

		case 'AVERAGE_ABSENT':
			$sum['AVERAGE_ABSENT'] += ($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT']);
			return round($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'],1);
		break;

		case 'GRADE_ID':
			return GetGrade($value).(count($cal_days)>1?' - '.$calendars_RET[$THIS_RET['CALENDAR_ID']][1]['TITLE']:'');
	}
}

function _makeByDay($value,$column)
{	global $THIS_RET,$student_days_absent,$cal_days,$sum;

	switch($column)
	{
		case 'STUDENTS':
			$sum['STUDENTS'] += $value/$cal_days;
			return round($value/$cal_days,1);
		break;

		case 'DAYS_POSSIBLE':
			return $cal_days;
		break;

		case 'PRESENT':
			$sum['PRESENT'] += ($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']);
			return $THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'];
		break;

		case 'ABSENT':
			$sum['ABSENT'] += ($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']);
			return $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'];
		break;

		case 'ADA':
			return Percent((($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']))/$THIS_RET['STUDENTS']);
		break;

		case 'AVERAGE_ATTENDANCE':
			$sum['AVERAGE_ATTENDANCE'] += (($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'])/$cal_days);
			return round(($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'])/$cal_days,1);
		break;

		case 'AVERAGE_ABSENT':
			$sum['AVERAGE_ABSENT'] += ($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']/$cal_days);
			return round($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']/$cal_days,1);
		break;
	}
}
?>
