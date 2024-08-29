<?php
$next_syear = UserSyear()+1;
$tables = array('SCHOOLS'=>_('Schools'),'STAFF'=>_('Users'),'SCHOOL_PERIODS'=>_('School Periods'),'SCHOOL_MARKING_PERIODS'=>_('Marking Periods'),'ATTENDANCE_CALENDARS'=>_('Calendars'),'ATTENDANCE_CODES'=>_('Attendance Codes'),'REPORT_CARD_GRADES'=>_('Report Card Grade Codes'),'COURSES'=>_('Courses').'<b>*</b>','STUDENT_ENROLLMENT_CODES'=>_('Student Enrollment Codes'),'STUDENT_ENROLLMENT'=>_('Students').'<b>*</b>','REPORT_CARD_COMMENTS'=>_('Report Card Comment Codes').'<b>*</b>','ELIGIBILITY_ACTIVITIES'=>_('Eligibility Activity Codes'));
$no_school_tables = array('SCHOOLS'=>true,'STUDENT_ENROLLMENT_CODES'=>true,'STAFF'=>true);
$tables += array('FOOD_SERVICE_STAFF_ACCOUNTS'=>_('Food Service Staff Accounts'));
if($CentreModules['Discipline'])
{
	$tables += array('DISCIPLINE_CATEGORIES'=>_('Referral Form'));
}

$table_list = '<TABLE align=left>';
foreach($tables as $table=>$name)
{
	if($table!='FOOD_SERVICE_STAFF_ACCOUNTS') :
		$exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT FROM $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
		$count_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT FROM $table WHERE SYEAR='".UserSyear()."'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
		//echo $count_RET[$table][1]['COUNT'][0]. ':'. $table .'<br>';
	else :
		$exists_RET['FOOD_SERVICE_STAFF_ACCOUNTS'] = DBGet(DBQuery("SELECT count(*) AS COUNT FROM staff WHERE SYEAR='$next_syear' AND exists(SELECT * FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=STAFF.STAFF_ID)"));
	endif;
	if($exists_RET[$table][1]['COUNT']>0):
		$table_list .= '<TR><TD><INPUT type=checkbox value=Y name=tables['.$table.']></TD><TD><font color=grey>'.$name.' ('.$exists_RET[$table][1]['COUNT'].')</font></TD></TR>';
	else:
		if($count_RET[$table][1]['COUNT'][0]>0)
			$table_list .= '<TR><TD><INPUT type=checkbox value=Y name=tables['.$table.'] CHECKED></TD><TD>'.$name.'</TD></TR>';
	endif;
}
$table_list .= '</TABLE></CENTER><BR><small>'
                .'* '._('You <i>must</i> roll users, school periods, marking periods, calendars, attendance codes, and report card codes at the same time or before rolling courses.')
                .'<BR><BR>* '._('You <i>must</i> roll enrollment codes at the same time or before rolling students.')
                .'<BR><BR>* '._('You <i>must</i> roll courses at the same time or before rolling report card comments.')
                .'<BR><BR>'._('Greyed items have already have data in the next school year (They might have been rolled).')
                .'<BR><BR>'._('Rolling greyed items will delete already existing data in the next school year.')
                .'</small><CENTER>';

DrawHeader(ProgramTitle());

