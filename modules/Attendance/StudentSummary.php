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

//if(User('PROFILE')=='teacher')
//	$_REQUEST['period_id'] = UserPeriod();

if($_REQUEST['search_modfunc'] || UserStudentID() || $_REQUEST['student_id'] || User('PROFILE')=='parent' || User('PROFILE')=='student')
{
	if(!UserStudentID() && !$_REQUEST['student_id'])
	{
		$periods_RET = DBGet(DBQuery("SELECT sp.PERIOD_ID,sp.TITLE FROM SCHOOL_PERIODS sp WHERE sp.SYEAR='".UserSyear()."' AND sp.SCHOOL_ID='".UserSchool()."' AND EXISTS(SELECT '' FROM COURSE_PERIODS cp WHERE cp.PERIOD_ID=sp.PERIOD_ID AND (position(',0,' IN cp.DOES_ATTENDANCE)>0 OR position('Y' IN cp.DOES_ATTENDANCE)>0 )".(User('PROFILE')=='teacher'?" AND cp.PERIOD_ID='".UserPeriod()."'":'').") ORDER BY sp.SORT_ORDER"));
		$period_select = "<SELECT name=period_id onchange='this.form.submit();'><OPTION value=\"\">"._('Daily')."</OPTION>";
		if(count($periods_RET))
		{
			foreach($periods_RET as $period)
				$period_select .= "<OPTION value=$period[PERIOD_ID]".(($_REQUEST['period_id']==$period['PERIOD_ID'])?' SELECTED':'').">$period[TITLE]</OPTION>";
		}
		$period_select .= '</SELECT>';
	}

	$PHP_tmp_SELF = PreparePHP_SELF();
	echo "<FORM action=$PHP_tmp_SELF method=POST>";
	DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($start_date))),'_start').' - '.PrepareDate(strtoupper(date("Y-m-d",strtotime($end_date))),'_end').' : <INPUT type=submit value=Go>',$period_select);
	echo '</FORM>';
}

