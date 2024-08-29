<?php
// Establish DB connection.
function db_start()
{	global $DatabaseServer,$DatabaseUsername,$DatabasePassword,$DatabaseName,$DatabasePort,$DatabaseType;

	switch($DatabaseType)
	{
		case 'oracle':
			$connection = @ocilogon($DatabaseUsername,$DatabasePassword,$DatabaseServer);
		break;
		case 'postgres':
			if($DatabaseServer!='localhost')
				$connectstring = "host=$DatabaseServer ";
			if($DatabasePort!='5432')
				$connectstring .= "port=$DatabasePort ";
			$connectstring .= "dbname=$DatabaseName user=$DatabaseUsername";
			if(!empty($DatabasePassword))
				$connectstring.=" password=$DatabasePassword";
			$connection = pg_connect($connectstring);
		break;
		case 'mysql':
			$connection = mysql_connect($DatabaseServer,$DatabaseUsername,$DatabasePassword);
			mysql_select_db($DatabaseName);
		break;
	}

	// Error code for both.
	if($connection===false)
	{
		switch($DatabaseType)
		{
			case 'oracle':
				$errors = OciError();
				$errormessage = $errors['message'];
			break;
			case 'postgres':
				$errormessage = pg_last_error($connection);
			break;
			case 'mysql':
				$errormessage = mysql_error($connection);
			break;
		}
        // TRANSLATION: do NOT translate these since error messages need to stay in English for technical support
    	db_show_error("",sprintf('Could not Connect to Database Server \'%s\'',$DatabaseServer),$errstring);
	}
	return $connection;
}

