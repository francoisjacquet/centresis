<?php
// if $homeroom is null then teacher and subject for period used for attendance are used for homeroom teacher and subject
// if $homeroom is set then teacher for $homeroom subject and $homeroom are used for teacher and subject
//$homeroom = 'Homeroom';
$target = '19.00';
$warning = '5.00';
// Available substitutions for the notes...
// %N = student firstname (given) or nickname (according to user preference)
// %F = student firstname
// %g = he/she according to student gender
// %G = He/She according to student gender
// %h = his/her according to student gender
// %H = His/Her according to student gender
// %P = payment amount
// %T = balance target amount
$warning_note = _('%N\'s lunch account is getting low.  Please send in at least %P with %h reminder slip.  THANK YOU!');
$negative_note = _('%N now has a <B>negative balance</B> in %h lunch account. Please send in the negative balance plus %T.  THANK YOU!');
$minimum = '-40.00';
$minimum_note = _('%N now has a <b>negative balance</b> below the allowed minimum.  Please send in the negative balance plus %T.  THANK YOU!');
$year_end_note = _('%N\'s lunch account is getting low.  The requested payment anount is estimated so %h account will have a zero balance at the end of the school year.  Please send in the requested amount with %h reminder slip.  THANK YOU!');
$year_end_note = _('%N\'s lunch account is getting low.  It\'s estimated that %g needs about a %T current balance to finish the year with a zero balance.  Please send in the requested amount with %h reminder slip.  THANK YOU!');

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = "'".implode("','",$_REQUEST['st_arr'])."'";

	$students = DBGet(DBQuery("SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.NAME_SUFFIX,s.CUSTOM_200000002 AS NICKNAME,s.CUSTOM_200000000 AS GENDER,fsa.ACCOUNT_ID,fsa.STATUS,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fsa.ACCOUNT_ID) AS BALANCE,(SELECT TITLE FROM SCHOOLS WHERE ID=ssm.SCHOOL_ID AND SYEAR=ssm.SYEAR) AS SCHOOL,(SELECT TITLE FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID) AS GRADE".($_REQUEST['year_end']=='Y'?",(SELECT count(1) FROM attendance_calendar WHERE CALENDAR_ID=ssm.CALENDAR_ID AND SCHOOL_DATE>CURRENT_DATE) AS DAYS,(SELECT -sum(fsti.AMOUNT) FROM FOOD_SERVICE_TRANSACTIONS fst,FOOD_SERVICE_TRANSACTION_ITEMS fsti WHERE fst.SYEAR=ssm.SYEAR AND fsti.TRANSACTION_ID=fst.TRANSACTION_ID AND fst.ACCOUNT_ID=fsa.ACCOUNT_ID AND fsti.AMOUNT<0 AND fst.TIMESTAMP BETWEEN CURRENT_DATE-14 AND CURRENT_DATE-1) AS T_AMOUNT,(SELECT count(1) FROM attendance_calendar WHERE CALENDAR_ID=ssm.CALENDAR_ID AND SCHOOL_DATE BETWEEN CURRENT_DATE-14 AND CURRENT_DATE-1) AS T_DAYS":'')." FROM STUDENTS s,STUDENT_ENROLLMENT ssm,FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE s.STUDENT_ID IN (".$st_list.") AND fsa.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."'"));
	$handle = PDFStart();
	foreach($students as $student)
	{
		if($homeroom)
			$teacher = DBGet(DBQuery("SELECT CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS FULL_NAME,cs.TITLE
			FROM staff s,SCHEDULE sch,COURSE_PERIODS cp,COURSES c,COURSE_SUBJECTS cs
			WHERE s.STAFF_ID=cp.TEACHER_ID AND sch.STUDENT_ID='".$student['STUDENT_ID']."' AND cp.COURSE_ID=sch.COURSE_ID AND c.COURSE_ID=cp.COURSE_ID AND c.SUBJECT_ID=cs.SUBJECT_ID AND cs.TITLE='".$homeroom."' AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.SYEAR='".UserSyear()."'"));
		else
			$teacher = DBGet(DBQuery("SELECT CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS FULL_NAME,cs.TITLE
			FROM staff s,SCHEDULE sch,COURSE_PERIODS cp,COURSES c,COURSE_SUBJECTS cs,SCHOOL_PERIODS sp
            WHERE s.STAFF_ID=cp.TEACHER_ID AND sch.STUDENT_ID='".$student['STUDENT_ID']."' AND cp.COURSE_ID=sch.COURSE_ID AND c.COURSE_ID=cp.COURSE_ID AND c.SUBJECT_ID=cs.SUBJECT_ID AND sp.PERIOD_ID=cp.PERIOD_ID AND sp.ATTENDANCE='Y' AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.SYEAR='".UserSyear()."'"));
		$teacher = $teacher[1];
		$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.CUSTOM_200000002 AS NICKNAME FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE fssa.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND s.STUDENT_ID=fssa.STUDENT_ID AND s.STUDENT_ID!='".$student['STUDENT_ID']."' AND exists(SELECT '' FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=s.STUDENT_ID AND SYEAR='".UserSyear()."' AND (START_DATE<=CURRENT_DATE AND (END_DATE IS NULL OR CURRENT_DATE<=END_DATE)))"));

		$last_deposit = DBGet(DBQuery("SELECT (SELECT sum(AMOUNT) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,DATE_FORMAT(fst.TIMESTAMP,'%Y-%m-%d') AS DATE FROM FOOD_SERVICE_TRANSACTIONS fst WHERE fst.SHORT_NAME='DEPOSIT' AND fst.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND SYEAR='".UserSyear()."' ORDER BY fst.TRANSACTION_ID DESC LIMIT 1"),array('DATE'=>'ProperDate'));
		$last_deposit = $last_deposit[1];

		if($_REQUEST['year_end']=='Y')
		{
			$xtarget = number_format($student['DAYS']*$student['T_AMOUNT']/$student['T_DAYS'],2);
			reminder($student,$teacher,$xstudents,$xtarget,$last_deposit,$year_end_note);
		}
		else
		{
			$xtarget = number_format($target*(count($xstudents)+1),2);
			if($student['BALANCE'] < $minimum)
				reminder($student,$teacher,$xstudents,$xtarget,$last_deposit,$minimum_note);
			elseif($student['BALANCE'] < 0)
				reminder($student,$teacher,$xstudents,$xtarget,$last_deposit,$negative_note);
			elseif($student['BALANCE'] < $warning)
				reminder($student,$teacher,$xstudents,$xtarget,$last_deposit,$warning_note);
		}

		echo '<!-- NEED 3in -->';
	}
	PDFStop($handle);
	}
	else
	BackPrompt(_('You must choose at least one student'));
}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&_CENTRE_PDF=true method=POST>";
		//DrawHeader('',SubmitButton('Create Reminders for Selected Students'));
		$extra['header_right'] = SubmitButton('Create Reminders for Selected Students');

		$extra['extra_header_left'] = '<TABLE><TR>';
		$extra['extra_header_left'] .= '<TD align=right>'._('Estimate for year end').'</TD><TD align=left><INPUT type=checkbox name=year_end value=Y></TD>';
		$extra['extra_header_left'] .= '</TR></TABLE>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y checked name=controller onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['new'] = true;
	$extra['options']['search'] = false;

	Widgets('fsa_balance_warning');
	Widgets('fsa_status');

	$extra['SELECT'] .= ',coalesce(fssa.STATUS,\'Active\') AS STATUS,fsa.BALANCE';
	$extra['SELECT'] .= ',(SELECT \'Y\' FROM FOOD_SERVICE_ACCOUNTS WHERE fsa.BALANCE < \''.$warning.'\' AND fsa.BALANCE >= 0 LIMIT 1) AS WARNING';
	$extra['SELECT'] .= ',(SELECT \'Y\' FROM FOOD_SERVICE_ACCOUNTS WHERE fsa.BALANCE < 0 AND fsa.BALANCE >= \''.$minimum.'\' LIMIT 1) AS NEGATIVE';
	$extra['SELECT'] .= ',(SELECT \'Y\' FROM FOOD_SERVICE_ACCOUNTS WHERE fsa.BALANCE < '.$minimum.' LIMIT 1) AS MINIMUM';
	if(!strpos($extra['FROM'],'fssa'))
	{
		$extra['FROM'] .= ',FOOD_SERVICE_STUDENT_ACCOUNTS fssa';
		$extra['WHERE'] .= ' AND fssa.STUDENT_ID=s.STUDENT_ID';
	}
	if(!strpos($extra['FROM'],'fsa'))
	{
		$extra['FROM'] .= ',FOOD_SERVICE_ACCOUNTS fsa';
		$extra['WHERE'] .= ' AND fsa.ACCOUNT_ID=fssa.ACCOUNT_ID';
	}
	$extra['functions'] += array('BALANCE'=>'red','WARNING'=>'x','NEGATIVE'=>'x','MINIMUM'=>'x');
	$extra['columns_after'] = array('BALANCE'=>_('Balance'),'STATUS'=>_('Status'),'WARNING'=>_('Warning').'<br>'.$warning,'NEGATIVE'=>_('Negative'),'MINIMUM'=>_('Minimum').'<br>'.$minimum);

	$extra['GROUP'] = "s.STUDENT_ID";
	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton(_('Create Reminders for Selected Students')).'</CENTER>';
		echo "</FORM>";
	}
}

function reminder($student,$teacher,$xstudents,$target,$last_deposit,$note)
{
	$payment = $target - $student['BALANCE'];
	if($_REQUEST['year_end']=='Y')
		$payment = floor($payment * 2 + 0.99) / 2;
	if($payment <= 0)
		return;
	$payment = number_format($payment,2);

	echo '<TABLE width=100%>';
	echo '<TR><TD colspan=3 align=center><FONT size=+1><I><B>* * * '.($_REQUEST['year_end']=='Y'?_('Year End').' ':'')._('Lunch Payment Reminder').' * * *</B></I></FONT></TD></TR>';
	echo '<TR><TD colspan=3 align=center><B>'.$student['SCHOOL'].'</B></TD></TR>';

	echo '<TR><TD width=33%>';
	echo ($student['NICKNAME']?$student['NICKNAME']:$student['FIRST_NAME']).' '.$student['LAST_NAME'].'<BR>';
	echo '<small>'.$student['STUDENT_ID'].'</small>';
	if(count($xstudents))
	{
		echo '<small><BR>'.Localize('colon',_('Other students om this account'));
		foreach($xstudents as $xstudent)
			echo '<BR>&nbsp;&nbsp;'.($xstudent['NICKNAME']?$xstudent['NICKNAME']:$xstudent['FIRST_NAME']).' '.$xstudent['LAST_NAME'];
		echo '</small>';
	}
	echo '</TD><TD width=33%>';
	echo $student['GRADE'].'<BR>';
	echo '<small>Grade</small>';
	echo '</TD><TD width=33%>';
	echo $teacher['FULL_NAME'].'<BR>';
	echo '<small>'.$teacher['TITLE'].' '._('Teacher').'</small>';
	echo '</TD></TR>';

	echo '<TR><TD width=33%>';
	echo ProperDate(DBDate()).'<BR>';
	echo '<small>'._('Today\'s Date').'</small>';
	echo '</TD><TD width=34%>';
	echo ($last_deposit ? $last_deposit['DATE'] : _('None')).'<BR>';
	echo '<small>'._('Date of Last Deposit').'</small>';
	echo '</TD><TD width=33%>';
	echo ($last_deposit ? $last_deposit['AMOUNT'] : _('None')).'<BR>';
	echo '<small>'._('Amount of Last Deposit').'</small>';
	echo '</TD></TR>';

	echo '<TR><TD width=33%>';
	echo ($student['BALANCE']<0 ? '<B>'.$student['BALANCE'].'</B>' : $student['BALANCE']).'<BR>';
	echo '<small>'._('Balance').'</small>';
	echo '</TD><TD width=34%>';
	echo '<B>'.$payment.'</B><BR>';
	echo '<small><B>'.($_REQUEST['year_end']=='Y'?_('Requested Payment'):_('Mimimum Payment')).' </B></small>';
	echo '</TD><TD width=33%>';
	echo $student['ACCOUNT_ID'].'<BR>';
	echo '<small>'._('Account ID').'</small>';
	echo '</TD></TR>';

	$note = str_replace('%N',($student['NICKNAME'] ? $student['NICKNAME'] : $student['FIRST_NAME']),$note);
	$note = str_replace('%F',$student['FIRST_NAME'],$note);
	$note = str_replace('%g',($student['GENDER'] ? (substr($student['GENDER'],0,1)=='F' ? 'she' : 'he') : 'he/she'),$note);
	$note = str_replace('%G',($student['GENDER'] ? (substr($student['GENDER'],0,1)=='F' ? 'She' : 'He') : 'He/she'),$note);
	$note = str_replace('%h',($student['GENDER'] ? (substr($student['GENDER'],0,1)=='F' ? 'her' : 'his') : 'his/her'),$note);
	$note = str_replace('%H',($student['GENDER'] ? (substr($student['GENDER'],0,1)=='F' ? 'Her' : 'His') : 'His/her'),$note);
	$note = str_replace('%P',money_formatt('%i',$payment),$note);
	$note = str_replace('%T',$target,$note);

	echo '<TR><TD colspan=3>';
	echo '<BR>'.$note.'<BR>';
	echo '</TD></TR>';
	echo '<TR><TD colspan=3><BR><BR><HR><BR><BR></TD></TR></TABLE>';
}
?>
