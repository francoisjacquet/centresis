<?php

if($_REQUEST['detailed_view']=='true')
{
    $RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID AS TRANS_ID,fst.TRANSACTION_ID,fst.STAFF_ID,fst.SHORT_NAME,(SELECT sum(AMOUNT) FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,fst.BALANCE,DATE_FORMAT(fst.TIMESTAMP,'%Y-%m-%d') AS DATE,DATE_FORMAT(fst.TIMESTAMP,'%h:%i:%S %p') AS TIME,fst.DESCRIPTION,(SELECT CONCAT(LAST_NAME,', ',FIRST_NAME) FROM staff WHERE STAFF_ID=fst.STAFF_ID) AS FULL_NAME,".db_case(array('fst.SELLER_ID',"''",'NULL',"(SELECT CONCAT(FIRST_NAME,' ',LAST_NAME) FROM staff WHERE STAFF_ID=fst.SELLER_ID)"))." AS SELLER FROM FOOD_SERVICE_STAFF_TRANSACTIONS fst WHERE SYEAR='".UserSyear()."' AND fst.TIMESTAMP BETWEEN '".date("Y-m-d",strtotime($date))."' AND date '".date("Y-m-d",strtotime('+1 day', strtotime($date)))."' AND SCHOOL_ID='".UserSchool()."'".$where."ORDER BY ".($_REQUEST['by_name']?'FULL_NAME,':'')."fst.TRANSACTION_ID DESC"),array('DATE'=>'ProperDate','SHORT_NAME'=>'bump_count'));
	foreach($RET as $key=>$value)
	{
		// get details of each transaction
		$tmpRET = DBGet(DBQuery("SELECT *, TRANSACTION_ID AS TRANS_ID,'".$value['SHORT_NAME']."' AS TRANSACTION_SHORT_NAME FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='".$value['TRANSACTION_ID']."'"),array('SHORT_NAME'=>'bump_items_count'));

		// merge transaction and detail records
		$RET[$key] = array($value) + $tmpRET;
	}
	//echo '<pre>'; var_dump($RET); echo '</pre>';
	$columns = array('TRANSACTION_ID'=>_('ID'),'FULL_NAME'=>_('User'),'DATE'=>_('Date'),'TIME'=>_('Time'),'BALANCE'=>_('Balance'),'DESCRIPTION'=>_('Description'),'AMOUNT'=>_('Amount'),'SELLER'=>_('User'));
	$group = array(array('TRANSACTION_ID'));
	$link['remove']['link'] = PreparePHP_SELF($_REQUEST,array(),array('modfunc'=>'delete'));
	$link['remove']['variables'] = array('transaction_id'=>'TRANS_ID','item_id'=>'ITEM_ID');
}
else
{
    $RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID,fst.STAFF_ID,fst.SHORT_NAME,(SELECT sum(AMOUNT) FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,fst.BALANCE,DATE_FORMAT(fst.TIMESTAMP,'%Y-%m-%d') AS DATE,DATE_FORMAT(fst.TIMESTAMP,'%h:%i:%S %p') AS TIME,fst.DESCRIPTION,(SELECT CONCAT(LAST_NAME,', ',FIRST_NAME) FROM staff WHERE STAFF_ID=fst.STAFF_ID) AS FULL_NAME FROM FOOD_SERVICE_STAFF_TRANSACTIONS fst WHERE SYEAR='".UserSyear()."' AND fst.TIMESTAMP BETWEEN '".date("Y-m-d",strtotime($date))."' AND date '".date("Y-m-d",strtotime('+1 day', strtotime($date)))."' AND SCHOOL_ID='".UserSchool()."'".$where."ORDER BY ".($_REQUEST['by_name']?'FULL_NAME,':'')."fst.TRANSACTION_ID DESC"),array('DATE'=>'ProperDate','SHORT_NAME'=>'bump_count'));
	$columns = array('TRANSACTION_ID'=>_('ID'),'FULL_NAME'=>_('User'),'DATE'=>_('Date'),'TIME'=>_('Time'),'BALANCE'=>_('Balance'),'DESCRIPTION'=>_('Description'),'AMOUNT'=>_('Amount'));
}
?>
