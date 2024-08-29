<?php

// hack to help assign school_id's to old transactions and until kiosk is updated
if(User('PROFILE')=='admin')
{
	// if there is only one school in then use it
	$schools_RET = DBGet(DBQuery("SELECT ID,SYEAR FROM SCHOOLS"),array(),array('SYEAR'));
	foreach($schools_RET as $syear=>$schools)
		if(count($schools)==1)
		{
			DBQuery("UPDATE FOOD_SERVICE_TRANSACTIONS SET SCHOOL_ID='".$schools[1]['ID']."' WHERE SYEAR='".$syear."' AND SCHOOL_ID IS NULL");
			DBQuery("UPDATE FOOD_SERVICE_STAFF_TRANSACTIONS SET SCHOOL_ID='".$schools[1]['ID']."' WHERE SYEAR='".$syear."' AND SCHOOL_ID IS NULL");
		}
	// if transaction has student_id then use enrolled school at time of transaction
	DBQuery("UPDATE FOOD_SERVICE_TRANSACTIONS SET SCHOOL_ID=(SELECT ssm.SCHOOL_ID FROM STUDENT_ENROLLMENT ssm WHERE ssm.STUDENT_ID=food_service_transactions.STUDENT_ID AND ssm.SYEAR=food_service_transactions.SYEAR AND food_service_transactions.TIMESTAMP>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR food_service_transactions.TIMESTAMP<ssm.END_DATE+1)) WHERE SCHOOL_ID IS NULL AND STUDENT_ID IS NOT NULL");
	// if transaction does not have student_id but account has only one student then for that student
	// use enrolled school at time of transaction
	DBQuery("UPDATE FOOD_SERVICE_TRANSACTIONS SET SCHOOL_ID=(SELECT ssm.SCHOOL_ID FROM STUDENT_ENROLLMENT ssm WHERE ssm.STUDENT_ID=(SELECT fsa.STUDENT_ID FROM FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE fsa.ACCOUNT_ID=food_service_transactions.ACCOUNT_ID) AND ssm.SYEAR=food_service_transactions.SYEAR AND food_service_transactions.TIMESTAMP>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR food_service_transactions.TIMESTAMP<ssm.END_DATE+1)) WHERE SCHOOL_ID IS NULL AND STUDENT_ID IS NULL AND (SELECT count(1) FROM FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE fsa.ACCOUNT_ID=food_service_transactions.ACCOUNT_ID)=1");
	// for staff, use their schools setting if it is unique
	$staff_RET = DBGet(DBQuery("SELECT TRANSACTION_ID,(SELECT SCHOOLS FROM STAFF s WHERE s.STAFF_ID=fst.STAFF_ID) AS SCHOOL_ID FROM FOOD_SERVICE_STAFF_TRANSACTIONS fst WHERE fst.SCHOOL_ID IS NULL"),array('SCHOOL_ID'=>'_make_school_id'));
	foreach($staff_RET as $transaction)
		if($transaction['SCHOOL_ID'])
			DBQuery("UPDATE FOOD_SERVICE_STAFF_TRANSACTIONS SET SCHOOL_ID='".$transaction['SCHOOL_ID']."' WHERE TRANSACTION_ID='".$transaction['TRANSACTION_ID']."'");
	unset($schools_RET);
	unset($staff_RET);
}

if(User('PROFILE')=='admin')
{
	$RET = DBGet(DBQuery("SELECT count(1) AS COUNT FROM FOOD_SERVICE_TRANSACTIONS WHERE SCHOOL_ID IS NULL UNION SELECT count(1) FROM FOOD_SERVICE_STAFF_TRANSACTIONS WHERE SCHOOL_ID IS NULL"));

	//if (!$_SESSION['FSA_type'])
		$_SESSION['FSA_type'] = 'student';

	if(($RET[1]['COUNT']>0 || $RET[2]['COUNT']>0) && AllowUse('Food_Service/AssignSchool.php'))
		$_REQUEST['modname'] = 'Food_Service/AssignSchool.php';
	else
		$_REQUEST['modname'] = 'Food_Service/Accounts.php';
}
else
{
	//if (!$_SESSION['FSA_type'])
		$_SESSION['FSA_type'] = 'student';

	$_REQUEST['modname'] = 'Food_Service/Accounts.php';
	//$_REQUEST['modname'] = 'Food_Service/TakeMenuCounts.php';
}

$modcat = 'Food_Service';
echo "<SCRIPT language=javascript>parent.help.location=\"Bottom.php?modcat=$modcat&modname=$_REQUEST[modname]\";</SCRIPT>";
include("modules/$_REQUEST[modname]");

function _make_school_id($value,$column)
{
	if($value!='')
	{
		$value = trim($value,',');
		if(strpos($value,','))
			$value = '';
	}
	return $value;
}
?>
