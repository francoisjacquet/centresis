<?php
$tables = array('SCHOOL_PERIODS'=>_('School Periods'),'SCHOOL_MARKING_PERIODS'=>_('Marking Periods'),'REPORT_CARD_GRADES'=>_('Report Card Grade Codes'),'REPORT_CARD_COMMENTS'=>_('Report Card Comment Codes'),'ELIGIBILITY_ACTIVITIES'=>_('Eligibility Activity Codes'),'ATTENDANCE_CODES'=>_('Attendance Codes'),'ATTENDANCE_CALENDARS'=>_('Attendance Calendars'),'ATTENDANCE_CALENDAR'=>_('Attendance Calendar'),'SCHOOL_GRADELEVELS'=>_('Grade Levels'));

$table_list = '<TABLE align=left>';
foreach($tables as $table=>$name)
{
	$table_list .= '<TR><TD><INPUT type=checkbox value=Y name=tables['.$table.'] CHECKED></TD><TD>'.$name.'</TD></TR>';
}
$table_list .= '</TABLE></CENTER><BR><small>New School\'s Title</small> <INPUT type=text name=title value="New School"><CENTER>';

DrawHeader(ProgramTitle());

if(Prompt(_('Confirm Copy School'),sprintf(_('Are you sure you want to copy the data for %s to a new school?'),GetSchool(UserSchool())),$table_list))
{
	if(count($_REQUEST['tables']))
	{	
		$next_id = DBGet(DBQuery("SELECT ID+1 AS NEWID FROM SCHOOLS ORDER BY ID DESC LIMIT 1"));
		//$id = DBGet(DBQuery("SELECT ".db_nextval('SCHOOLS')." AS ID".FROM_DUAL));
		$id = $next_id[1]['NEWID'];
		$COPIED_RET = DBGet(DBQuery("SELECT ADDRESS,CITY,STATE,ZIPCODE,PHONE,PRINCIPAL,WWW_ADDRESS,SCHOOL_NUMBER,SHORT_NAME FROM SCHOOLS WHERE id='".UserSchool()."'"));
		DBQuery("INSERT INTO SCHOOLS (ID,SYEAR,TITLE,ADDRESS,CITY,STATE,ZIPCODE,PHONE,PRINCIPAL,WWW_ADDRESS,SCHOOL_NUMBER,SHORT_NAME) values('$id','".UserSyear()."','".str_replace("\'","''",$_REQUEST['title'])."','".$COPIED_RET[1]['ADDRESS']."','".$COPIED_RET[1]['CITY']."','".$COPIED_RET[1]['STATE']."','".$COPIED_RET[1]['ZIPCODE']."','".$COPIED_RET[1]['PHONE']."','".$COPIED_RET[1]['PRINCIPAL']."','".$COPIED_RET[1]['WWW_ADDRESS']."','".$COPIED_RET[1]['SCHOOL_NUMBER']."','')");
		// removed copied short_name - set defaults to NULL ($COPIED_RET[1]['SHORT_NAME']) -- backup post/field name
		DBQuery("UPDATE staff SET SCHOOLS=CONCAT(SUBSTRING(SCHOOLS, 1, CHAR_LENGTH(SCHOOLS) - 1),',$id,') WHERE STAFF_ID='".User('STAFF_ID')."' AND SCHOOLS IS NOT NULL");
		foreach($_REQUEST['tables'] as $table=>$value)
			_rollover($table);
	}
	echo '<FORM action=Modules.php?modname='.$_REQUEST['modname'].' method=POST>';
	echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
    DrawHeader('<IMG SRC=assets/check.gif>'.sprintf(_('The data have been copied to a new school called "%s".'),$_REQUEST['title']),'<INPUT type=submit value="'._('OK').'>"');
	echo '</FORM>';
	unset($_SESSION['_REQUEST_vars']['tables']);
	unset($_SESSION['_REQUEST_vars']['delete_ok']);
}