if($_REQUEST['period_id'])
{
	$extra['SELECT'] .= ",(SELECT count(*) FROM attendance_period ap,ATTENDANCE_CODES ac
						WHERE ac.ID=ap.ATTENDANCE_CODE AND (ac.STATE_CODE='A' OR ac.STATE_CODE='H') AND ap.STUDENT_ID=ssm.STUDENT_ID
						AND ap.PERIOD_ID='$_REQUEST[period_id]'
						AND ap.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND ac.SYEAR=ssm.SYEAR) AS STATE_ABS";

	$extra['columns_after']['STATE_ABS'] = _('State Abs');
	$codes_RET = DBGet(DBQuery("SELECT ID,TITLE FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='0' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL)"));
	if(count($codes_RET)>1)
	{
		foreach($codes_RET as $code)
		{
			$extra['SELECT'] .= ",(SELECT count(*) FROM attendance_period ap,ATTENDANCE_CODES ac
						WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.ID='$code[ID]' AND ap.PERIOD_ID='$_REQUEST[period_id]' AND ap.STUDENT_ID=ssm.STUDENT_ID
						AND ap.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."') AS ABS_$code[ID]";
			$extra['columns_after']["ABS_$code[ID]"] = $code['TITLE'];
		}
	}
}
else
{
	$extra['SELECT'] = ",(SELECT COALESCE((sum(STATE_VALUE-1)*-1),0.0) FROM attendance_day ad
						WHERE ad.STUDENT_ID=ssm.STUDENT_ID
						AND ad.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND ad.SYEAR=ssm.SYEAR) AS STATE_ABS";
	$extra['columns_after']['STATE_ABS'] = 'Days Abs';
}
$extra['link']['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]&period_id=$_REQUEST[period_id]";
$extra['link']['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');
//if((!$_REQUEST['search_modfunc'] || $_CENTRE['modules_search']) && !$_REQUEST['student_id'])
//	$extra['new'] = true;
/*
Widgets('activity');
Widgets('course');
Widgets('absences');
*/
Search('student_id',$extra);

if(UserStudentID())
{
	$name_RET = DBGet(DBQuery("SELECT CONCAT(FIRST_NAME,' ',COALESCE(MIDDLE_NAME,' '),' ',LAST_NAME) AS FULL_NAME FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	DrawHeader($name_RET[1]['FULL_NAME']);
	$PHP_tmp_SELF = PreparePHP_SELF();

	$absences_RET = DBGet(DBQuery("SELECT ap.STUDENT_ID,ap.PERIOD_ID,ap.SCHOOL_DATE,ac.SHORT_NAME,ac.STATE_CODE,ad.STATE_VALUE,ad.COMMENT AS OFFICE_COMMENT,ap.COMMENT AS TEACHER_COMMENT FROM attendance_period ap,ATTENDANCE_DAY ad,ATTENDANCE_CODES ac WHERE ap.STUDENT_ID=ad.STUDENT_ID AND ap.SCHOOL_DATE=ad.SCHOOL_DATE AND ap.ATTENDANCE_CODE=ac.ID AND (ac.DEFAULT_CODE!='Y' OR ac.DEFAULT_CODE IS NULL) AND ap.STUDENT_ID='".UserStudentID()."' AND ap.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND ad.SYEAR='".UserSyear()."' ORDER BY ap.SCHOOL_DATE"),array(),array('SCHOOL_DATE','PERIOD_ID'));
	foreach($absences_RET as $school_date=>$absences)
	{
		$i++;
		$days_RET[$i]['SCHOOL_DATE'] = ProperDate($school_date);
		$days_RET[$i]['DAILY'] = _makeStateValue($absences[key($absences)][1]['STATE_VALUE']);
		$days_RET[$i]['OFFICE_COMMENT'] = $absences[key($absences)][1]['OFFICE_COMMENT'];
		foreach($absences as $period_id=>$absence)
		{
			//$days_RET[$i][$period_id] =            $absence[1]['SHORT_NAME'];
			$days_RET[$i][$period_id] = _makeColor($absence[1]['SHORT_NAME'],$absence[1]['STATE_CODE']);
			$days_RET[$i]['COMMENT_'.$period_id] = $absence[1]['TEACHER_COMMENT'];
		}
	}

	//$periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,SHORT_NAME FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
	$periods_RET = DBGet(DBQuery("SELECT sp.PERIOD_ID,sp.SHORT_NAME FROM SCHOOL_PERIODS sp,SCHEDULE s,COURSE_PERIODS cp WHERE sp.SCHOOL_ID='".UserSchool()."' AND sp.SYEAR='".UserSyear()."' AND s.STUDENT_ID='".UserStudentID()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND position(',0,' IN cp.DOES_ATTENDANCE)>0 ORDER BY sp.SORT_ORDER"));
	$columns['SCHOOL_DATE'] = _('Date');
	$columns['DAILY'] = _('Present');
	$columns['OFFICE_COMMENT'] = _('Office Comment');
	foreach($periods_RET as $period)
	{
		$columns[$period['PERIOD_ID']] = $period['SHORT_NAME'];
		$columns['COMMENT_'.$period['PERIOD_ID']] = $period['SHORT_NAME'].' Comment';
	}
	ListOutput($days_RET,$columns,'Day','Days');
}

function _makeStateValue($value)
{	global $THIS_RET,$date;

	if($value=='0.0' || is_null($value))
		return 'None';
	elseif($value=='.5')
		return 'Half-Day';
	else
		return 'Full-Day';
}

function _makeColor($value,$state_code)
{
	$colors = array('P'=>'#00FF00','A'=>'#FF0000','H'=>'#FFCC00','T'=>'#0000FF');
	return '<TABLE'.($colors[$state_code]?' bgcolor='.$colors[$state_code]:'').' cellspacing=0 class=LO_field><TR><TD>'.$value.'</TD></TR></TABLE>';
}
?>