if(Prompt('Confirm Rollover',sprintf(_('Are you sure you want to roll the data for %d-%d to the next school year?'),UserSyear(),(UserSyear()+1)),$table_list))
{
	if($_REQUEST['tables']['COURSES'] && ((!$_REQUEST['tables']['STAFF'] && $exists_RET['STAFF'][1]['COUNT']<1) || (!$_REQUEST['tables']['SCHOOL_PERIODS'] && $exists_RET['SCHOOL_PERIODS'][1]['COUNT']<1) || (!$_REQUEST['tables']['SCHOOL_MARKING_PERIODS'] && $exists_RET['SCHOOL_MARKING_PERIODS'][1]['COUNT']<1) || (!$_REQUEST['tables']['ATTENDANCE_CALENDARS'] && $exists_RET['ATTENDANCE_CALENDARS'][1]['COUNT']<1) || (!$_REQUEST['tables']['REPORT_CARD_GRADES'] && $exists_RET['REPORT_CARD_GRADES'][1]['COUNT']<1)))
		BackPrompt(_('You <i>must</i> roll users, school periods, marking periods, calendars, and report card codes at the same time or before rolling courses.'));
	if($_REQUEST['tables']['REPORT_CARD_COMMENTS'] && ((!$_REQUEST['tables']['COURSES'] && $exists_RET['COURSES'][1]['COUNT']<1)))
		BackPrompt(_('You <i>must</i> roll courses at the same time or before rolling report card comments.'));
	if(count($_REQUEST['tables']))
	{
		foreach($_REQUEST['tables'] as $table=>$value)
		{
			//if($exists_RET[$table][1]['COUNT']>0)
			//	DBQuery("DELETE FROM $table WHERE SYEAR='".$next_syear."'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":''));
			Rollover($table);
		}
	}
	echo '<FORM action=Modules.php?modname='.$_REQUEST['modname'].' method=POST>';
	DrawHeader('<IMG SRC=assets/check.gif>'._('The data have been rolled.'),'<INPUT type=submit value="'._('OK').'">');
	echo '</FORM>';
	unset($_SESSION['_REQUEST_vars']['tables']);
	unset($_SESSION['_REQUEST_vars']['delete_ok']);
}

