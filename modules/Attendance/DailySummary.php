<?php

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	while(!VerifyDate($start_date = $_REQUEST['year_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['day_start']))
		$_REQUEST['day_start']--;
}
else
	$start_date = strtoupper(date('Y-m')).'-01';

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
{
	while(!VerifyDate($end_date = date("Y-m-d", strtotime($_REQUEST['year_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['day_end']))))
		$_REQUEST['day_end']--;
}
else
	$end_date = DBDate();

DrawHeader(ProgramTitle());

if($_REQUEST['attendance'] && $_POST['attendance'] && AllowEdit())
{
	foreach($_REQUEST['attendance'] as $student_id=>$values)
	{
		foreach($values as $school_date=>$columns)
		{
			$sql = "UPDATE ATTENDANCE_PERIOD SET ADMIN='Y',";

			foreach($columns as $column=>$value)
				$sql .= $column."='".str_replace("\'","''",$value)."',";

			$sql = substr($sql,0,-1) . " WHERE SCHOOL_DATE='".$school_date."' AND PERIOD_ID='".$_REQUEST['period_id']."' AND STUDENT_ID='".$student_id."'";
			DBQuery($sql);
			UpdateAttendanceDaily($student_id,date("Y-m-d", strtotime($school_date)));
		}
	}
	$current_RET = DBGet(DBQuery("SELECT ATTENDANCE_TEACHER_CODE,ATTENDANCE_CODE,ATTENDANCE_REASON,STUDENT_ID,ADMIN,COURSE_PERIOD_ID FROM attendance_period WHERE SCHOOL_DATE='".$date."'"),array(),array('STUDENT_ID','COURSE_PERIOD_ID'));
	unset($_REQUEST['attendance']);
}

if($_REQUEST['search_modfunc'] || $_REQUEST['student_id'] || UserStudentID() || User('PROFILE')=='parent' || User('PROFILE')=='student')
{
	$PHP_tmp_SELF = PreparePHP_SELF();
	$period_select = "<SELECT name=period_id onchange='this.form.submit();'><OPTION value=\"\">"._('Daily')."</OPTION>";
	if(!UserStudentID() && !$_REQUEST['student_id'])
	{
		if(User('PROFILE')=='admin')
		{
			$periods_RET = DBGet(DBQuery("SELECT sp.PERIOD_ID,sp.TITLE FROM SCHOOL_PERIODS sp WHERE sp.SYEAR='".UserSyear()."' AND sp.SCHOOL_ID='".UserSchool()."' AND (SELECT count(1) FROM COURSE_PERIODS WHERE ( position(',0,' IN DOES_ATTENDANCE)>0 OR position('Y' IN DOES_ATTENDANCE)>0 ) AND PERIOD_ID=sp.PERIOD_ID AND SYEAR=sp.SYEAR AND SCHOOL_ID=sp.SCHOOL_ID)>0 ORDER BY sp.SORT_ORDER"));
			foreach($periods_RET as $period)
				$period_select .= "<OPTION value=".$period['PERIOD_ID'].(($_REQUEST['period_id']==$period['PERIOD_ID'])?' SELECTED':'').">".$period['TITLE'].'</OPTION>';
		}
		else
		{
			$periods_RET = DBGet(DBQuery("SELECT sp.PERIOD_ID,sp.TITLE FROM SCHOOL_PERIODS sp,COURSE_PERIODS cp WHERE ( position(',0,' IN cp.DOES_ATTENDANCE)>0 OR position('Y' IN cp.DOES_ATTENDANCE)>0 ) AND sp.PERIOD_ID=cp.PERIOD_ID AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'"));
			if($periods_RET)
			{
				$period_select .= "<OPTION value=".$periods_RET[1]['PERIOD_ID'].(($_REQUEST['period_id']==$periods_RET[1]['PERIOD_ID'] || !isset($_REQUEST['period_id']))?' SELECTED':'').">".$periods_RET[1]['TITLE'].'</OPTION>';
				if(!isset($_REQUEST['period_id']))
					$_REQUEST['period_id'] = $periods_RET['PERIOD_ID'];
			}
		}
	}
	else
		$period_select .= '<OPTION value="PERIOD"'.($_REQUEST['period_id']?' SELECTED':'').'>'._('By Period').'</OPTION>';
	$period_select .= '</SELECT>';
	echo "<FORM action=$PHP_tmp_SELF method=POST>";
	DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($start_date))),'_start').' - '.PrepareDate(strtoupper(date("Y-m-d",strtotime($end_date))),'_end').' : '.$period_select.' : <INPUT type=submit value=Go>');
}

$cal_RET = DBGet(DBQuery("SELECT DISTINCT SCHOOL_DATE,CONCAT('_',DATE_FORMAT(SCHOOL_DATE,'%Y%m%d')) AS SHORT_DATE FROM attendance_calendar WHERE SCHOOL_ID='".UserSchool()."' AND SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' ORDER BY SCHOOL_DATE"));

if(UserStudentID() || $_REQUEST['student_id'] || User('PROFILE')=='parent')
{
	// JUST TO SET USERSTUDENTID()
	Search('student_id');
	if($_REQUEST['period_id'])
	{
		$sql = "SELECT
				cp.TITLE as COURSE_PERIOD,sp.TITLE as PERIOD,cp.PERIOD_ID
			FROM
				SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp
			WHERE
				s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
				AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND cp.PERIOD_ID = sp.PERIOD_ID AND ( position(',0,' IN cp.DOES_ATTENDANCE)>0 OR position('Y' IN cp.DOES_ATTENDANCE)>0 )
				AND s.SYEAR = c.SYEAR AND cp.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).")
				AND s.STUDENT_ID='".UserStudentID()."' AND s.SYEAR='".UserSyear()."'
				AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR s.END_DATE IS NULL)
			ORDER BY sp.SORT_ORDER
			";
		$schedule_RET = DBGet(DBQuery($sql));

		$sql = "SELECT ap.SCHOOL_DATE,ap.PERIOD_ID,ac.SHORT_NAME,ac.STATE_CODE,ac.DEFAULT_CODE FROM attendance_period ap,ATTENDANCE_CODES ac WHERE ap.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND ap.ATTENDANCE_CODE=ac.ID AND ap.STUDENT_ID='".UserStudentID()."'";
		$attendance_RET = DBGet(DBQuery($sql),array(),array('SCHOOL_DATE','PERIOD_ID'));
	}
	else
	{
		$schedule_RET[1] = array('COURSE_PERIOD'=>'Daily Attendance','PERIOD_ID'=>'0');
		$attendance_RET = DBGet(DBQuery("SELECT ad.SCHOOL_DATE,'0' AS PERIOD_ID,ad.STATE_VALUE AS STATE_CODE,".db_case(array('ad.STATE_VALUE',"'0.0'","'A'","'1.0'","'P'","'H'"))." AS SHORT_NAME FROM attendance_day ad WHERE ad.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND ad.STUDENT_ID='".UserStudentID()."'"),array(),array('SCHOOL_DATE','PERIOD_ID'));
	}

	$i = 0;
	if(count($schedule_RET))
	{
		foreach($schedule_RET as $course)
		{
			$i++;
			$student_RET[$i]['TITLE'] = $course['COURSE_PERIOD'];
			foreach($cal_RET as $value)
				$student_RET[$i][$value['SHORT_DATE']] = _makePeriodColor($attendance_RET[$value['SCHOOL_DATE']][$course['PERIOD_ID']][1]['SHORT_NAME'],$attendance_RET[$value['SCHOOL_DATE']][$course['PERIOD_ID']][1]['STATE_CODE'],$attendance_RET[$value['SCHOOL_DATE']][$course['PERIOD_ID']][1]['DEFAULT_CODE']);
		}
	}

	$columns = array('TITLE'=>'Course');
	if(count($cal_RET))
	{
		foreach($cal_RET as $value)
			$columns[$value['SHORT_DATE']] = ShortDate($value['SCHOOL_DATE']);
	}

	ListOutput($student_RET,$columns,_('Course'),_('Courses'));
}
else
{
	// in pre-2.11 versions the attendance data would be queried for all students here but data for #students*#days can be a lot
	// in 2.11 this was switched to incremental query in the _makeColor function
	if(!$_REQUEST['period_id'])
	{
		$att_sql = "SELECT ad.STATE_VALUE,SCHOOL_DATE,CONCAT('_',DATE_FORMAT(ad.SCHOOL_DATE,'%Y%m%d')) AS SHORT_DATE FROM attendance_day ad,STUDENT_ENROLLMENT ssm WHERE ad.STUDENT_ID=ssm.STUDENT_ID AND (('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR ssm.END_DATE IS NULL) AND '".DBDate()."'>=ssm.START_DATE) AND ssm.SCHOOL_ID='".UserSchool()."' AND ad.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND ad.STUDENT_ID=";
	}
	else
	{
		$att_sql = "SELECT ap.ATTENDANCE_CODE,ap.SCHOOL_DATE,CONCAT('_',DATE_FORMAT(ap.SCHOOL_DATE,'%Y%m%d')) AS SHORT_DATE FROM attendance_period ap,STUDENT_ENROLLMENT ssm WHERE ap.STUDENT_ID=ssm.STUDENT_ID AND ap.SCHOOL_DATE BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND '".date("Y-m-d",strtotime($end_date))."' AND ap.PERIOD_ID='$_REQUEST[period_id]' AND ap.STUDENT_ID=";
	}

	if(count($cal_RET))
	{
		foreach($cal_RET as $value)
		{
			$extra['SELECT'] .= ",'' as _".str_replace('-','',$value['SCHOOL_DATE']);
			$extra['columns_after']['_'.str_replace('-','',$value['SCHOOL_DATE'])] = ShortDate($value['SCHOOL_DATE']);
			$extra['functions']['_'.str_replace('-','',$value['SCHOOL_DATE'])] = '_makeColor';
		}
	}
	$extra['link']['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[next_modname]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]&period_id=$_REQUEST[period_id]";
	$extra['link']['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');

	Widgets('course');
	Widgets('absences');

	$extra['new'] = true;
	Search('student_id',$extra);
	echo '</FORM>';
}

function _makeColor($value,$column)
{	global $THIS_RET,$att_RET,$att_sql,$attendance_codes;

	if(!$att_RET[$THIS_RET['STUDENT_ID']])
		$att_RET[$THIS_RET['STUDENT_ID']] = DBGet(DBQuery($att_sql.$THIS_RET['STUDENT_ID']),array(),array('SHORT_DATE'));

	if($_REQUEST['period_id'])
	{
		if(!$attendance_codes)
			$attendance_codes = DBGet(DBQuery("SELECT ID,DEFAULT_CODE,STATE_CODE,SHORT_NAME FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='0'"),array(),array('ID'));

		$ac = $att_RET[$THIS_RET['STUDENT_ID']][$column][1]['ATTENDANCE_CODE'];
		if($attendance_codes[$ac][1]['DEFAULT_CODE']=='Y')
			return "<TABLE bgcolor=#00FF00 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>".makeCodePulldown($ac,$THIS_RET['STUDENT_ID'],$column)."</TD></TR></TABLE>";
		elseif($attendance_codes[$ac][1]['STATE_CODE']=='P')
			return "<TABLE bgcolor=#0000FF cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>".makeCodePulldown($ac,$THIS_RET['STUDENT_ID'],$column)."</TD></TR></TABLE>";
		elseif($attendance_codes[$ac][1]['STATE_CODE']=='A')
			return "<TABLE bgcolor=#FF0000 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>".makeCodePulldown($ac,$THIS_RET['STUDENT_ID'],$column)."</TD></TR></TABLE>";
		elseif($attendance_codes[$ac][1]['STATE_CODE']=='H')
			return "<TABLE bgcolor=#FFCC00 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>".makeCodePulldown($ac,$THIS_RET['STUDENT_ID'],$column)."</TD></TR></TABLE>";
		elseif($ac)
			return "<TABLE bgcolor=#FFFF00 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>".makeCodePulldown($ac,$THIS_RET['STUDENT_ID'],$column)."</TD></TR></TABLE>";
	}
	else
	{
		$ac = $att_RET[$THIS_RET['STUDENT_ID']][$column][1]['STATE_VALUE'];
		if($ac=='0.0')
			return "<TABLE bgcolor=#FF0000 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>A</TD></TR></TABLE>";
		elseif($ac > 0 && $ac < 1)
			return "<TABLE bgcolor=#FFCC00 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>H</TD></TR></TABLE>";
		elseif($ac == 1)
			return "<TABLE bgcolor=#00FF00 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>P</TD></TR></TABLE>";
		else
			return "<TABLE bgcolor=#00FF00 cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>P</TD></TR></TABLE>";
	}
}

function _makePeriodColor($name,$state_code,$default_code)
{
	if($state_code=='A' || $state_code=='0.0')
		$color = '#FF0000';
	elseif($default_code=='Y' || $state_code=='1.0')
		$color='#00FF00';
	elseif($state_code=='P' || is_numeric($state_code))
		$color = '#FFCC00';
	elseif($state_code=='T')
		$color = '#0000FF';

	if($color) // && $state_code!='1.0')
		return "<TABLE bgcolor=$color cellpadding=0 cellspacing=0 width=10 class=LO_field><TR><TD>$name</TD></TR></TABLE>";
	else
		return false;
}

function makeCodePulldown($value,$student_id,$date)
{	global $THIS_RET,$attendance_codes,$_CENTRE;

	$date = substr($date,1,4).'-'.substr($date,5,2).'-'.substr($date,7);

	if(!$_CENTRE['code_options'])
	{
		foreach($attendance_codes as $id=>$code)
			$_CENTRE['code_options'][$id] = $code[1]['SHORT_NAME'];
	}

	return SelectInput($value,'attendance['.$student_id.']['.$date.'][ATTENDANCE_CODE]','',$_CENTRE['code_options']);
}
?>
