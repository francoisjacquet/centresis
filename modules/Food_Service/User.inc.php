<?php

if($_REQUEST['modfunc']=='update')
{
	if(UserStaffID() && AllowEdit())
	{
		if(count($_REQUEST['food_service']))
		{
			$sql = "UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET ";
			foreach($_REQUEST['food_service'] as $column_name=>$value)
				$sql .= $column_name."='".str_replace("\'","''",trim($value))."',";
			$sql = substr($sql,0,-1)." WHERE STAFF_ID='".UserStaffID()."'";
			DBQuery($sql);
		}
	}
	//unset($_REQUEST['modfunc']);
	unset($_REQUEST['food_service']);
	unset($_SESSION['_REQUEST_vars']['food_service']);
}

if(!$_REQUEST['modfunc'] && UserStaffID())
{
	$staff = DBGet(DBQuery("SELECT s.STAFF_ID,s.FIRST_NAME||' '||s.LAST_NAME,(SELECT s.STAFF_ID FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS ACCOUNT_ID,(SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE,(SELECT BARCODE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BARCODE FROM STAFF s WHERE s.STAFF_ID='".UserStaffID()."'"));
	$staff = $staff[1];

	echo '<TABLE width=100%>';
	echo '<TR>';
	echo '<TD valign=top>';
	echo '<TABLE width=100%><TR>';

        echo '<TD valign=top>'.NoInput(($staff['BALANCE']<0?'<FONT color=red>':'').$staff['BALANCE'].($staff['BALANCE']<0?'</FONT>':''),'Balance');
	if(!$staff['ACCOUNT_ID'])
	{
		$warning = _('This user does not have a Meal Account.');
		echo '<BR>'.button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	echo '</TD>';

        echo '</TR></TABLE>';
        echo '</TD></TR></TABLE>';
        echo '<HR>';

	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0>';
	echo '<TR><TD valign=top>';

	echo '<TABLE border=0 cellpadding=6 width=100%>';
	echo '<TR>';
	$options = array('Inactive'=>_('Inactive'),'Disabled'=>_('Disabled'),'Closed'=>_('Closed'));
	echo '<TD>'.($staff['ACCOUNT_ID']?SelectInput($staff['STATUS'],'food_service[STATUS]',_('Status'),$options,_('Active')):NoInput('-',_('Status'))).'</TD>';
	echo '<TD>'.($staff['ACCOUNT_ID']?TextInput($staff['BARCODE'],'food_service[BARCODE]',_('Barcode'),'size=12 maxlength=25'):NoInput('-',_('Barcode'))).'</TD>';
	echo '</TR>';
	echo '</TABLE>';

	echo '</TD></TR>';
	echo '</TABLE>';
}
?>
