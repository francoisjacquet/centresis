<?php
DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='remove')
{
	$Arr=$_REQUEST['st_arr'];
	echo '<style>.inside_cont { max-width: 475px; min-width: 175px; padding: 5px 40px 8px; } h4 { line-height: 16px; text-align:center; } span { display:block; text-align:left; } input[type="button"], input[type="submit"], input[type="reset"] { width:75px; } .otherinfo:before { content: "»"; } .otherinfo { font-size: 12px; line-height: 14px; margin-bottom: 8px; margin-left: 12px; margin-top: 5px; } .otherinfo b { font-weight: bold; font-size: 12px; line-height: 14px; } </style>';
	if(DeleteStudentPrompt('Student(s)','delete',$Arr))
	{
		if(count($_REQUEST['st_arr']))
		{

			foreach($_REQUEST['st_arr'] as $student_ids):
				$stud_fullname = getSTUDENTINFOLINEAR($student_ids);
				DBQuery("DELETE FROM students WHERE student_id='".$student_ids."'");
				DBQuery("DELETE FROM student_enrollment WHERE student_id='".$student_ids."'");
				DBQuery("DELETE FROM food_service_student_accounts WHERE student_id='".$student_ids."'");
				DBQuery("DELETE FROM schedule WHERE student_id='".$student_ids."'");
				DBQuery("DELETE FROM attendance_day WHERE student_id='".$student_ids."'");
				DBQuery("DELETE FROM attendance_period WHERE student_id='".$student_ids."'");
				DBQuery("DELETE FROM gradebook_grades WHERE student_id='".$student_ids."'");
				DBQuery("DELETE FROM student_report_card_grades WHERE student_id='".$student_ids."'");
				DBQuery("INSERT INTO student_delete_records (deleted_by_id, deleted_by_user, student_id, student_fullname, datetime) VALUES ('".User('STAFF_ID')."', '".User('USERNAME')."', '".$student_ids."', '".$stud_fullname."', CURRENT_TIMESTAMP)");

			endforeach;
			
		}
		
		unset($_REQUEST['modfunc']);
	}
}

