<?php
if($_REQUEST['modfunc']=='save' && AllowEdit())
{
	$current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".UserStaffID()."'"),array(),array('STUDENT_ID'));
	foreach($_REQUEST['student'] as $student_id=>$yes)
	{
		if(!$current_RET[$student_id])
		{
			$sql = "INSERT INTO STUDENTS_JOIN_USERS (STUDENT_ID,STAFF_ID) values('".$student_id."','".UserStaffID()."')";
			DBQuery($sql);
		}
	}
	unset($_REQUEST['modfunc']);
	unset($_SESSION['_REQUEST_vars']['modfunc']);
	$note = _('The selected user\'s profile now includes access to the selected students.');
}
DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='delete' && AllowEdit())
{
	if(DeletePrompt('student from that user','remove access to'))
	{
		DBQuery("DELETE FROM STUDENTS_JOIN_USERS WHERE STUDENT_ID='$_REQUEST[student_id]' AND STAFF_ID='".UserStaffID()."'");
		unset($_REQUEST['modfunc']);
	}
}

//if($_REQUEST['modfunc']!='delete')
if(!$_REQUEST['modfunc'])
{
	if(UserStaffID())
	{
		$profile = DBGet(DBQuery("SELECT PROFILE FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
		if($profile[1]['PROFILE']!='parent')
		{
			unset($_SESSION['staff_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		}
	}

	$extra['profile'] = 'parent';
	Search('staff_id',$extra);

	if(UserStaffID())
	{
		if($_REQUEST['search_modfunc']=='list')
		{
			echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
			DrawHeader('',SubmitButton(_('Add Selected Students')));
		}

		if($note)
			DrawHeader('<IMG SRC=assets/check.gif>'.$note);

		echo '<CENTER><TABLE><TR><TD valign=top>';
		$current_RET = DBGet(DBQuery("SELECT u.STUDENT_ID,s.LAST_NAME||', '||s.FIRST_NAME AS FULL_NAME FROM STUDENTS_JOIN_USERS u,STUDENTS s WHERE s.STUDENT_ID=u.STUDENT_ID AND u.STAFF_ID='".UserStaffID()."'"));
		$link['remove'] = array('link'=>"Modules.php?modname=$_REQUEST[modname]&modfunc=delete",'variables'=>array('student_id'=>'STUDENT_ID'));
		ListOutput($current_RET,array('FULL_NAME'=>_('Students')),'Student','Students',$link,array(),array('search'=>false));
		echo '</TD><TD valign=top>';

		$extra['link'] = array('FULL_NAME'=>false);
		$extra['SELECT'] = ",CAST (NULL AS CHAR(1)) AS CHECKBOX";
		$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
		$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
		$extra['new'] = true;
		$extra['options']['search'] = false;

		if(AllowEdit())
			Search('student_id',$extra);

		echo '</TD></TR></TABLE></CENTER>';

		if($_REQUEST['search_modfunc']=='list')
			echo "<BR><CENTER>".SubmitButton(_('Add Selected Students'))."</CENTER></FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>
