<?php

// This was a quick hack to email parents who were assigned accounts but had never logged in

// If $test email is set then this script will only 'go through the motions' and email the results to the $test_email address instead of parents
$test_email = 'maboytim@yahoo.com';

// Set the from and cc emails here - the emails can be comma separated list of emails.
if(User('EMAIL'))
	$headers = "From: ".User('EMAIL')."\r\nCc: ".User('EMAIL').",$CentreNotifyAddress";
else
	$headers = "From: matt@boytim.org\r\nCc: $CentreNotifyAddress";

$subject = "$CentreTitle - SIS Parent Account";
// ^N=parent name, ^S=list of student names, ^U=username, ^P=password
$message = "Dear ^N,

A Parent account was created for you to access the Student Information System for the following students of the $CentreTitle:
^S

An earlier email was sent when your account was created but the account has not been used.  This is a reminder email in case the earlier email was not received.

Your account credentials are:
Username: ^U
Password: ^P

A link to the SIS website and a brief introduction for parents are available on the school's website.";

$passwords = array('bigbadbug','zigzagzug','disdogbop','bigbedbug','bigredbug','wigwagwug','rubdubdub');

// end of user configuration

DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['staff']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['staff']).'\'';

	// find and assign blank missing passwords
	$extra['WHERE'] = " AND s.PASSWORD IS NULL AND s.STAFF_ID IN ($st_list)";
	$RET = GetStaffList($extra);
	//echo '<pre>'; var_dump($RET); echo '</pre>';

	foreach($RET as $staff)
	{
		$password = $passwords[rand(0,count($passwords)-1)];
		DBQuery("UPDATE STAFF SET PASSWORD='$password' WHERE STAFF_ID='$staff[STAFF_ID]'");
	}

	$extra['SELECT'] = ",s.FIRST_NAME||' '||s.LAST_NAME AS NAME,s.USERNAME,s.PASSWORD,s.EMAIL";
	$extra['WHERE'] = " AND s.STAFF_ID IN ($st_list)";

	$RET = GetStaffList($extra);
	//echo '<pre>'; var_dump($RET); echo '</pre>';

	$RESULT = array(0=>array());
	foreach($RET as $staff)
	{
		$staff_id = $staff['STAFF_ID'];

		$students_RET = DBGet(DBQuery("SELECT s.FIRST_NAME||' '||s.LAST_NAME AS FULL_NAME FROM STUDENTS s,STUDENT_ENROLLMENT sse,STUDENTS_JOIN_USERS sju WHERE sju.STAFF_ID='$staff_id' AND s.STUDENT_ID=sju.STUDENT_ID AND sse.STUDENT_ID=sju.STUDENT_ID AND sse.SYEAR='".UserSyear()."' AND sse.END_DATE IS NULL"));
		//echo '<pre>'; var_dump($students_RET); echo '</pre>';

		$student_list = '';
		foreach($students_RET as $student)
			$student_list .= str_replace('&nbsp;',' ',$student['FULL_NAME'])."\r";

		$msg = str_replace('^S',$student_list,$message);
		$msg = str_replace('^N',$staff['NAME'],$msg);
		$msg = str_replace('^U',$staff['USERNAME'],$msg);
		$msg = str_replace('^P',$staff['PASSWORD'],$msg);
		$result = mail(!$test_email?$staff['EMAIL']:$test_email,$subject,$msg,$headers);

		$RESULT[] = array('PARENT'=>$staff['FULL_NAME'],'USERNAME'=>$staff['USERNAME'],'EMAIL'=>!$test_email?$staff['EMAIL']:$test_email,'RESULT'=>$result?'Success':'Fail');
	}
	unset($RESULT[0]);
	$columns = array('PARENT'=>'Parent','USERNAME'=>'Username','EMAIL'=>'Email','RESULT'=>'Result');
	ListOutput($RESULT,$columns,'Notification Result','Notification Results');
	}
	else
		BackPrompt('You must choose at least one student');

}

if(!$_REQUEST['modfunc'] || $_REQUEST['modfunc']=='list')
{
	if($_REQUEST['modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		DrawHeader('',SubmitButton('Notify Selected Parents'));
	}

	$extra['SELECT'] = ",s.STAFF_ID AS CHECKBOX,s.USERNAME,s.EMAIL";

	$extra['SELECT'] .= ",(SELECT count(st.STUDENT_ID) FROM STUDENTS st,STUDENT_ENROLLMENT sse,STUDENTS_JOIN_USERS sju WHERE sju.STAFF_ID=s.STAFF_ID AND st.STUDENT_ID=sju.STUDENT_ID AND sse.STUDENT_ID=sju.STUDENT_ID AND sse.SYEAR='".UserSyear()."' AND sse.END_DATE IS NULL) AS ASSOCIATED";

	$extra['WHERE'] = " AND s.LAST_LOGIN IS NULL";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'staff\');"><A>');
	$extra['columns_after'] = array('ASSOCIATED'=>'# Associated','USERNAME'=>'Username','EMAIL'=>'Email');
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['profile'] = 'parent';

	Search('staff_id',$extra);

	if($_REQUEST['modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton('Notify Selected Parents').'</CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	if($THIS_RET['USERNAME'] && $THIS_RET['EMAIL'] && $THIS_RET['ASSOCIATED']>0)
		return "<INPUT type=checkbox name=staff[$value] value=$value>";
	else
		return '';
}
?>