if(!$_REQUEST['modfunc'])
{
	echo '<style>form .sub-header tr td:first-child { display:none; } </style>';

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=remove&include_inactive=$_REQUEST[include_inactive] method='POST'>";
		$extra['header_right'] = '<INPUT type=submit value=\'Delete Selected Students\'>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;
	//$extra['force_search'] = true;

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit value="'._('Delete Selected Students').'"></CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function DeleteStudentPrompt($title,$action='delete',$arr)
{
	$PHP_tmp_SELF = PreparePHP_SELF($_REQUEST,array('delete_ok'));

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',_('Confirm').(strpos($action,' ')===false?' '._(ucwords($action)):''));
		echo "<h4>".sprintf(_('Are you sure you want to %s <br>the selected %s?'),_($action),_($title))."</h4>";
			foreach($arr as $student_ids):
				echo '<span>'.getSTUDENTINFO($student_ids).'</span>';
				echo (getSTUDENTENROLLMENTREC($student_ids)) ? '<span class="otherinfo">'.getSTUDENTENROLLMENTREC($student_ids).'</span>' : '';
				echo (getSTUDENTATTPERIOD($student_ids)) ? '<span class="otherinfo">'.getSTUDENTATTPERIOD($student_ids).'</span>' : '';
				echo (getSTUDENTATTDAY($student_ids)) ? '<span class="otherinfo">'.getSTUDENTATTDAY($student_ids).'</span>' : '';
				echo (getSTUDENTREPORTCARD($student_ids)) ? '<span class="otherinfo">'.getSTUDENTREPORTCARD($student_ids).'</span>' : '';
				echo (getSTUDENTGRADEBOOK($student_ids)) ? '<span class="otherinfo">'.getSTUDENTGRADEBOOK($student_ids).'</span>' : '';
			endforeach;
		echo "<BR><CENTER><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><INPUT type=submit value=\""._('OK')."\"><INPUT type=button name=delete_cancel value=\""._('Cancel')."\" onclick='javascript:history.go(-1);'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}

function getSTUDENTINFO($student_id)
{
	$sql=DBGet(DBQuery("SELECT STUDENT_ID, CONCAT('<B>',LAST_NAME,',</B> ',FIRST_NAME) AS FULL_NAME FROM students WHERE STUDENT_ID='$student_id'"));
	return $sql[1]['FULL_NAME'];
}

function getSTUDENTINFOLINEAR($student_id)
{
	$sql=DBGet(DBQuery("SELECT STUDENT_ID, CONCAT(FIRST_NAME,' ',LAST_NAME) AS FULL_NAME FROM students WHERE STUDENT_ID='$student_id'"));
	return ucwords(strtolower($sql[1]['FULL_NAME']));
}

function getSTUDENTENROLLMENTREC($student_id)
{
	$sql=DBGet(DBQuery("SELECT COUNT(*) AS REC_NUM, STUDENT_ID FROM student_enrollment WHERE STUDENT_ID='$student_id'"));
	$rec_num = $sql[1]['REC_NUM'];
	$stu_id = $sql[1]['STUDENT_ID'];
	if($rec_num>1) :
		$rec_num -= 1;
		$ret = 'This student has <b style="border-bottom: 1px solid #000000; line-height: 17px;">'.$rec_num.' previous years</b> of enrollment records. Are you sure you want to delete the enrollment record for '.getSTUDENTINFO($stu_id).', student id '.$stu_id.'?';
	else : 
		$ret = 'Student has no previous year(s) of enrollment records.'; 
	endif;
	return $ret;
}

function getSTUDENTATTPERIOD($student_id)
{
	$sql=DBGet(DBQuery("SELECT COUNT(*) AS REC_NUM, STUDENT_ID FROM attendance_period WHERE STUDENT_ID='$student_id' GROUP BY period_id, course_period_id"));
	$rec_num = $sql[1]['REC_NUM'];
	$stu_id = $sql[1]['STUDENT_ID'];
	if($rec_num>0) :
		$ret = 'There are <b style="border-bottom: 1px solid #000000; line-height: 17px;">'.$rec_num.' attendance periods</b> for this student. Are you sure you want to delete the attendance period for '.getSTUDENTINFO($stu_id).', student id '.$stu_id.'?';
	else : 
		$ret = 'Student has no attendance period record.'; 
	endif;
	return $ret;
}

function getSTUDENTATTDAY($student_id)
{
	$sql=DBGet(DBQuery("SELECT COUNT(*) AS REC_NUM, STUDENT_ID FROM attendance_day WHERE STUDENT_ID='$student_id'"));
	$rec_num = $sql[1]['REC_NUM'];
	$stu_id = $sql[1]['STUDENT_ID'];
	if($rec_num>0) :
		$ret = 'There are <b style="border-bottom: 1px solid #000000; line-height: 17px;">'.$rec_num.' days of attendance</b> for this student. Are you sure you want to delete the attendance days for '.getSTUDENTINFO($stu_id).', student id '.$stu_id.'?';
	else : 
		$ret = 'Student has no daily attendance.'; 
	endif;
	return $ret;
}

function getSTUDENTREPORTCARD($student_id)
{
	$sql=DBGet(DBQuery("SELECT COUNT(*) AS REC_NUM, STUDENT_ID FROM student_report_card_grades WHERE STUDENT_ID='$student_id'"));
	$rec_num = $sql[1]['REC_NUM'];
	$stu_id = $sql[1]['STUDENT_ID'];
	if($rec_num>0) :
		$ret = 'There are <b style="border-bottom: 1px solid #000000; line-height: 17px;">'.$rec_num.' report card grades</b> for this student. Are you sure you want to delete the report card grades for '.getSTUDENTINFO($stu_id).', student id '.$stu_id.'?';
	else : 
		$ret = 'Student has no report card grades.'; 
	endif;
	return $ret;
}

function getSTUDENTGRADEBOOK($student_id)
{
	$sql=DBGet(DBQuery("SELECT COUNT(*) AS REC_NUM, STUDENT_ID FROM gradebook_grades WHERE STUDENT_ID='$student_id'"));
	$rec_num = $sql[1]['REC_NUM'];
	$stu_id = $sql[1]['STUDENT_ID'];
	if($rec_num>0) :
		$ret = 'There are <b style="border-bottom: 1px solid #000000; line-height: 17px;">'.$rec_num.' gradebook grades</b> for this student. Are you sure you want to delete the gradebook grades for '.getSTUDENTINFO($stu_id).', student id '.$stu_id.'?';
	else : 
		$ret = 'Student has no gradebook grades.'; 
	endif;
	return $ret;
}
?>