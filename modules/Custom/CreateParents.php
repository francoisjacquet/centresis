<?php
// 23-Aug-2007
// This script will automatically create parent accounts and associate students based on an email address which is part of the student record.
// The $email_column corresponds to a student field or an address field which is created for the email address.  The COLUMN_# is the column in the
// students table or the address table which holds the student contact email address.  You will need to create the column and inspect centre database
// to determine the email column and assign it here.
// Making the email address an address field is useful when using 'ganged' address (address record is shared by multiple students by using the 'add
// existing address feature).
// The column name should start with 's.' if a student field or 'a.' if an address field.
$email_column = 'a.CUSTOM_2';

// A list of potential users is obtained from the student contacts with an address.  The student must have at least one such contact.  Students which
// have the same email will be associated to the same user and grouped together in the list and even though each will have contacts listed for selection
// only that of the first student selected in the group will be used in the creation of the account.

// Parent users are created with the following profile id.  As of Centre 2.8 non-admin profiles are available and '3' is the default 'parent' profile.
$profile_id = '3';

// If $test email is set then this script will only 'go through the motions' and email the results to the $test_email address instead of parents
// no accounts are created and no associations are made.  Use this to verify the behavior and email operation before actual use.
$test_email = 'maboytim@yahoo.com';

// Set the from and cc emails here.  $cc can be comma separated list of emails addresses.
// If $from is empty then deafult is the user's email address from their centre staff record.
// The 'from' email address is automatically prepended to the $cc list and should not be included in the $cc list here..
$from = '';
$cc = $CentreNotifyAddress;

// new for when parent account was created new
// old for when parent account was existing
$subject['new'] = $CentreTitle.' New Parent Account';
$subject['old'] = $CentreTitle.' Updated Parent Account';
// ^N=parent name, ^S=list of student names, ^U=username, ^P=password
$message['new'] = "Dear ^N,

A parent account for the $CentreTitle Student Information System has been created to access school information and student information for the following students:
^S

Your account credentials are:
Username: ^U
Password: ^P

A link to the SIS website and instructions for access are available on the school's website www.stsjp.org.";

$message['old'] = "Dear ^N,

The following students have been added to your parent account on the $CentreTitle Student Information System:
^S";

$passwords = array('bigbadbug','zigzagzug','disdogbop','bigbedbug','bigredbug','wigwagwug','rubdubdub','fatcatsat');

// end of user configuration

DrawHeader(ProgramTitle());

if(!$email_column)
	ErrorMessage(array('<IMG SRC=assets/x.gif>You must set the <b>$email_column</b> variable to use this script.'),'fatal');

if(!$from)
{
	$from = User('EMAIL');
	if(!$from)
		ErrorMessage(array('<IMG SRC=assets/x.gif>You must set the <b>$from</b> variable or have a user email address to use this script.'),'fatal');
}

$cc = $from.($cc?','.$cc:'');

