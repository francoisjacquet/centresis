<?php
DrawHeader(ProgramTitle());

$message = '<TABLE><TR><TD colspan=7 align=center>From'.PrepareDate(DBDate(),'_min').' to '.PrepareDate(DBDate(),'_max').'</TD></TR></TABLE>';
if(Prompt('Confirm','When do you want to recalculate the daily attendance?',$message))
{
	$current_RET = DBGet(DBQuery("SELECT DISTINCT DATE_FORMAT(SCHOOL_DATE,'%Y-%m-%d') as SCHOOL_DATE FROM attendance_calendar WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"),array(),array('SCHOOL_DATE'));
	$students_RET = GetStuList();

	$begin = mktime(0,0,0,MonthNWSwitch($_REQUEST['month_min'],'to_num'),$_REQUEST['day_min']*1,$_REQUEST['year_min']) + 43200;
	$end = mktime(0,0,0,MonthNWSwitch($_REQUEST['month_max'],'to_num'),$_REQUEST['day_max']*1,$_REQUEST['year_max']) + 43200;

	for($i=$begin;$i<=$end;$i+=86400)
	{
		if($current_RET[strtoupper(date('Y-m-d',$i))])
		{
			foreach($students_RET as $student)
			{
				UpdateAttendanceDaily($student['STUDENT_ID'],date('Y-m-d',strtotime($i)));
			}
		}
	}
	
	unset($_REQUEST['modfunc']);
	DrawHeader('<IMG SRC=assets/check.gif>&nbsp;'._('The Daily Attendance for that timeframe has been recalculated.'));
}
?>