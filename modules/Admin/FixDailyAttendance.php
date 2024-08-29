<?php
DrawHeader(ProgramTitle());

$message = '<TABLE><TR><TD colspan=7 align=center>From'.PrepareDate(DBDate(),'_min').' to '.PrepareDate(DBDate(),'_max').'</TD></TR></TABLE>';
if(Prompt('Confirm','When do you want to recalculate the daily attendance?',$message))
{
	$current_RET = DBGet(DBQuery("SELECT DISTINCT DATE_FORMAT(SCHOOL_DATE,'%Y-%m-%d') as SCHOOL_DATE FROM attendance_calendar WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"),array(),array('SCHOOL_DATE'));
	$students_RET = GetStuList();

	$begin = strtotime($_REQUEST['year_min'].'-'.$_REQUEST['month_min'].'-'.$_REQUEST['day_min']);
	$end = strtotime($_REQUEST['year_max'].'-'.$_REQUEST['month_max'].'-'.$_REQUEST['day_max']);

	#echo "<br /><br />Begin: ".$begin.' -- '.date('Y-m-d',$begin).'<br />';
	#echo "End: ".$end.' -- '.date('Y-m-d',$end).'<br /><br />';

	for($i=$begin;$i<=$end;$i+=86400)
	{
		if($current_RET[strtoupper(date('Y-m-d',$i))])
		{
			foreach($students_RET as $student)
			{
				UpdateAttendanceDaily($student['STUDENT_ID'],date('Y-m-d',$i));
				#echo $student['STUDENT_ID'] . ' ' .date('Y-m-d',$i).' <br />';
			}
		}
	}
	
	unset($_REQUEST['modfunc']);
	DrawHeader('<IMG SRC=assets/check.gif>&nbsp;'._('The Daily Attendance for that timeframe has been recalculated.'));
}
?>