$headers = "From: $from\r\nCc: $cc";

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['student']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['student']).'\'';
	$extra['SELECT'] = ",lower($email_column) AS EMAIL";
	$extra['SELECT'] .= ",(SELECT STAFF_ID FROM STAFF WHERE lower(EMAIL)=lower($email_column) AND PROFILE='parent' AND SYEAR=ssm.SYEAR) AS STAFF_ID";
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
	$extra['group'] = array('EMAIL');
	$extra['addr'] = true;
	$extra['WHERE'] .= " AND sam.RESIDENCE='Y'";

	$RET = GetStuList($extra);
	//echo '<pre>'; var_dump($RET); echo '</pre>';

	foreach($RET as $email=>$students)
	{
		unset($id);
		$student_id = $students[1]['STUDENT_ID'];
		if(!$students[1]['STAFF_ID'])
		{
			if($_REQUEST['contact'][$student_id])
			{
				$tmp_username = $username = trim(strpos($students[1]['EMAIL'],'@')!==false?substr($students[1]['EMAIL'],0,strpos($students[1]['EMAIL'],'@')):$students[1]['EMAIL']);
				$i = 1;
				while(DBGet(DBQuery("SELECT STAFF_ID FROM STAFF WHERE upper(USERNAME)=upper('$username') AND SYEAR='".UserSyear()."'")))
					$username = $tmp_username.$i++;
				$user = DBGet(DBQuery("SELECT FIRST_NAME,MIDDLE_NAME,LAST_NAME FROM PEOPLE WHERE PERSON_ID='".$_REQUEST['contact'][$student_id]."'"));
				$user = $user[1];
				$password = $passwords[rand(0,count($passwords)-1)];
				if(!$test_email)
				{
					// get staff id
					$id = DBGet(DBQuery('SELECT '.db_seq_nextval('STAFF_SEQ').' AS SEQ_ID '.FROM_DUAL));
					$id = $id[1]['SEQ_ID'];
					$sql = "INSERT INTO STAFF (STAFF_ID,SYEAR,PROFILE,PROFILE_ID,FIRST_NAME,MIDDLE_NAME,LAST_NAME,USERNAME,PASSWORD,EMAIL) values ('$id','".UserSyear()."','parent','$profile_id','$user[FIRST_NAME]','$user[MIDDLE_NAME]','$user[LAST_NAME]','$username','$password','".$students[1]['EMAIL']."')";
					DBQuery($sql);
					$staff = DBGet(DBquery("SELECT FIRST_NAME||' '||LAST_NAME AS NAME,USERNAME,PASSWORD FROM STAFF WHERE STAFF_ID=$id"));
				}
				else
				{
					$id = true;
					$staff = array(1=>array('NAME'=>$user['FIRST_NAME'].' '.$user['LAST_NAME'],'USERNAME'=>$username,'PASSWORD'=>$password));
				}
				$account = 'new';
			}
		}
		else
		{
			$id = $students[1]['STAFF_ID'];
			$staff = DBGet(DBquery("SELECT FIRST_NAME||' '||LAST_NAME AS NAME,USERNAME,PASSWORD FROM STAFF WHERE STAFF_ID=$id"));
			$account = 'old';
		}
		if($id)
		{
			$staff = $staff[1];
			$student_list = '';
			foreach($students as $student)
			{
				if(!$test_email)
				{
					$sql = "INSERT INTO STUDENTS_JOIN_USERS (STAFF_ID,STUDENT_ID) values ('$id',$student[STUDENT_ID])";
					DBQuery($sql);
				}
				$student_list .= str_replace('&nbsp;',' ',$student['FULL_NAME'])."\r";
			}
			$msg = str_replace('^S',$student_list,$message[$account]);
			$msg = str_replace('^N',$staff['NAME'],$msg);
			$msg = str_replace('^U',$staff['USERNAME'],$msg);
			$msg = str_replace('^P',$staff['PASSWORD'],$msg);
			$result = mail(!$test_email ? $students[1]['EMAIL'] : $test_email,$subject[$account],$msg,$headers);

			$RET[$email][1]['PARENT'] = $staff['NAME'];
			$RET[$email][1]['USERNAME'] = $staff['USERNAME'];
			$RET[$email][1]['PASSWORD'] = $staff['PASSWORD'];
			if($result)
				$RET[$email][1]['RESULT'] = 'Success';
			else
				$RET[$email][1]['RESULT'] = 'Email failed';
		}
		else
			$RET[$email][1]['RESULT'] = 'Fail';
	}
	$columns = array('FULL_NAME'=>'Student','PARENT'=>'Parent','USERNAME'=>'Username','PASSWORD'=>'Password','EMAIL'=>'Email','RESULT'=>'Result');
	ListOutput($RET,$columns,'Creation Result','Creation Results',false,array('EMAIL'));
	}
	else
		BackPrompt('You must choose at least one student');
	//unset($_REQUEST['modfunc']);
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']=='list' || UserStudentID())
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		$extra['header_right'] = SubmitButton('Create Parent Accounts for Selected Students');
	}

	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX,lower($email_column) AS EMAIL,s.STUDENT_ID AS CONTACT";
	$extra['SELECT'] .= ",(SELECT STAFF_ID FROM STAFF WHERE lower(EMAIL)=lower($email_column) AND PROFILE='parent' AND SYEAR=ssm.SYEAR) AS STAFF_ID";
	//$extra['WHERE'] = " AND $email_column IS NOT NULL";
	$extra['WHERE'] .= " AND NOT EXISTS (SELECT '' FROM STUDENTS_JOIN_USERS sju,STAFF st WHERE sju.STUDENT_ID=s.STUDENT_ID AND st.STAFF_ID=sju.STAFF_ID AND SYEAR='".UserSyear()."')";

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox','CONTACT'=>'_makeContactSelect');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['columns_after'] = array('EMAIL'=>'Email','CONTACT'=>'Contact');
	$extra['LO_group'] = $extra['group'] = array('EMAIL');
	$extra['addr'] = true;
	$extra['SELECT'] .= ",a.ADDRESS_ID";
	$extra['WHERE'] .= " AND sam.RESIDENCE='Y'";

	if(!UserStudentID())
		Search('student_id',$extra);
	else
	{
		DrawHeader('',$extra['header_right']);
		$extra['WHERE'] .= " AND s.STUDENT_ID='".UserStudentID()."'";
		$LO_ret = GetStuList($extra);
		$LO_columns = $extra['columns_before']+array('FULL_NAME'=>'Student','STUDENT_ID'=>'Centre ID','GRADE_ID'=>'Grade')+$extra['columns_after'];
		ListOutput($LO_ret,$LO_columns,'Student','Students',$extra['link'],$extra['LO_group']);
	}

	if($_REQUEST['search_modfunc']=='list' || UserStudentID())
	{
		echo '<BR><CENTER>'.SubmitButton('Create Parent Accounts for Selected Students').'</CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	if(strpos($THIS_RET['EMAIL'],'@'))
		return "<INPUT type=checkbox name=student[$value] value=$value>";
	else
		return '';
}

function _makeContactSelect($value,$column)
{	global $THIS_RET;

	if(!$THIS_RET['STAFF_ID'])
		$RET = DBGet(DBQuery("SELECT sjp.PERSON_ID,sjp.STUDENT_RELATION,p.FIRST_NAME||' '||p.LAST_NAME AS CONTACT FROM STUDENTS_JOIN_PEOPLE sjp,PEOPLE p WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='$value' AND sjp.ADDRESS_ID='$THIS_RET[ADDRESS_ID]' ORDER BY sjp.STUDENT_RELATION"));
	else
		$RET = DBGet(DBQuery("SELECT '' AS PERSON_ID,STAFF_ID AS STUDENT_RELATION,FIRST_NAME||' '||LAST_NAME AS CONTACT FROM STAFF WHERE STAFF_ID='$THIS_RET[STAFF_ID]'"));

	if(count($RET))
	{
		$checked = ' CHECKED';
		$return = '<TABLE>';
		foreach($RET as $contact)
		{
			$return .= '<TR><TD>'.($contact['PERSON_ID']?'<INPUT type=radio name=contact['.$value.'] value='.$contact['PERSON_ID'].$checked.'>':'&nbsp;').'</TD>';
			$return .= '<TD>'.$contact['CONTACT'].'</TD>';
			$return .= '<TD>('.$contact['STUDENT_RELATION'].')</TD></TR>';
			$checked = '';
		}
		$return .= '</TABLE>';
	}
	return $return;
}
?>