// This function connects, and does the passed query, then returns a connection identifier.
// Not receiving the return == unusable search.
//		ie, $processable_results = DBQuery("select * from students");
function DBQuery($sql)
{	global $DatabaseType;

	$connection = db_start();

	switch($DatabaseType)
	{
		case 'oracle':
			$result = @ociparse($connection, $sql);
            // TRANSLATION: do NOT translate these since error messages need to stay in English for technical support
			if($result === false)
			{
				$errors = OCIError($connection);
				db_show_error($sql,"DB Parse Failed.", $errors['message']);
			}
			if(!@OciExecute($result))
			{
				$errors = OCIError($result);
				db_show_error($sql,"DB Execute Failed.", $errors['message']);
			}
			OciCommit($connection);
			OciLogoff($connection);
		break;
		case 'postgres':
            // TRANSLATION: do NOT translate these since error messages need to stay in English for technical support
			$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
			$result = @pg_exec($connection,$sql);
			if($result===false)
			{
				$errstring = pg_last_error($connection);
				db_show_error($sql,"DB Execute Failed.",$errstring);
			}
		break;
		case 'mysql':
            // TRANSLATION: do NOT translate these since error messages need to stay in English for technical support
			mysql_query("SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE");
			mysql_query("SET SESSION SQL_MODE='ANSI'");
			$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);

			$tbls = array("/ADDRESS/","/address_FIELD_CATEGORIES/","/address_FIELDS/","/ASSESSMENT_EXAMS/","/ASSESSMENT_SCORES/","/ASSESSMENT_SECTIONS/","/ATTENDANCE_CALENDAR/","/attendance_calendarS/","/ATTENDANCE_CODE_CATEGORIES/","/ATTENDANCE_CODES/","/ATTENDANCE_COMPLETED/","/ATTENDANCE_DAY/","/ATTENDANCE_PERIOD/","/BILLING_ACCOUNTS/","/BILLING_ACCOUNTS_JOIN_STUDENTS/","/BILLING_BILL_ITEMS/","/BILLING_BILLS/","/BILLING_FEE_CATEGORIES/","/BILLING_FEES/","/billing_fees_CATEGORIES/","/BILLING_PAYMENTS/","/BILLING_PLAN_DATES/","/BILLING_PLANS/","/BILLING_TRANSACTION_ITEMS/","/BILLING_TRANSACTIONS/","/CALENDAR_EVENTS/","/CONFIG/","/COURSE_DETAILS/","/COURSE_PERIODS/","/COURSE_SUBJECTS/","/COURSES/","/CUSTOM/","/custom_FIELDS/","/DISCIPLINE_CATEGORIES/","/DISCIPLINE_REFERRALS/","/ELIGIBILITY/","/eligibility_ACTIVITIES/","/eligibility_COMPLETED/","/ENROLL_GRADE/","/FOOD_SERVICE_ACCOUNTS/","/FOOD_SERVICE_CATEGORIES/","/FOOD_SERVICE_ITEMS/","/FOOD_SERVICE_MENU_ITEMS/","/FOOD_SERVICE_MENUS/","/FOOD_SERVICE_STAFF_ACCOUNTS/","/FOOD_SERVICE_STAFF_TRANSACTION_ITEMS/","/FOOD_SERVICE_STAFF_TRANSACTIONS/","/FOOD_SERVICE_STUDENT_ACCOUNTS/","/FOOD_SERVICE_TRANSACTION_ITEMS/","/FOOD_SERVICE_TRANSACTIONS/","/GRADEBOOK_ASSIGNMENT_TYPES/","/GRADEBOOK_ASSIGNMENTS/","/GRADEBOOK_GRADES/","/GRADES_COMPLETED/","/HISTORY_MARKING_PERIODS/","/HOMEWORK_PERIOD/","/LUNCH_CONFIG/","/LUNCH_MENU/","/LUNCH_MENU_CATEGORIES/","/LUNCH_PERIOD/","/LUNCH_TRANSACTIONS/","/LUNCH_USERS/","/MARKING_PERIODS/","/PEOPLE/","/people_FIELD_CATEGORIES/","/people_FIELDS/","/people_JOIN_CONTACTS/","/PORTAL_NOTES/","/PROFILE_EXCEPTIONS/","/PROGRAM_CONFIG/","/PROGRAM_USER_config/","/REPORT_CARD_COMMENT_CATEGORIES/","/REPORT_CARD_COMMENT_CODE_SCALES/","/REPORT_CARD_COMMENT_CODES/","/REPORT_CARD_COMMENTS/","/REPORT_CARD_GRADE_SCALES/","/REPORT_CARD_GRADES/","/SCHEDULE/","/schedule_REQUESTS/","/SCHOOL_GRADELEVELS/","/SCHOOL_marking_periods/","/SCHOOL_PERIODS/","/SCHOOLS/","/STAFF/","/staff_EXCEPTIONS/","/staff_FIELD_CATEGORIES/","/staff_FIELDS/","/STUDENT_eligibility_activities/","/STUDENT_ENROLLMENT/","/student_enrollment_CODES/","/STUDENT_FIELD_CATEGORIES/","/STUDENT_GPA_CALCULATED/","/STUDENT_GPA_RUNNING/","/STUDENT_MEDICAL/","/student_medical_ALERTS/","/student_medical_VISITS/","/STUDENT_MP_COMMENTS/","/STUDENT_MP_STATS/","/STUDENT_report_card_comments/","/STUDENT_report_card_grades/","/TABLES_IN_CSBETA/","/STUDENT_TEST_CATEGORIES/","/STUDENT_TEST_SCORES/","/STUDENTS/","/students_JOIN_address/","/students_JOIN_FEES/","/students_JOIN_people/","/students_JOIN_USERS/","/TRANSCRIPT_GRADES/","/USER_PROFILES/","/VOLUNTEER_LOG/","/WORKBOOK_PERIOD/");
			
			$tbl_patterns = array("address","address_field_categories","address_fields","assessment_exams","assessment_scores","assessment_sections","attendance_calendar","attendance_calendars","attendance_code_categories","attendance_codes","attendance_completed","attendance_day","attendance_period","billing_accounts","billing_accounts_join_students","billing_bill_items","billing_bills","billing_fee_categories","billing_fees","billing_fees_categories","billing_payments","billing_plan_dates","billing_plans","billing_transaction_items","billing_transactions","calendar_events","config","course_details","course_periods","course_subjects","courses","custom","custom_fields","discipline_categories","discipline_referrals","eligibility","eligibility_activities","eligibility_completed","enroll_grade","food_service_accounts","food_service_categories","food_service_items","food_service_menu_items","food_service_menus","food_service_staff_accounts","food_service_staff_transaction_items","food_service_staff_transactions","food_service_student_accounts","food_service_transaction_items","food_service_transactions","gradebook_assignment_types","gradebook_assignments","gradebook_grades","grades_completed","history_marking_periods","homework_period","lunch_config","lunch_menu","lunch_menu_categories","lunch_period","lunch_transactions","lunch_users","marking_periods","people","people_field_categories","people_fields","people_join_contacts","portal_notes","profile_exceptions","program_config","program_user_config","report_card_comment_categories","report_card_comment_code_scales","report_card_comment_codes","report_card_comments","report_card_grade_scales","report_card_grades","schedule","schedule_requests","school_gradelevels","school_marking_periods","school_periods","schools","staff","staff_exceptions","staff_field_categories","staff_fields","student_eligibility_activities","student_enrollment","student_enrollment_codes","student_field_categories","student_gpa_calculated","student_gpa_running","student_medical","student_medical_alerts","student_medical_visits","student_mp_comments","student_mp_stats","student_report_card_comments","student_report_card_grades","tables_in_csbeta","student_test_categories","student_test_scores","students","students_join_address","students_join_fees","students_join_people","students_join_users","transcript_grades","user_profiles","volunteer_log","workbook_period");
			
			$sql = preg_replace($tbls, $tbl_patterns, $sql);
			$result = mysql_query($sql);
			#echo $sql.'<BR><BR>'; // nick
			if($result===false)
			{
				$errstring = mysql_error();
				db_show_error($sql,"DB Execute Failed.",$errstring);
			}
		break;
	}
	return $result;
}