function _rollover($table)
{	global $id;

	switch($table)
	{
		case 'SCHOOL_PERIODS':
			$SPERIOD_RET = DBGet(DBQuery("SELECT SYEAR,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,PERIOD_ID FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
			DBQuery("INSERT INTO SCHOOL_PERIODS (SYEAR,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,ROLLOVER_ID) VALUES ('".UserSyear()."','".$id."','".$SPERIOD_RET[1]['SORT_ORDER']."','".$SPERIOD_RET[1]['TITLE']."','".$SPERIOD_RET[1]['SHORT_NAME']."','".$SPERIOD_RET[1]['LENGTH']."','".$SPERIOD_RET[1]['ATTENDANCE']."','".$SPERIOD_RET[1]['ROLLOVER_ID']."')");
		
			//DBQuery("INSERT INTO SCHOOL_PERIODS (PERIOD_ID,SYEAR,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,ROLLOVER_ID) SELECT nextval('SCHOOL_PERIODS_SEQ'),SYEAR,'$id' AS SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,PERIOD_ID FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'SCHOOL_GRADELEVELS':
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SCHOOL_ID' && $column!='NEXT_GRADE_ID')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SCHOOL_ID".$columns.") SELECT '$id' AS SCHOOL_ID".$columns." FROM $table WHERE SCHOOL_ID='".UserSchool()."'");
		break;

		case 'SCHOOL_MARKING_PERIODS':
			DBQuery("INSERT INTO SCHOOL_MARKING_PERIODS (PARENT_ID,SYEAR,MP,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT PARENT_ID,SYEAR,MP,'$id' AS SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_MARKING_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("UPDATE SCHOOL_MARKING_PERIODS AS school_marking_periods INNER JOIN SCHOOL_MARKING_PERIODS AS mp ON (mp.SYEAR=school_marking_periods.SYEAR AND mp.SCHOOL_ID=school_marking_periods.SCHOOL_ID AND mp.ROLLOVER_ID=school_marking_periods.PARENT_ID) SET school_marking_periods.PARENT_ID=mp.MARKING_PERIOD_ID WHERE school_marking_periods.SYEAR='".UserSyear()."' AND school_marking_periods.SCHOOL_ID='$id'");

		break;

		case 'REPORT_CARD_GRADES':
			DBQuery("INSERT INTO REPORT_CARD_GRADE_SCALES (SYEAR,SCHOOL_ID,TITLE,COMMENT,HR_GPA_VALUE,HHR_GPA_VALUE,SORT_ORDER,ROLLOVER_ID) SELECT SYEAR,'$id',TITLE,COMMENT,HR_GPA_VALUE,HHR_GPA_VALUE,SORT_ORDER,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			$RCC_ID = DBGet(DBQuery("SELECT rca.ID AS rID FROM REPORT_CARD_GRADE_SCALES rca INNER JOIN REPORT_CARD_GRADES rcb ON rca.ROLLOVER_ID=rcb.GRADE_SCALE_ID AND rca.SCHOOL_ID='$id'")); $RCC_ID = $RCC_ID[1]['rID'];
			
			DBQuery("INSERT INTO REPORT_CARD_GRADES (SYEAR,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,GRADE_SCALE_ID,SORT_ORDER) SELECT SYEAR,'$id',TITLE,COMMENT,BREAK_OFF,GPA_VALUE,'$RCC_ID',SORT_ORDER FROM REPORT_CARD_GRADES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'REPORT_CARD_COMMENTS':
			DBQuery("INSERT INTO REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,CATEGORY_ID,COURSE_ID) SELECT SYEAR,'$id',TITLE,SORT_ORDER,NULL,NULL FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ELIGIBILITY_ACTIVITIES':
		case 'ATTENDANCE_CODES':
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR' && $column!='SCHOOL_ID')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SYEAR,SCHOOL_ID".$columns.") SELECT SYEAR,'$id' AS SCHOOL_ID".$columns." FROM $table WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ATTENDANCE_CALENDARS':
			DBQuery("INSERT INTO ATTENDANCE_CALENDARS (SYEAR,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,ROLLOVER_ID) SELECT SYEAR,'$id',TITLE,DEFAULT_CALENDAR,CALENDAR_ID FROM attendance_calendars WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;
		case 'ATTENDANCE_CALENDAR':
			$CAL_RET = DBGet(DBQuery("SELECT CALENDAR_ID AS LATESTID FROM attendance_calendars ORDER BY CALENDAR_ID DESC LIMIT 1"));
			DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,BLOCK,CALENDAR_ID) SELECT SYEAR,'$id',SCHOOL_DATE,MINUTES,BLOCK,'".$CAL_RET[1]['LATESTID']."' FROM attendance_calendar WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;
	}
}
?>
