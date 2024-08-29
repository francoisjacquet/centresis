<?php

if($_REQUEST['modfunc']=='update')
{
    if(UserStudentID() && AllowEdit())
    {
        if(count($_REQUEST['food_service']))
        {
            if($_REQUEST['food_service']['BARCODE'])
            {
                $RET = DBGet(DBQuery("SELECT ACCOUNT_ID FROM FOOD_SERVICE_STUDENT_ACCOUNTS WHERE BARCODE='".str_replace("\'","''",trim($_REQUEST['food_service']['BARCODE']))."' AND STUDENT_ID!='".UserStudentID()."'"));
                if($RET)
                {
                    $student_RET = DBGet(DBQuery("SELECT s.FIRST_NAME||' '||s.LAST_NAME AS FULL_NAME FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE s.STUDENT_ID=fssa.STUDENT_ID AND fssa.ACCOUNT_ID='".$RET[1]['ACCOUNT_ID']."'"));
                    $question = _("Are you sure you want to assign that barcode?");
                    $message = sprintf(_("That barcode is already assigned to Student <B>%s</B>."),$student_RET[1]['FULL_NAME']).' '._("Hit OK to reassign it to the current student or Cancel to cancel all changes.");
                }
                else
                {
                    $RET = DBGet(DBQuery("SELECT STAFF_ID FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE BARCODE='".str_replace("\'","''",trim($_REQUEST['food_service']['BARCODE']))."'"));
                    if($RET)
                    {
                        $staff_RET = DBGet(DBQuery("SELECT FIRST_NAME||' '||LAST_NAME AS FULL_NAME FROM STAFF WHERE STAFF_ID='".$RET[1]['STAFF_ID']."'"));
                        $question = _("Are you sure you want to assign that barcode?");
                        $message = sprintf(_("That barcode is already assigned to User <B>%s</B>"),$staff_RET[1]['FULL_NAME']).' '._("Hit OK to reassign it to the current student or Cancel to cancel all changes.");
                    }
                }
            }
            if(!$RET || PromptX($title='Confirm',$question,$message))
            {
                $sql = "UPDATE FOOD_SERVICE_STUDENT_ACCOUNTS SET ";
                foreach($_REQUEST['food_service'] as $column_name=>$value)
                    $sql .= $column_name."='".str_replace("\'","''",trim($value))."',";
                $sql = substr($sql,0,-1)." WHERE STUDENT_ID='".UserStudentID()."'";
                if($_REQUEST['food_service']['BARCODE'])
                {
                    DBQuery("UPDATE FOOD_SERVICE_STUDENT_ACCOUNTS SET BARCODE=NULL WHERE BARCODE='".str_replace("\'","''",trim($_REQUEST['food_service']['BARCODE']))."'");
                    DBQuery("UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET BARCODE=NULL WHERE BARCODE='".str_replace("\'","''",trim($_REQUEST['food_service']['BARCODE']))."'");
                }
                DBQuery($sql);
                unset($_REQUEST['modfunc']);
                unset($_REQUEST['food_service']);
                unset($_SESSION['_REQUEST_vars']['food_service']);
            }
        }
    }
    else
    {
        unset($_REQUEST['modfunc']);
        unset($_REQUEST['food_service']);
        unset($_SESSION['_REQUEST_vars']['food_service']);
    }
}

Widgets('fsa_discount');
Widgets('fsa_status');
Widgets('fsa_barcode');
Widgets('fsa_account_id');

$extra['SELECT'] .= ",coalesce(fssa.STATUS,'Active') AS STATUS";
$extra['SELECT'] .= ",(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fssa.ACCOUNT_ID) AS BALANCE";
if(!strpos($extra['FROM'],'fssa'))
{
	$extra['FROM'] .= ",FOOD_SERVICE_STUDENT_ACCOUNTS fssa";
	$extra['WHERE'] .= " AND fssa.STUDENT_ID=s.STUDENT_ID";
}
$extra['functions'] += array('BALANCE'=>'red');
$extra['columns_after'] = array('BALANCE'=>'Balance','STATUS'=>'Status');

Search('student_id',$extra);