function Rollover($table)
{	global $next_syear,$CentreModules;

	switch($table)
	{
		case 'SCHOOLS':
			DBQuery("DELETE FROM SCHOOLS WHERE SYEAR='$next_syear'");
            DBQuery("INSERT INTO SCHOOLS (SYEAR,ID,TITLE,ADDRESS,CITY,STATE,ZIPCODE,PHONE,PRINCIPAL,WWW_ADDRESS,SCHOOL_NUMBER,SHORT_NAME) SELECT SYEAR+1,ID,TITLE,ADDRESS,CITY,STATE,ZIPCODE,PHONE,PRINCIPAL,WWW_ADDRESS,SCHOOL_NUMBER,SHORT_NAME FROM SCHOOLS WHERE SYEAR='".UserSyear()."'");
		break;

		case 'STAFF':
			$user_custom='';
			$fields_RET = DBGet(DBQuery("SELECT ID FROM staff_FIELDS"));
			foreach($fields_RET as $field)
				$user_custom .= ',CUSTOM_'.$field['ID'];
			if($CentreModules['Food_Service'])
			{
				DBQuery("UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET STAFF_ID=(SELECT ROLLOVER_ID FROM staff WHERE STAFF_ID=FOOD_SERVICE_STAFF_ACCOUNTS.STAFF_ID) WHERE exists(SELECT * FROM staff WHERE STAFF_ID=FOOD_SERVICE_STAFF_ACCOUNTS.STAFF_ID AND ROLLOVER_ID IS NOT NULL AND SYEAR='$next_syear')");
				DBQuery("DELETE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE exists(SELECT * FROM staff WHERE STAFF_ID=FOOD_SERVICE_STAFF_ACCOUNTS.STAFF_ID AND SYEAR='$next_syear')");
			}
			DBQuery("DELETE FROM STUDENTS_JOIN_USERS WHERE STAFF_ID IN (SELECT STAFF_ID FROM staff WHERE SYEAR=$next_syear)");
			DBQuery("DELETE FROM staff_EXCEPTIONS WHERE USER_ID IN (SELECT STAFF_ID FROM staff WHERE SYEAR=$next_syear)");
			DBQuery("DELETE FROM PROGRAM_USER_CONFIG WHERE USER_ID IN (SELECT STAFF_ID FROM staff WHERE SYEAR=$next_syear)");
			DBQuery("DELETE FROM staff WHERE SYEAR='$next_syear'");

			DBQuery("INSERT INTO staff (SYEAR,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,LAST_LOGIN,SCHOOLS,PROFILE_ID,ROLLOVER_ID$user_custom) SELECT SYEAR+1,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,NULL,SCHOOLS,PROFILE_ID,STAFF_ID$user_custom FROM staff WHERE SYEAR='".UserSyear()."'");

			DBQuery("INSERT INTO PROGRAM_USER_CONFIG (USER_ID,PROGRAM,TITLE,VALUE) SELECT s.STAFF_ID,puc.PROGRAM,puc.TITLE,puc.VALUE FROM staff s,PROGRAM_USER_CONFIG puc WHERE puc.USER_ID=s.ROLLOVER_ID AND puc.PROGRAM='Preferences' AND s.SYEAR='$next_syear'");

			DBQuery("INSERT INTO staff_EXCEPTIONS (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT STAFF_ID,MODNAME,CAN_USE,CAN_EDIT FROM staff,STAFF_EXCEPTIONS WHERE USER_ID=ROLLOVER_ID AND SYEAR='$next_syear'");

			DBQuery("INSERT INTO STUDENTS_JOIN_USERS (STUDENT_ID,STAFF_ID) SELECT j.STUDENT_ID,s.STAFF_ID FROM staff s,STUDENTS_JOIN_USERS j WHERE j.STAFF_ID=s.ROLLOVER_ID AND s.SYEAR='$next_syear'");
		break;

		case 'SCHOOL_PERIODS':
			DBQuery("DELETE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
			DBQuery("INSERT INTO SCHOOL_PERIODS (SYEAR,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,PERIOD_ID FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ATTENDANCE_CALENDARS':
			DBQuery("DELETE FROM attendance_calendars WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
			DBQuery("INSERT INTO ATTENDANCE_CALENDARS (SYEAR,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,CALENDAR_ID FROM attendance_calendars WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			/*ATTENDANCE_CALENDAR SYNC*/
			$CAL_RET = DBGet(DBQuery("SELECT CALENDAR_ID AS LATESTID FROM attendance_calendars ORDER BY CALENDAR_ID DESC LIMIT 1"));
			DBQuery("DELETE FROM attendance_calendar WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
			DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,BLOCK,CALENDAR_ID) SELECT SYEAR+1,SCHOOL_ID,DATE_ADD(SCHOOL_DATE, INTERVAL 1 YEAR),MINUTES,BLOCK,'".$CAL_RET[1]['LATESTID']."' FROM attendance_calendar WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT SYEAR+1,SCHOOL_ID,SCHOOL_DATE,MINUTES,BLOCK,'".$CAL_RET[1]['LATESTID']."' FROM attendance_calendar WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ATTENDANCE_CODES':
			DBQuery("DELETE FROM attendance_code_categories WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM attendance_codes WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO ATTENDANCE_CODE_CATEGORIES (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SORT_ORDER,ID FROM attendance_code_categories WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO ATTENDANCE_CODES (SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,TYPE,STATE_CODE,DEFAULT_CODE,TABLE_NAME,SORT_ORDER) SELECT c.SYEAR+1,c.SCHOOL_ID,c.TITLE,c.SHORT_NAME,c.TYPE,c.STATE_CODE,c.DEFAULT_CODE,".db_case(array('c.TABLE_NAME',"'0'","'0'",'(SELECT ID FROM attendance_code_categories WHERE SCHOOL_ID=c.SCHOOL_ID AND ROLLOVER_ID=c.TABLE_NAME)')).",c.SORT_ORDER FROM attendance_codes c WHERE c.SYEAR='".UserSyear()."' AND c.SCHOOL_ID='".UserSchool()."'");
		break;

		case 'SCHOOL_MARKING_PERIODS':
			DBQuery("DELETE FROM SCHOOL_MARKING_PERIODS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");

			DBQuery("INSERT INTO SCHOOL_MARKING_PERIODS (PARENT_ID,SYEAR,MP,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT PARENT_ID,SYEAR+1,MP,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE+365,END_DATE+365,POST_START_DATE+365,POST_END_DATE+365,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_MARKING_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("UPDATE SCHOOL_MARKING_PERIODS AS asmp INNER JOIN SCHOOL_MARKING_PERIODS AS mp ON (mp.SYEAR=asmp.SYEAR AND mp.SCHOOL_ID=asmp.SCHOOL_ID AND mp.ROLLOVER_ID=asmp.PARENT_ID) SET asmp.PARENT_ID=mp.MARKING_PERIOD_ID WHERE asmp.SYEAR='$next_syear' AND asmp.SCHOOL_ID='".UserSchool()."'");
		break;

		case 'COURSES':
			DBQuery("DELETE FROM COURSE_SUBJECTS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM COURSES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM COURSE_PERIODS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");

			// ROLL COURSE_SUBJECTS
			DBQuery("INSERT INTO COURSE_SUBJECTS (SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,SUBJECT_ID FROM COURSE_SUBJECTS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			// ROLL COURSES
			DBQuery("INSERT INTO COURSES (SYEAR,SUBJECT_ID,SCHOOL_ID,GRADE_LEVEL,TITLE,SHORT_NAME,ROLLOVER_ID) SELECT SYEAR+1,(SELECT SUBJECT_ID FROM COURSE_SUBJECTS s WHERE s.SCHOOL_ID=c.SCHOOL_ID AND s.ROLLOVER_ID=c.SUBJECT_ID),SCHOOL_ID,GRADE_LEVEL,TITLE,SHORT_NAME,COURSE_ID FROM COURSES c WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			// ROLL COURSE_PERIODS
			DBQuery("INSERT INTO COURSE_PERIODS (SYEAR,SCHOOL_ID,COURSE_ID,TITLE,SHORT_NAME,PERIOD_ID,MP,MARKING_PERIOD_ID,TEACHER_ID,ROOM,TOTAL_SEATS,FILLED_SEATS,DOES_ATTENDANCE,GRADE_SCALE_ID,DOES_HONOR_ROLL,DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,CALENDAR_ID,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,(SELECT COURSE_ID FROM COURSES c WHERE c.SCHOOL_ID=p.SCHOOL_ID AND c.ROLLOVER_ID=p.COURSE_ID),TITLE,SHORT_NAME,(SELECT PERIOD_ID FROM SCHOOL_PERIODS n WHERE n.SCHOOL_ID=p.SCHOOL_ID AND n.ROLLOVER_ID=p.PERIOD_ID),MP,(SELECT MARKING_PERIOD_ID FROM SCHOOL_MARKING_PERIODS n WHERE n.MP=p.MP AND n.SCHOOL_ID=p.SCHOOL_ID AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID),(SELECT STAFF_ID FROM staff n WHERE n.ROLLOVER_ID=p.TEACHER_ID),ROOM,TOTAL_SEATS,0 AS FILLED_SEATS,DOES_ATTENDANCE,(SELECT ID FROM REPORT_CARD_GRADE_SCALES WHERE SCHOOL_ID=p.SCHOOL_ID AND ROLLOVER_ID=p.GRADE_SCALE_ID),DOES_HONOR_ROLL,DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,(SELECT CALENDAR_ID FROM attendance_calendars WHERE SCHOOL_ID=p.SCHOOL_ID AND ROLLOVER_ID=p.CALENDAR_ID),COURSE_PERIOD_ID FROM COURSE_PERIODS p WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			DBQuery("UPDATE COURSE_PERIODS cpa INNER JOIN COURSE_PERIODS cpb ON cpb.ROLLOVER_ID = cpa.teacher_id SET cpa.PARENT_ID = cpb.COURSE_PERIOD_ID WHERE cpa.PARENT_ID IS NOT NULL AND cpa.SYEAR='$next_syear' AND cpa.SCHOOL_ID='".UserSchool()."'"); // Update SQL Subquery
						
			$categories_RET = DBGet(DBQuery("SELECT ID,ROLLOVER_ID FROM attendance_code_categories WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."' AND ROLLOVER_ID IS NOT NULL"));
			foreach($categories_RET as $value)
				DBQuery("UPDATE COURSE_PERIODS SET DOES_ATTENDANCE=replace(DOES_ATTENDANCE,',$value[ROLLOVER_ID],',',$value[ID],') WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'STUDENT_ENROLLMENT':
			$next_start_date = DBDate();
			DBQuery("DELETE FROM STUDENT_ENROLLMENT WHERE SYEAR='$next_syear' AND LAST_SCHOOL='".UserSchool()."'");
			// ROLL STUDENTS TO NEXT GRADE
			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,NEXT_SCHOOL,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,(SELECT NEXT_GRADE_ID FROM SCHOOL_GRADELEVELS g WHERE g.ID=e.GRADE_ID),'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=e.SYEAR+1 AND TYPE='Add' AND DEFAULT_CODE='Y') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID,SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='".UserSchool()."'");

			// ROLL STUDENTS WHO ARE TO BE RETAINED
			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,NEXT_SCHOOL,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,GRADE_ID,'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=e.SYEAR+1 AND TYPE='Add' AND DEFAULT_CODE='Y') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID,SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='0'");

			// ROLL STUDENTS TO NEXT SCHOOL
			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,NEXT_SCHOOL,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,STUDENT_ID,(SELECT g.ID FROM SCHOOL_GRADELEVELS g WHERE g.SORT_ORDER=1 AND g.SCHOOL_ID=e.NEXT_SCHOOL),'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=e.SYEAR+1 AND TYPE='Add' AND DEFAULT_CODE='Y') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),NEXT_SCHOOL,SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL NOT IN ('".UserSchool()."','0','-1')");
		break;

		case 'REPORT_CARD_GRADES':
			DBQuery("DELETE FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM REPORT_CARD_GRADES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_GRADE_SCALES (SYEAR,SCHOOL_ID,TITLE,COMMENT,HR_GPA_VALUE,HHR_GPA_VALUE,SORT_ORDER,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,HR_GPA_VALUE,HHR_GPA_VALUE,SORT_ORDER,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_GRADES (SYEAR,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,GRADE_SCALE_ID,SORT_ORDER) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,(SELECT ID FROM REPORT_CARD_GRADE_SCALES WHERE ROLLOVER_ID=GRADE_SCALE_ID AND SCHOOL_ID=report_card_grades.SCHOOL_ID),SORT_ORDER FROM REPORT_CARD_GRADES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'REPORT_CARD_COMMENTS':
			DBQuery("DELETE FROM REPORT_CARD_COMMENT_CATEGORIES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM REPORT_CARD_COMMENTS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_COMMENT_CATEGORIES (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,COURSE_ID,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SORT_ORDER,".db_case(array('COURSE_ID',"''",'NULL',"(SELECT COURSE_ID FROM COURSES WHERE ROLLOVER_ID=rc.COURSE_ID)")).",ID FROM REPORT_CARD_COMMENT_CATEGORIES rc WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
            DBQuery("INSERT INTO REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,COURSE_ID,CATEGORY_ID,SCALE_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SORT_ORDER,".db_case(array('COURSE_ID',"''",'NULL',"(SELECT COURSE_ID FROM COURSES WHERE ROLLOVER_ID=rc.COURSE_ID)")).",".db_case(array('CATEGORY_ID',"''",'NULL',"(SELECT ID FROM REPORT_CARD_COMMENT_CATEGORIES WHERE ROLLOVER_ID=rc.CATEGORY_ID)")).",SCALE_ID FROM REPORT_CARD_COMMENTS rc WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ELIGIBILITY_ACTIVITIES':
		case 'DISCIPLINE_CATEGORIES':
			DBQuery("DELETE FROM $table WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SYEAR".$columns.") SELECT SYEAR+1".$columns." FROM $table WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		// DOESN'T HAVE A SCHOOL_ID
		case 'STUDENT_ENROLLMENT_CODES':
			DBQuery("DELETE FROM $table WHERE SYEAR='$next_syear'");
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SYEAR".$columns.") SELECT SYEAR+1".$columns." FROM $table WHERE SYEAR='".UserSyear()."'");
		break;

		case 'FOOD_SERVICE_STAFF_ACCOUNTS':
			DBQuery("UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET STAFF_ID=(SELECT STAFF_ID FROM staff WHERE ROLLOVER_ID=FOOD_SERVICE_STAFF_ACCOUNTS.STAFF_ID) WHERE exists(SELECT * FROM staff WHERE ROLLOVER_ID=FOOD_SERVICE_STAFF_ACCOUNTS.STAFF_ID AND SYEAR='$next_syear')");
		break;
	}
}
?>
