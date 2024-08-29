<?php

function DeleteTransactionItem($transaction_id,$item_id,$type='student')
{
	if($_REQUEST['type']=='staff')
	{
		$amt_RET = DBGet(DBQuery("SELECT FORMAT(coalesce(sum(AMOUNT),0),0) AS AMT FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'"));
		$c_AMT = $amt_RET[1]['AMT'];
		$acct_RET = DBGet(DBQuery("SELECT ACCOUNT_ID FROM FOOD_SERVICE_STAFF_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id'"));
		$c_ACCT = $acct_RET[1]['ACCOUNT_ID'];
		
		$sql1 = "UPDATE FOOD_SERVICE_STAFF_TRANSACTIONS SET balance=balance-$c_AMT WHERE transaction_id>='$transaction_id' AND account_id='$c_ACCT'";

		//$sql1 = "UPDATE FOOD_SERVICE_STAFF_TRANSACTIONS SET BALANCE=BALANCE-(SELECT AMOUNT FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id') WHERE TRANSACTION_ID>='$transaction_id' AND STAFF_ID=(SELECT STAFF_ID FROM FOOD_SERVICE_STAFF_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id')";
		$sql2 = "UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET BALANCE=BALANCE-(SELECT AMOUNT FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id') WHERE STAFF_ID=(SELECT STAFF_ID FROM FOOD_SERVICE_STAFF_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id')";
		$sql3 = "DELETE FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'";
	}
	else
	{
		$amt_RET = DBGet(DBQuery("SELECT FORMAT(coalesce(sum(AMOUNT),0),0) AS AMT FROM food_service_transaction_items WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'"));
		$c_AMT = $amt_RET[1]['AMT'];
		$acct_RET = DBGet(DBQuery("SELECT ACCOUNT_ID FROM food_service_transactions WHERE TRANSACTION_ID='$transaction_id'"));
		$c_ACCT = $acct_RET[1]['ACCOUNT_ID'];
		
		$sql1 = "UPDATE food_service_transactions SET balance=balance-$c_AMT WHERE transaction_id>='$transaction_id' AND account_id='$c_ACCT'";

		//$sql1 = "UPDATE FOOD_SERVICE_TRANSACTIONS SET BALANCE=BALANCE-(SELECT AMOUNT FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id') WHERE TRANSACTION_ID>='$transaction_id' AND ACCOUNT_ID=(SELECT ACCOUNT_ID FROM FOOD_SERVICE_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id')";
		$sql2 = "UPDATE FOOD_SERVICE_ACCOUNTS SET BALANCE=BALANCE-(SELECT AMOUNT FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id') WHERE ACCOUNT_ID=(SELECT ACCOUNT_ID FROM FOOD_SERVICE_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id')";
		$sql3 = "DELETE FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'";
	}
	//DBQuery('BEGIN; '.$sql1.'; '.$sql2.'; '.$sql3.'; COMMIT');
	DBQuery('BEGIN;');
	DBQuery($sql1);
	DBQuery($sql2);
	DBQuery($sql3);
	DBQuery('COMMIT');
}
?>