if(!$_REQUEST['modfunc'] && UserStudentID())
{
	$student = DBGet(DBQuery("SELECT s.STUDENT_ID,".(Preferences('NAME')=='Common'?'coalesce(s.CUSTOM_200000002,s.FIRST_NAME)':'s.FIRST_NAME')."||' '||s.LAST_NAME AS FULL_NAME,fssa.ACCOUNT_ID,fssa.STATUS,fssa.DISCOUNT,fssa.BARCODE,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fssa.ACCOUNT_ID) AS BALANCE FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE s.STUDENT_ID='".UserStudentID()."' AND fssa.STUDENT_ID=s.STUDENT_ID"));
	$student = $student[1];

	// find other students associated with the same account
	$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,".(Preferences('NAME')=='Common'?'coalesce(s.CUSTOM_200000002,s.FIRST_NAME)':'s.FIRST_NAME')."||' '||s.LAST_NAME AS FULL_NAME FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE fssa.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND s.STUDENT_ID=fssa.STUDENT_ID AND s.STUDENT_ID!='".UserStudentID()."'".($_REQUEST['include_inactive']?'':" AND exists(SELECT '' FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=s.STUDENT_ID AND SYEAR='".UserSyear()."' AND (START_DATE<=CURRENT_DATE AND (END_DATE IS NULL OR CURRENT_DATE<=END_DATE)))")));

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";

	DrawHeader(CheckBoxOnclick('include_inactive')._('Include Inactive Students in Shared Account'),SubmitButton(_('Save')));

	echo '<BR>';
	PopTable('header',_('Account Information'),'width=100%');
	echo '<TABLE width=100%>';
	echo '<TR>';
	echo '<TD valign=top>';
	echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>'.NoInput($student['FULL_NAME'],'<b>'.$student['STUDENT_ID'].'</b>').'</TD>';
	echo '<TD valign=top>'.NoInput(red($student['BALANCE']),_('Balance')).'</TD>';

	echo '</TR></TABLE>';
	echo '</TD></TR></TABLE>';
	echo '<HR>';

	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0>';
	echo '<TR><TD valign=top>';

	echo '<TABLE border=0 cellpadding=6 width=100%>';
	echo '<TR>';
	echo '<TD>';
	// warn if account non-existent (balance query failed)
	if($student['BALANCE']=='')
	{
		echo TextInput(array($student['ACCOUNT_ID'],'<FONT color=red>'.$student['ACCOUNT_ID'].'</FONT>'),'food_service[ACCOUNT_ID]','Account ID','size=12 maxlength=10');
		$warning = _('Non-existent account!');
		echo button('warning','','# onMouseOver=\'stm(["'._('Warning').'","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	else
	 	echo TextInput($student['ACCOUNT_ID'],'food_service[ACCOUNT_ID]',_('Account ID'),'size=12 maxlength=10');
	// warn if other students associated with the same account
	if(count($xstudents))
	{
		$warning = Localize('colon',_('Other students associated with same account')).'<BR>';
		foreach($xstudents as $xstudent)
			$warning .= '&nbsp;'.str_replace('\'','&#39;',$xstudent['FULL_NAME']).'<BR>';
		echo button('warning','','# onMouseOver=\'stm(["'._('Warning').'","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	echo '</TD>';
	$options = array('Inactive'=>_('Inactive'),'Disabled'=>_('Disabled'),'Closed'=>_('Closed'));
	echo '<TD>'.SelectInput($student['STATUS'],'food_service[STATUS]',_('Status'),$options,_('Active')).'</TD>';
	echo '</TR><TR>';
	$options = array('Reduced'=>_('Reduced'),'Free'=>_('Free'));
	echo '<TD>'.SelectInput($student['DISCOUNT'],'food_service[DISCOUNT]',_('Discount'),$options,_('Full')).'</TD>';
	echo '<TD>'.TextInput($student['BARCODE'],'food_service[BARCODE]',_('Barcode'),'size=12 maxlength=25').'</TD>';
	echo '</TR>';
	echo '</TABLE>';

	echo '</TD></TR>';
	echo '</TABLE>';
	PopTable('footer');
	echo '<CENTER>'.SubmitButton(_('Save')).'</CENTER>';
	echo '</FORM>';
}
?>
