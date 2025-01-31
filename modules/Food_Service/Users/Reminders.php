<?php

$target = '19.00';
$warning = '5.00';
$warning_note = _('Your lunch account is getting low.  Please send in at least %P with your reminder slip.  THANK YOU!');
$negative_note = _('You now have a <B>negative balance</B> in your lunch account. Please send in the negative balance plus %T.  THANK YOU!');
$minimum = '-40.00';
$minimum_note = _('You now have a <b>negative balance</b> below the allowed minimum.  Please send in the negative balance plus %T.  THANK YOU!');

if($_REQUEST['staff_id'])
	unset($_REQUEST['staff_id']);
if($_SESSION['staff_id'])
{
	unset($_SESSION['staff_id']);
	echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'";</script>';
}

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = "'".implode("','",$_REQUEST['st_arr'])."'";

	$school = DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
	$school = $school[1]['TITLE'];

	$staffs = DBGet(DBQuery("SELECT s.STAFF_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.PROFILE,fsa.STATUS,fsa.BALANCE FROM staff s,FOOD_SERVICE_STAFF_ACCOUNTS fsa WHERE s.STAFF_ID IN (".$st_list.") AND fsa.STAFF_ID=s.STAFF_ID"));
	$handle = PDFStart();
	foreach($staffs as $staff)
	{
		$last_deposit = DBGet(DBQuery("SELECT (SELECT sum(AMOUNT) FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,DATE_FORMAT(fst.TIMESTAMP,'%Y-%m-%d') AS DATE FROM FOOD_SERVICE_STAFF_TRANSACTIONS fst WHERE fst.SHORT_NAME='DEPOSIT' AND fst.STAFF_ID='".$staff['STAFF_ID']."' AND SYEAR='".UserSyear()."' ORDER BY fst.TRANSACTION_ID DESC LIMIT 1"),array('DATE'=>'ProperDate'));
		$last_deposit = $last_deposit[1];

		if($staff['BALANCE'] < $minimum)
			reminder($staff,$school,$target,$last_deposit,$minimum_note);
		elseif($staff['BALANCE'] < 0)
			reminder($staff,$school,$target,$last_deposit,$negative_note);
		elseif($staff['BALANCE'] < $warning)
			reminder($staff,$school,$target,$last_deposit,$warning_note);

		echo '<!-- NEED 3in -->';
	}
	PDFStop($handle);
	}
	else
	BackPrompt(_('You must choose at least one user'));
}

if(!$_REQUEST['modfunc'] || $_REQUEST['modfunc']=='list')
{
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&_CENTRE_PDF=true method=POST>";
		DrawHeader('',SubmitButton(_('Create Reminders for Selected Users')));
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STAFF_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y checked name=controller onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['new'] = true;
	$extra['options']['search'] = false;

	StaffWidgets('fsa_balance_warning');
	StaffWidgets('fsa_status');
	StaffWidgets('fsa_exists_Y');

	$extra['SELECT'] .= ',coalesce(fsa.STATUS,\'Active\') AS STATUS,fsa.BALANCE';
	$extra['SELECT'] .= ',(SELECT \'Y\' FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE fsa.BALANCE < \''.$warning.'\' AND fsa.BALANCE >= 0 LIMIT 1) AS WARNING';
	$extra['SELECT'] .= ',(SELECT \'Y\' FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE fsa.BALANCE < 0 AND fsa.BALANCE >= \''.$minimum.'\' LIMIT 1) AS NEGATIVE';
	$extra['SELECT'] .= ',(SELECT \'Y\' FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE fsa.BALANCE < '.$minimum.' LIMIT 1) AS MINIMUM';
	if(!strpos($extra['FROM'],'fsa'))
	{
		$extra['FROM'] .= ',FOOD_SERVICE_STAFF_ACCOUNTS fsa';
		$extra['WHERE'] .= ' AND fsa.STAFF_ID=s.STAFF_ID';
	}
	$extra['functions'] += array('BALANCE'=>'red','WARNING'=>'x','NEGATIVE'=>'x','MINIMUM'=>'x');
	$extra['columns_after'] = array('BALANCE'=>_('Balance'),'STATUS'=>_('Status'),'WARNING'=>_('Warning').'<br>'.$warning,'NEGATIVE'=>_('Negative'),'MINIMUM'=>_('Minimum').'<br>'.$minimum);

	Search('staff_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton(_('Create Reminders for Selected Users')).'</CENTER>';
		echo "</FORM>";
	}
}

function reminder($staff,$school,$target,$last_deposit,$note)
{
	$payment = $target - $staff['BALANCE'];
	if($payment < 0)
		return;;
	$payment = number_format($payment,2);

	echo '<TABLE width=100%>';
	echo '<TR><TD colspan=3 align=center><FONT size=+1><I><B>'._('Payment Reminder').'</B></I></FONT></TD></TR>';
	echo '<TR><TD colspan=3 align=center><B>'.$school.'</B></TD></TR>';

	echo '<TR><TD width=33%>';
	echo $staff['FIRST_NAME'].' '.$staff['MIDDLE_NAME'].' '.$staff['LAST_NAME'].'<BR>';
	echo '<small>'.$staff['STAFF_ID'].'</small>';
	echo '</TD><TD width=33%>';
	echo '&nbsp;<BR>';
	echo '<small>&nbsp;</small>';
	echo '</TD><TD width=33%>';
	echo '&nbsp;<BR>';
	echo '<small>&nbsp;</small>';
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
	echo ($staff['BALANCE']<0 ? '<B>'.$staff['BALANCE'].'</B>' : $staff['BALANCE']).'<BR>';
	echo '<small>'._('Balance').'</small>';
	echo '</TD><TD width=33%>';
	echo '<B>'.$payment.'</B><BR>';
	echo '<small><B>'._('Mimimum Payment').'</B></small>';
	echo '</TD><TD width=33%>';
	echo ucfirst($staff['PROFILE']).'<BR>';
	echo '<small>'._('Profile').'</small>';
	echo '</TD></TR>';

	$note = str_replace('%F',$staff['FIRST_NAME'],$note);
	$note = str_replace('%P',money_formatt('%i',$payment),$note);
	$note = str_replace('%T',$target,$note);

	echo '<TR><TD colspan=3>';
	echo '<BR>'.$note.'<BR>';
	echo '</TD></TR>';
	echo '<TR><TD colspan=3><BR><BR><HR><BR><BR></TD></TR></TABLE>';
}
?>
