<?php

Widgets('fsa_discount');
Widgets('fsa_status');
Widgets('fsa_barcode');
Widgets('fsa_account_id');

$extra['SELECT'] .= ",coalesce(fssa.STATUS,'Active') AS STATUS";
$extra['SELECT'] .= ",(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fssa.ACCOUNT_ID) AS BALANCE";
if(!strpos($extra['FROM'],'fssa'))
{
	$extra['FROM'] = ",FOOD_SERVICE_STUDENT_ACCOUNTS fssa";
	$extra['WHERE'] .= " AND fssa.STUDENT_ID=s.STUDENT_ID";
}
$extra['functions'] += array('BALANCE'=>'red');
$extra['columns_after'] = array('BALANCE'=>_('Balance'),'STATUS'=>_('Status'));

Search('student_id',$extra);

if(UserStudentID())
{
	$student = DBGet(DBQuery("SELECT s.STUDENT_ID,CONCAT(".(Preferences('NAME')=='Common'?'coalesce(s.CUSTOM_200000002,s.FIRST_NAME)':'s.FIRST_NAME').",' ',s.LAST_NAME) AS FULL_NAME,fsa.ACCOUNT_ID,fsa.STATUS,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fsa.ACCOUNT_ID) AS BALANCE FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE s.STUDENT_ID='".UserStudentID()."' AND fsa.STUDENT_ID=s.STUDENT_ID"));
	$student = $student[1];

	// find other students associated with the same account
	$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID, CONCAT(".(Preferences('NAME')=='Common'?'coalesce(s.CUSTOM_200000002,s.FIRST_NAME)':'s.FIRST_NAME').",' ',s.LAST_NAME) AS FULL_NAME FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE fssa.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND s.STUDENT_ID=fssa.STUDENT_ID AND s.STUDENT_ID!='".UserStudentID()."' AND exists(SELECT '' FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=s.STUDENT_ID AND SYEAR='".UserSyear()."' AND (START_DATE<=CURRENT_DATE AND (END_DATE IS NULL OR CURRENT_DATE<=END_DATE)))"));

	if(count($xstudents))
	{
		$student_select = 'Student<SELECT name=student_select><OPTION value="">Not Specified</OPTION>';
		$student_select .= '<OPTION value='.$student['STUDENT_ID'].($_REQUEST['student_select']==$student['STUDENT_ID'] ? ' SELECTED' : '').'>'.$student['FULL_NAME'].'</OPTION>';
		foreach($xstudents as $xstudent)
			$student_select .= '<OPTION value='.$xstudent['STUDENT_ID'].($_REQUEST['student_select']==$xstudent['STUDENT_ID'] ? ' SELECTED' : '').'>'.$xstudent['FULL_NAME'].'</OPTION>';
		$student_select .= '</SELECT>';
	}

	echo '<FORM action='.PreparePHP_SELF().' method=POST>';
	DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($start_date))),'_start').' - '.PrepareDate(strtoupper(date("Y-m-d",strtotime($end_date))),'_end').' : '.$type_select.($student_select?' : '.$student_select:'').' : <INPUT type=submit value=Go>');
	echo '</FORM>';

	echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>'.NoInput($student['FULL_NAME'],'<b>'.$student['STUDENT_ID'].'</b>').'</TD>';
	echo '<TD valign=top>'.NoInput(red($student['BALANCE']),_('Balance')).'</TD>';

	echo '</TR></TABLE>';

	if($_REQUEST['detailed_view']!='true')
		DrawHeader('<A HREF='.PreparePHP_SELF($_REQUEST,array(),array('detailed_view'=>'true')).'>'._('Detailed View').'</A>');
	else
		DrawHeader('<A HREF='.PreparePHP_SELF($_REQUEST,array(),array('detailed_view'=>'false')).'>'._('Original View').'</A>');

	if($student['BALANCE'])
	{
		if($_REQUEST['student_select'])
			$where = " AND fst.STUDENT_ID='".$_REQUEST['student_select']."'";

		if($_REQUEST['type_select'])
			$where .= " AND fst.SHORT_NAME='".$_REQUEST['type_select']."'";

		if($_REQUEST['detailed_view']=='true')
		{
			$RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID AS TRANS_ID,fst.TRANSACTION_ID,fst.STUDENT_ID,fst.DISCOUNT,(SELECT sum(AMOUNT) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,fst.BALANCE,DATE_FORMAT(fst.TIMESTAMP,'%Y-%m-%d') AS DATE,DATE_FORMAT(fst.TIMESTAMP,'%h:%i:%S %p') AS TIME,fst.DESCRIPTION,".db_case(array('fst.STUDENT_ID',"''",'NULL',"(SELECT CONCAT(FIRST_NAME,' ',LAST_NAME) FROM STUDENTS WHERE STUDENT_ID=fst.STUDENT_ID)"))." AS STUDENT,".db_case(array('fst.SELLER_ID',"''",'NULL',"(SELECT CONCAT(FIRST_NAME,' ',LAST_NAME) FROM staff WHERE STAFF_ID=fst.SELLER_ID)"))." AS SELLER FROM FOOD_SERVICE_TRANSACTIONS fst WHERE fst.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND SYEAR='".UserSyear()."' AND fst.TIMESTAMP BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND date '".date("Y-m-d",strtotime('+1 day', strtotime($end_date)))."' ".$where." ORDER BY fst.TRANSACTION_ID DESC"),array('DATE'=>'ProperDate','BALANCE'=>'red'));
			// get details of each transaction
			foreach($RET as $key=>$value)
			{
				$tmpRET = DBGet(DBQuery("SELECT *, TRANSACTION_ID AS TRANS_ID FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='".$value['TRANSACTION_ID']."'"));
				// merge transaction and detail records
				$RET[$key] = array($RET[$key]) + $tmpRET;
			}
			$columns = array('TRANSACTION_ID'=>_('ID'),'STUDENT'=>_('Student'),'DATE'=>_('Date'),'TIME'=>_('Time'),'BALANCE'=>_('Balance'),'DISCOUNT'=>_('Discount'),'DESCRIPTION'=>_('Description'),'AMOUNT'=>_('Amount'),'SELLER'=>_('Seller'));
			$group = array(array('TRANSACTION_ID'));
			$link['remove']['link'] = PreparePHP_SELF($_REQUEST,array(),array('modfunc'=>'delete'));
			$link['remove']['variables'] = array('transaction_id'=>'TRANS_ID','item_id'=>'ITEM_ID');
		}
		else
		{
			$RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID,fst.DISCOUNT,(SELECT sum(AMOUNT) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,fst.BALANCE,DATE_FORMAT(fst.TIMESTAMP,'%Y-%m-%d') AS DATE,DATE_FORMAT(fst.TIMESTAMP,'%h:%i:%S %p') AS TIME,fst.DESCRIPTION FROM FOOD_SERVICE_TRANSACTIONS fst WHERE fst.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND SYEAR='".UserSyear()."' AND fst.TIMESTAMP BETWEEN '".date("Y-m-d",strtotime($start_date))."' AND date '".date("Y-m-d",strtotime('+1 day', strtotime($end_date)))."' ".$where." ORDER BY fst.TRANSACTION_ID DESC"),array('DATE'=>'ProperDate','BALANCE'=>'red'));
			$columns = array('TRANSACTION_ID'=>_('ID'),'DATE'=>_('Date'),'TIME'=>_('Time'),'BALANCE'=>_('Balance'),'DISCOUNT'=>_('Discount'),'DESCRIPTION'=>_('Description'),'AMOUNT'=>_('Amount'));
		}

		ListOutput($RET,$columns,'Transaction','Transactions',$link,$group);
	}
	else
		echo ErrorMessage(array('<IMG SRC=assets/x.gif align=absmiddle> '._('This student does not have a valid Meal Account.')));
}
?>