// return next row.
function db_fetch_row($result)
{	global $DatabaseType;

	switch($DatabaseType)
	{
		case 'oracle':
			OCIFetchInto($result,$row,OCI_ASSOC+OCI_RETURN_NULLS);
			$return = $row;
		break;
		case 'postgres':
			$return = @pg_fetch_array($result);
			if(is_array($return))
			{
				foreach($return as $key => $value)
				{
					if(is_int($key))
						unset($return[$key]);
				}
			}
		break;
		case 'mysql':
			$return = mysql_fetch_array($result);
			if(is_array($return))
			{
				foreach($return as $key => $value)
				{
					if(is_int($key))
						unset($return[$key]);
				}
			}
		break;
	}
	return @array_change_key_case($return,CASE_UPPER);
}

// returns code to go into SQL statement for accessing the next value of a sequenc	function db_nextval($seqname)
function db_nextval($seqname)
{	global $DatabaseName, $DatabaseType;

	if($DatabaseType=='oracle')
		$seq = $seqname.".nextval";
	elseif($DatabaseType=='postgres')
		$seq = "nextval('".$seqname."')";
	elseif($DatabaseType=='mysql')
	{
		$seqname = str_replace("_SEQ", "", $seqname);
		$results = db_fetch_row(DBQuery("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$DatabaseName."' AND TABLE_NAME = '".$seqname."'"));
		$seq = $results['AUTO_INCREMENT'];
		//echo "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$DatabaseName."' AND TABLE_NAME = '".$seqname."'";
		//echo $seq;
	}

	return $seq;
}

// start transaction
function db_trans_start($connection)
{	global $DatabaseType;

	if($DatabaseType=='postres')
		db_trans_query($connection,"BEGIN WORK");
}

// run query on transaction -- if failure, runs rollback.
function db_trans_query($connection,$sql)
{	global $DatabaseType;

    // TRANSLATION: do NOT translate these since error messages need to stay in English for technical support
	if($DatabaseType=='oracle')
	{
		$parse = ociparse($connection,$sql);
		if($parse===false)
		{
			db_trans_rollback($connection);
			db_show_error($sql,"DB Transaction Parse Failed.");
		}
		$result=OciExecute($parse,OCI_DEFAULT);
		if ($result===false)
		{
			db_trans_rollback($connection);
			db_show_error($sql,"DB Transaction Execute Failed.");
		}
		$result=$parse;
	}
	elseif($DatabaseType=='postgres')
	{
		$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
		$result = pg_query($connection,$sql);
		if($result===false)
		{
			db_trans_rollback($connection);
			db_show_error($sql,"DB Transaction Execute Failed.");
		}
	}

	return $result;
}

// rollback commands.
function db_trans_rollback($connection)
{	global $DatabaseType;

	if($DatabaseType=='oracle')
		OCIRollback($connection);
	elseif($DatabaseType=='postgres')
		pg_query($connection,"ROLLBACK");
}

// commit changes.
function db_trans_commit($connection)
{	global $DatabaseType;

	if($DatabaseType=='oracle')
		OCICommit($connection);
	elseif($DatabaseType=='postgres')
		pg_query($connection,"COMMIT");
}

// keyword mapping.
if($DatabaseType=='oracle')
	define("FROM_DUAL"," FROM DUAL ");
else
	define("FROM_DUAL"," ");

// DECODE and CASE-WHEN support
function db_case($array)
{	global $DatabaseType;

	$counter=0;
	switch($DatabaseType)
	{
		case 'oracle':
			$string=" decode( ";
			foreach($array as $value)
				$string.="$value,";
			$string[strlen($string)-1]=")";
			$string.=" ";
		break;
		case 'postgres':
		case 'mysql':
			$array_count=count($array);
			$string = " CASE WHEN $array[0] =";
			$counter++;
			$arr_count = count($array);
			for($i=1;$i<$arr_count;$i++)
			{
				$value = $array[$i];

				if($value=="''" && substr($string,-1)=='=')
				{
					$value = ' IS NULL';
					$string = substr($string,0,-1);
				}

				$string.="$value";
				if($counter==($array_count-2) && $array_count%2==0)
					$string.=" ELSE ";
				elseif($counter==($array_count-1))
					$string.=" END ";
				elseif($counter%2==0)
					$string.=" WHEN $array[0]=";
				elseif($counter%2==1)
					$string.=" THEN ";

				$counter++;
			}
	}
	return $string;
}

// String position.
function db_strpos($args)
{	global $DatabaseType;

	if($DatabaseType=='postgres')
		$ret = 'strpos(';
	else
		$ret = 'instr(';

	foreach($args as $value)
		$ret .= $value . ',';
	$ret = substr($ret,0,-1) . ')';

	return $ret;
}

// CONVERT VARCHAR TO NUMERIC
function db_to_number($text)
{	global $DatabaseType;

	if($DatabaseType=='postgres')
		return '('.$text.')::text::float::numeric';
	else
		return 'to_number('.$text.')';
}

// greatest/least - builtin to postgres 8 but not 7
function db_greatest($a,$b)
{	global $DatabaseType;

	if($DatabaseType=='postgres')
		return "(CASE WHEN $a IS NOT NULL AND $b IS NOT NULL THEN (CASE WHEN $a>$b THEN $a ELSE $b END) WHEN $a IS NOT NULL AND $b IS NULL THEN $a WHEN $a IS NULL AND $b IS NOT NULL THEN $b ELSE NULL END)";
	else
		return "greatest($a,$b)";
}

function db_least($a,$b)
{	global $DatabaseType;

	if($DatabaseType=='postgres')
		return "(CASE WHEN $a IS NOT NULL AND $b IS NOT NULL THEN (CASE WHEN $a<$b THEN $a ELSE $b END) WHEN $a IS NOT NULL AND $b IS NULL THEN $a WHEN $a IS NULL AND $b IS NOT NULL THEN $b ELSE NULL END)";
	else
		return "least($a,$b)";
}

// returns an array with the field names for the specified table as key with subkeys
// of SIZE, TYPE, SCALE and NULL.  TYPE: varchar, numeric, etc.
function db_properties($table)
{	global $DatabaseType,$DatabaseUsername;

	switch($DatabaseType)
	{
		case 'oracle':
			$sql="SELECT COLUMN_NAME, DATA_TYPE, DATA_LENGTH, DATA_PRECISION,
				DATA_SCALE, NULLABLE, DATA_DEFAULT
				FROM ALL_TAB_COLUMNS WHERE TABLE_NAME='".strtoupper($table)."'
				AND OWNER='".strtoupper($DatabaseUsername)."' ORDER BY COLUMN_ID";
			$result = DBQuery($sql);
			while($row=db_fetch_row($result))
			{
				if($row['DATA_TYPE']=='VARCHAR2')
				{
					$properties[$row['COLUMN_NAME']]['TYPE'] = "VARCHAR";
					$properties[$row['COLUMN_NAME']]['SIZE'] = $row['DATA_LENGTH'];
				}
				elseif($row['DATA_TYPE']=='NUMBER')
				{
					$properties[$row['COLUMN_NAME']]['TYPE'] = "NUMERIC";
					$properties[$row['COLUMN_NAME']]['SIZE'] = $row['DATA_PRECISION'];
					$properties[$row['COLUMN_NAME']]['SCALE'] = $row['DATA_SCALE'];
				}
				else
				{
					$properties[$row['COLUMN_NAME']]['TYPE'] = $row['DATA_TYPE'];
					$properties[$row['COLUMN_NAME']]['SIZE'] = $row['DATA_LENGTH'];
					$properties[$row['COLUMN_NAME']]['SCALE'] = $row['DATA_SCALE'];
				}
				$properties[$row['COLUMN_NAME']]['NULL'] = $row['NULLABLE'];
			}
		break;
		case 'postgres':
			$sql = "SELECT a.attnum,a.attname AS field,t.typname AS type,
					a.attlen AS length,a.atttypmod AS lengthvar,
					a.attnotnull AS notnull
				FROM pg_class c, pg_attribute a, pg_type t
				WHERE c.relname = '".strtolower($table)."'
					and a.attnum > 0 and a.attrelid = c.oid
					and a.atttypid = t.oid ORDER BY a.attnum";
			$result = DBQuery($sql);
			while($row = db_fetch_row($result))
			{
				$properties[strtoupper($row['FIELD'])]['TYPE'] = strtoupper($row['TYPE']);
				if(strtoupper($row['TYPE'])=="NUMERIC")
				{
					$properties[strtoupper($row['FIELD'])]['SIZE'] = ($row['LENGTHVAR'] >> 16) & 0xffff;
					$properties[strtoupper($row['FIELD'])]['SCALE'] = ($row['LENGTHVAR'] -4) & 0xffff;
				}
				else
				{
					if($row['LENGTH']>0)
						$properties[strtoupper($row['FIELD'])]['SIZE'] = $row['LENGTH'];
					elseif($row['LENGTHVAR']>0)
						$properties[strtoupper($row['FIELD'])]['SIZE'] = $row['LENGTHVAR']-4;
				}
				if ($row['NOTNULL']=='t')
					$properties[strtoupper($row['FIELD'])]['NULL'] = "N";
				else
					$properties[strtoupper($row['FIELD'])]['NULL'] = "Y";
			}
		break;
		case 'mysql':
			$result = DBQuery("SHOW COLUMNS FROM $table");
			while($row = db_fetch_row($result))
			{
				$properties[strtoupper($row['FIELD'])]['TYPE'] = strtoupper($row['TYPE'],strpos($row['TYPE'],'('));
				if(!$pos = strpos($row['TYPE'],','))
					$pos = strpos($row['TYPE'],')');
				else
					$properties[strtoupper($row['FIELD'])]['SCALE'] = substr($row['TYPE'],$pos+1);

				$properties[strtoupper($row['FIELD'])]['SIZE'] = substr($row['TYPE'],strpos($row['TYPE'],'(')+1,$pos);

				if($row['NULL']!='')
					$properties[strtoupper($row['FIELD'])]['NULL'] = "Y";
				else
					$properties[strtoupper($row['FIELD'])]['NULL'] = "N";
			}
		break;
	}
	return $properties;
}

function db_show_error($sql,$failnote,$additional='')
{	global $CentreTitle,$CentreVersion,$CentreNotifyAddress;

    echo '<BR>';
	PopTable('header',_('We have a problem, please contact technical support ...'));
    // TRANSLATION: do NOT translate these since error messages need to stay in English for technical support
	echo "
		<TABLE CELLSPACING=10 BORDER=0>
			<TD align=right><b>Date:</TD>
			<TD><pre>".date("m/d/Y h:i:s")."</pre></TD>
		</TR><TR>
			<TD align=right><b>Failure Notice:</b></TD>
			<TD><pre> $failnote </pre></TD>
		</TR><TR>
			<TD align=right><b>Additional Information:</b></TD>
			<TD>$additional</TD>
		</TR>
		</TABLE>";
	//Something you have asked the system to do has thrown a database error.  A system administrator has been notified, and the problem will be fixed as soon as possible.  It might be that changing the input parameters sent to this program will cause it to run properly.  Thanks for your patience.
	PopTable('footer');
	echo "<!-- SQL STATEMENT: \n\n $sql \n\n -->";

	if(false && function_exists('mysql_query'))
	{
		$link = @mysql_connect('augie.miller-group.net','centre_log','centre_log');
		@mysql_select_db('centre_log');
		@mysql_query("INSERT INTO SQL_ERROR_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME,SQL,REQUEST) values('$_SERVER[SERVER_NAME]','$_SERVER[SERVER_ADDR]','".date('Y-m-d')."','$CentreVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."','$sql','".ShowVar($_REQUEST,'Y', 'N')."')");
		@mysql_close($link);
	}

	if($CentreNotifyAddress)
	{
		$message = "System: $CentreTitle \n";
		$message .= "Date: ".date("m/d/Y h:i:s")."\n";
		$message .= "Page: ".$_SERVER['PHP_SELF'].' '.ProgramTitle()." \n\n";
		$message .= "Failure Notice:  $failnote \n";
		$message .= "Additional Info: $additional \n";
		$message .= "\n $sql \n";
		$message .= "Request Array: \n".ShowVar($_REQUEST,'Y', 'N');
		$message .= "\n\nSession Array: \n".ShowVar($_SESSION,'Y', 'N');
		mail($CentreNotifyAddress,'Centre Database Error',$message);
	}

	die();
}

// $safe_string = DBEscapeString($string).  Escapes single quotes by using two for every
// one.  Requires preg support in PHP.

function DBEscapeString($input)
{
	return preg_replace("/'/","''",$input);
}

function EncryptPWD($str)
{
	$key = 'password to (en/de)crypt';
	$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $str, MCRYPT_MODE_CBC, md5(md5($key))));
	return $encrypted;
}

function DecryptPWD($str)
{
	$key = 'password to (en/de)crypt';
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($str), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	return $decrypted;
}


?>