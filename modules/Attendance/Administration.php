<?php

DrawHeader(ProgramTitle());

if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
{
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
	if($_SESSION['Administration.php']['date'] && $_SESSION['Administration.php']['date']!=$date)
	{
		unset($_REQUEST['attendance']);
		unset($_REQUEST['attendance_day']);
	}
}
else
{
	//$date = DBDate();
	$date = date('Y-m-d');
	$_REQUEST['day_date'] = date('d');
	$_REQUEST['month_date'] = date('m');
	$_REQUEST['year_date'] = date('Y');
}

if($_REQUEST['table']=='')
	$_REQUEST['table'] = '0';

if($_REQUEST['table']=='0')
{
	$table = 'ATTENDANCE_PERIOD';
	$extra_sql = '';
}
else
{
	$table = 'LUNCH_PERIOD';
	$extra_sql = " AND TABLE_NAME='$_REQUEST[table]'";
}

$_SESSION['Administration.php']['date'] = $date;
$current_mp = GetCurrentMP('QTR',date("Y-m-d", strtotime($date)),false);
if(!$current_mp)
{
	echo "<FORM action=".PreparePHP_SELF($_REQUEST,array('day_date','month_date','year_date','codes'))." method=POST>";
	DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($date))),'_date',false,array('submit'=>true)));
	echo '</FORM>';
	ErrorMessage(array('<IMG SRC=assets/x.gif>The selected date is not in a school quarter.'),'fatal');
}

$all_mp = GetAllMP('QTR',$current_mp);

$current_Q = "SELECT ATTENDANCE_TEACHER_CODE,ATTENDANCE_CODE,ATTENDANCE_REASON,COMMENT,STUDENT_ID,ADMIN,PERIOD_ID FROM $table WHERE SCHOOL_DATE='".date("Y-m-d", strtotime($date))."'".$extra_sql;
$current_schedule_Q = "SELECT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,cp.HALF_DAY FROM SCHEDULE s,COURSE_PERIODS cp WHERE s.STUDENT_ID='__student_id__' AND s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID='".UserSchool()."' AND cp.COURSE_PERIOD_ID = s.COURSE_PERIOD_ID AND ( position(',$_REQUEST[table],' IN cp.DOES_ATTENDANCE)>0 OR position('Y' IN cp.DOES_ATTENDANCE)>0 ) AND ('".date("Y-m-d", strtotime($date))."' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND '".date("Y-m-d", strtotime($date))."'>=s.START_DATE)) AND position(substring('UMTWHFS' FROM cast(DAYOFWEEK(cast('".date("Y-m-d", strtotime($date))."' AS DATE)) AS SIGNED)+1 FOR 1) IN cp.DAYS)>0 AND s.MARKING_PERIOD_ID IN ($all_mp) ORDER BY s.START_DATE ASC";
$current_RET = DBGet(DBQuery($current_Q),array(),array('STUDENT_ID','PERIOD_ID'));
if($_REQUEST['attendance'] && $_POST['attendance'] && AllowEdit())
{
	foreach($_REQUEST['attendance'] as $student_id=>$values)
	{
		if(!$current_schedule_RET[$student_id])
		{
			$current_schedule_RET[$student_id] = DBGet(DBQuery(str_replace('__student_id__',$student_id,$current_schedule_Q)),array(),array('PERIOD_ID'));
			if(!$current_schedule_RET[$student_id])
				$current_schedule_RET[$student_id] = true;
		}

		foreach($values as $period_id=>$columns)
		{
			if($current_RET[$student_id][$period_id])
			{
				$sql = "UPDATE $table SET ADMIN='Y',COURSE_PERIOD_ID='".$current_schedule_RET[$student_id][$period_id][1]['COURSE_PERIOD_ID']."',";

				foreach($columns as $column=>$value)
					$sql .= $column."='".str_replace("\'","''",$value)."',";

				$sql = substr($sql,0,-1) . " WHERE SCHOOL_DATE='".date("Y-m-d", strtotime($date))."' AND PERIOD_ID='$period_id' AND STUDENT_ID='$student_id'".$extra_sql;
				DBQuery($sql);
			}
			else
			{
				$sql = "INSERT INTO $table ";

				$fields = 'STUDENT_ID,SCHOOL_DATE,PERIOD_ID,MARKING_PERIOD_ID,ADMIN,COURSE_PERIOD_ID,';
				$values = "'".$student_id."','".date("Y-m-d", strtotime($date))."','".$period_id."','".$current_mp."','Y','".$current_schedule_RET[$student_id][$period_id][1]['COURSE_PERIOD_ID']."',";
				if($table=='LUNCH_PERIOD')
				{
					$fields .= 'TABLE_NAME,';
					$values .= "'".$_REQUEST['table']."',";
				}

				$go = 0;
				foreach($columns as $column=>$value)
				{
					if($value)
					{
						$fields .= $column.',';
						$values .= "'".str_replace("\'","''",$value)."',";
						$go = true;
					}
				}
				$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

				if($go)
					DBQuery($sql);
			}
		}
		UpdateAttendanceDaily($student_id,date("Y-m-d", strtotime($date)),($_REQUEST['attendance_day'][$student_id]['COMMENT']?$_REQUEST['attendance_day'][$student_id]['COMMENT']:false));
		unset($_REQUEST['attendance_day'][$student_id]);
	}
	$current_RET = DBGet(DBQuery($current_Q),array(),array('STUDENT_ID','PERIOD_ID'));
	unset($_REQUEST['attendance']);
	unset($_SESSION['_REQUEST_vars']['attendance']);
	unset($_SESSION['_REQUEST_vars']['attendance_day']);
}
if(count($_REQUEST['attendance_day']))
{
	foreach($_REQUEST['attendance_day'] as $student_id=>$comment)
		UpdateAttendanceDaily($student_id,date("Y-m-d", strtotime($date)),$comment['COMMENT']);
	unset($_REQUEST['attendance_day']);
}

$codes_RET = DBGet(DBQuery("SELECT ID,SHORT_NAME,TITLE,STATE_CODE FROM attendance_codes WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND TABLE_NAME='$_REQUEST[table]'"));
$periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,SHORT_NAME,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND EXISTS (SELECT '' FROM COURSE_PERIODS WHERE PERIOD_ID=SCHOOL_PERIODS.PERIOD_ID AND ( position(',$_REQUEST[table],' IN DOES_ATTENDANCE)>0 OR position('Y' IN cp.DOES_ATTENDANCE)>0 )) ORDER BY SORT_ORDER"));

$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM attendance_code_categories WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
if(count($categories_RET))
{
	$tmp_PHP_SELF = PreparePHP_SELF($_REQUEST,array('table','codes'));

	$headerl = '<TABLE><TR>';
	if($_REQUEST['table']!=='0')
	{
		$tabcolor = '#DFDFDF';
		$textcolor = '#999999';
	}
	else
	{
		$tabcolor = Preferences('HIGHLIGHT');
		$textcolor = '#FFFFFF';
	}

	$headerl .= '<TD></TD><TD>'.DrawTab('Attendance',$tmp_PHP_SELF.'&amp;table=0',$tabcolor,$textcolor,'_circle',array('tabcolor'=>Preferences('HIGHLIGHT'),'textcolor'=>'#FFFFFF')).'</TD>';
	foreach($categories_RET as $category)
	{
		if($_REQUEST['table']!==$category['ID'])
		{
			$tabcolor = '#DFDFDF';
			$textcolor = '#999999';
		}
		else
		{
			$tabcolor = Preferences('HIGHLIGHT');
			$textcolor = '#FFFFFF';
		}

		$headerl .= '<TD width=10></TD><TD>'.DrawTab($category['TITLE'],$tmp_PHP_SELF.'&amp;table='.$category['ID'],$tabcolor,$textcolor,'_circle',array('tabcolor'=>Preferences('HIGHLIGHT'),'textcolor'=>'#FFFFFF')).'</TD>';
	}
	$headerl .= '</TR></TABLE>';
}

if(isset($_REQUEST['student_id']) && $_REQUEST['student_id']!='new')
{
	if(UserStudentID() != $_REQUEST['student_id'])
	{
		$_SESSION['student_id'] = $_REQUEST['student_id'];
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}

	$functions = array('ATTENDANCE_CODE'=>'_makeCodePulldown','ATTENDANCE_TEACHER_CODE'=>'_makeCode','ATTENDANCE_REASON'=>'_makeReasonInput','COMMENT'=>'_makeReason');
	$schedule_RET = DBGet(DBQuery("SELECT
										s.STUDENT_ID,c.TITLE AS COURSE,cp.PERIOD_ID,cp.COURSE_PERIOD_ID,p.TITLE AS PERIOD_TITLE,
										s.STUDENT_ID AS ATTENDANCE_CODE,s.STUDENT_ID AS ATTENDANCE_TEACHER_CODE,s.STUDENT_ID AS ATTENDANCE_REASON,s.STUDENT_ID AS COMMENT
									FROM
										SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS p,ATTENDANCE_CALENDAR ac
									WHERE
										s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID='".UserSchool()."' AND s.MARKING_PERIOD_ID IN (".$all_mp.")
										AND s.COURSE_ID=c.COURSE_ID
										AND s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND cp.PERIOD_ID=p.PERIOD_ID AND ( position(',$_REQUEST[table],' IN cp.DOES_ATTENDANCE)>0 OR position('Y' IN cp.DOES_ATTENDANCE)>0 )
										AND s.STUDENT_ID='".$_REQUEST['student_id']."' AND ('".date("Y-m-d", strtotime($date))."' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND '".date("Y-m-d", strtotime($date))."'>=s.START_DATE))
										AND position(substring('UMTWHFS' FROM cast(DAYOFWEEK(cast('".date("Y-m-d", strtotime($date))."' AS DATE)) AS SIGNED)+1 FOR 1) IN cp.DAYS)>0
										AND ac.CALENDAR_ID=cp.CALENDAR_ID AND ac.SCHOOL_DATE='".date("Y-m-d", strtotime($date))."' AND ac.MINUTES!='0'
									ORDER BY p.SORT_ORDER"),$functions);
	$columns = array('PERIOD_TITLE'=>_('Period'),'COURSE'=>_('Course'),'ATTENDANCE_CODE'=>_('Attendance Code'),'ATTENDANCE_TEACHER_CODE'=>_('Teacher\'s Entry'),'ATTENDANCE_REASON'=>_('Office Comment'),'COMMENT'=>_('Teacher Comment'));

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=student&student_id=$_REQUEST[student_id]&table=$_REQUEST[table] method=POST>";
	DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($date))),'_date',false,array('submit'=>true)),SubmitButton(_('Update')));

	$headerr = "<A HREF=Modules.php?modname=$_REQUEST[modname]&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]&table=$_REQUEST[table]>Student List</A>";
	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0><TR><TD bgcolor=#FFFFFF align=left>'.$headerl.'</TD><TD bgcolor=#FFFFFF align=right>'.$headerr.'</TD></TR></TABLE>';

	ListOutput($schedule_RET,$columns,_('Course'),_('Courses'));
	echo '</FORM>';
}
else
{
	if($_REQUEST['expanded_view']!='true')
		$extra['WHERE'] = $extra2['WHERE'] = " AND EXISTS (SELECT '' FROM $table ap,ATTENDANCE_CODES ac WHERE ap.SCHOOL_DATE='".date("Y-m-d", strtotime($date))."' AND ap.STUDENT_ID=ssm.STUDENT_ID AND ap.ATTENDANCE_CODE=ac.ID AND ac.SCHOOL_ID=ssm.SCHOOL_ID AND ac.SYEAR=ssm.SYEAR ".str_replace('TABLE_NAME','ac.TABLE_NAME',$extra_sql);
	else
		$extra['WHERE'] = " AND EXISTS (SELECT '' FROM $table ap,ATTENDANCE_CODES ac WHERE ap.SCHOOL_DATE='".date("Y-m-d", strtotime($date))."' AND ap.STUDENT_ID=ssm.STUDENT_ID AND ap.ATTENDANCE_CODE=ac.ID AND ac.SCHOOL_ID=ssm.SCHOOL_ID AND ac.SYEAR=ssm.SYEAR ".str_replace('TABLE_NAME','ac.TABLE_NAME',$extra_sql);

	if(count($_REQUEST['codes']))
	{
		$REQ_codes = $_REQUEST['codes'];
		foreach($REQ_codes as $key=>$value)
		{
			if(!$value)
				unset($REQ_codes[$key]);
			elseif($value=='A')
				$abs = true;
		}
	}
	else
		$abs = ($_REQUEST['table']=='0'); //true;
	if(count($REQ_codes) && !$abs)
	{
		$extra['WHERE'] .= "AND ac.ID IN (";
		foreach($REQ_codes as $code)
			$extra['WHERE'] .= "'".$code."',";
		if($_REQUEST['expanded_view']!='true')
			$extra2['WHERE'] = $extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
		else
			$extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
	}
	elseif($abs)
	{
		$RET = DBGet(DBQuery("SELECT ID FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL) AND TABLE_NAME='$_REQUEST[table]'"));
		if(count($RET))
		{
			$extra['WHERE'] .= "AND ac.ID IN (";
			foreach($RET as $code)
				$extra['WHERE'] .= "'".$code['ID']."',";

			if($_REQUEST['expanded_view']!='true')
				$extra2['WHERE'] = $extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
			else
				$extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
		}
	}
	$extra['WHERE'] .= ')';

	// EXPANDED VIEW BREAKS THIS QUERY.  PLUS, PHONE IS ALREADY AN OPTION IN EXPANDED VIEW
	if($_REQUEST['expanded_view']!='true' && $_REQUEST['_CENTRE_PDF']!='true')
	{
		$extra2['WHERE'] .= ')';
		$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID ';
		$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
		$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
		$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
		$extra2['group'] = array('STUDENT_ID','PERSON_ID');

		$contacts_RET = GetStuList($extra2);
		$extra['columns_before']['PHONE'] = '<IMG SRC=assets/down_phone_button.gif border=0 width=20>';
	}

	$columns = array();
	$extra['SELECT'] .= ',s.STUDENT_ID AS PHONE';
	$extra['functions']['PHONE'] = '_makePhone';
	if($_REQUEST['table']=='0')
	{
		$extra['SELECT'] .= ",(SELECT STATE_VALUE FROM attendance_day WHERE STUDENT_ID=ssm.STUDENT_ID AND SCHOOL_DATE='".date("Y-m-d", strtotime($date))."') AS STATE_VALUE";
		$extra['SELECT'] .= ",(SELECT COMMENT FROM attendance_day WHERE STUDENT_ID=ssm.STUDENT_ID AND SCHOOL_DATE='".date("Y-m-d", strtotime($date))."') AS DAILY_COMMENT";
		$extra['functions']['STATE_VALUE'] = '_makeStateValue';
		$extra['functions']['DAILY_COMMENT'] = '_makeStateValue';
		$extra['columns_after']['STATE_VALUE'] = 'Present';
		$extra['columns_after']['DAILY_COMMENT'] = 'Day Comment';
	}
	$extra['link']['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]&table=$_REQUEST[table]";
	$extra['link']['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');
	$extra['BackPrompt'] = false;
	$extra['Redirect'] = false;
	$extra['new'] = true;
	foreach($periods_RET as $period)
	{
		$extra['SELECT'] .= ",s.STUDENT_ID AS PERIOD_".$period['PERIOD_ID'];
		$extra['functions']['PERIOD_'.$period['PERIOD_ID']] = '_makeCodePulldown';
		$extra['columns_after']['PERIOD_'.$period['PERIOD_ID']] = $period['SHORT_NAME'];
	}

	if($REQ_codes)
	{
		foreach($REQ_codes as $code)
			$code_pulldowns .= _makeCodeSearch($code);
	}
	elseif($abs)
		$code_pulldowns = _makeCodeSearch('A');
	else
		$code_pulldowns = _makeCodeSearch();

	echo "<FORM action=".PreparePHP_SELF($_REQUEST,array('day_date','month_date','year_date','codes'))." method=POST>";
	DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($date))),'_date',false,array('submit'=>true)),SubmitButton(_('Update')));

	if(UserStudentID())
		$current_student_link = "<A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=student&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]&student_id=".UserStudentID()."&table=$_REQUEST[table]>"._('Current Student')."</A></TD><TD>";
	$headerr = '<TABLE><TR><TD>'.$current_student_link.button('add','',"# onclick='javascript:addHTML(\"".str_replace('"','\"',_makeCodeSearch())."\",\"code_pulldowns\"); return false;'").'</TD><TD><DIV id=code_pulldowns>'.$code_pulldowns.'</DIV></TD></TR></TABLE>';
	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0><TR><TD bgcolor=#FFFFFF align=left>'.$headerl.'</TD><TD bgcolor=#FFFFFF align=right>'.$headerr.'</TD></TR></TABLE>';

	$_REQUEST['search_modfunc'] = 'list';
	$extra['DEBUG']=true;
	Search('student_id',$extra);

	echo '<BR><CENTER>'.SubmitButton(_('Update')).'</CENTER>';
	echo "</FORM>";
}

function _makePhone($value,$column)
{	global $contacts_RET;

	if(count($contacts_RET[$value]))
	{
		foreach($contacts_RET[$value] as $person)
		{
			if($person[1]['FIRST_NAME'] || $person[1]['LAST_NAME'])
				$tipmessage .= '<B>'.$person[1]['STUDENT_RELATION'].': '.$person[1]['FIRST_NAME'].' '.$person[1]['LAST_NAME'].'</B><BR>';
			$tipmessage .= '<TABLE>';
			if($person[1]['PHONE'])
				$tipmessage .= '<TR><TD align=right><font color=gray size=1 face=Verdana,Arial,Helvetica>'._('Home Phone').'</font> </TD><TD><font size=1 face=Verdana,Arial,Helvetica>'.$person[1]['PHONE'].'</font></TD></TR>';
			foreach($person as $info)
			{
				if($info['TITLE'] || $info['VALUE'])
					$tipmessage .= '<TR><TD align=right><font color=gray size=1 face=Verdana,Arial,Helvetica>'.$info['TITLE'].'</font></TD><TD><font size=1 face=Verdana,Arial,Helvetica>'.$info['VALUE'].'</font></TD></TR>';
			}
			$tipmessage .= '</TABLE>';
		}
	}
	else
		$tipmessage = _('This student has no contact information.');
	return button('phone','','# onMouseOver=\'stm(["'._('Contact Information').'","'.str_replace("'",'&#39;',$tipmessage).'"],["white","#333366","","","",,"black","#e8e8ff","","","",,,,2,"#333366",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
}

function _makeCodePulldown($value,$title)
{	global $THIS_RET,$codes_RET,$current_RET,$current_schedule_RET,$current_schedule_Q;

	if(!$current_schedule_RET[$value])
	{
		$current_schedule_RET[$value] = DBGet(DBQuery(str_replace('__student_id__',$value,$current_schedule_Q)),array(),array('PERIOD_ID'));
		if(!$current_schedule_RET[$value])
			$current_schedule_RET[$value] = true;
	}
	if($THIS_RET['COURSE'])
	{
		$period_id = $THIS_RET['PERIOD_ID'];
		$code_title = 'TITLE';
	}
	else
	{
		$period_id = substr($title,7);
		$code_title = 'SHORT_NAME';
	}

	if($current_schedule_RET[$value][$period_id])
	{
		foreach($codes_RET as $code)
			if($current_schedule_RET[$value][$period_id][1]['HALF_DAY']!='Y' || $code['STATE_CODE']!='H') // prune half day codes for half day courses
				$options[$code['ID']] = $code[$code_title];

		$val = $current_RET[$value][$period_id][1]['ATTENDANCE_CODE'];

		return SelectInput($val,'attendance['.$value.']['.$period_id.'][ATTENDANCE_CODE]','',$options);
	}
	else
		return false;
}

function _makeCode($value,$title)
{	global $THIS_RET,$codes_RET,$current_RET;

	foreach($codes_RET as $code)
	{
		if($current_RET[$value][$THIS_RET['PERIOD_ID']][1]['ATTENDANCE_TEACHER_CODE']==$code['ID'])
			return $code['TITLE'];
	}
}

function _makeReasonInput($value,$title)
{	global $THIS_RET,$codes_RET,$current_RET;

	$val = $current_RET[$value][$THIS_RET['PERIOD_ID']][1]['ATTENDANCE_REASON'];

	return TextInput($val,'attendance['.$value.']['.$THIS_RET['PERIOD_ID'].'][ATTENDANCE_REASON]','',$options);
}

function _makeReason($value,$title)
{	global $THIS_RET,$current_RET;
	return $current_RET[$value][$THIS_RET['PERIOD_ID']][1]['COMMENT'];
}

function _makeCodeSearch($value='')
{	global $codes_RET,$code_search_selected;

	$return = '<SELECT name=codes[]><OPTION value="">'._('All').'</OPTION>';
	if($_REQUEST['table']=='0')
		$return .= '<OPTION value="A"'.(($value=='A')?' SELECTED':'').'>NP</OPTION>';
	if(count($codes_RET))
	{
		foreach($codes_RET as $code)
			$return .= "<OPTION value=$code[ID]".($value==$code['ID']?' SELECTED':'').">$code[SHORT_NAME]</OPTION>";
	}
	$return .= '</SELECT>';

	return $return;
}

function _makeStateValue($value,$name)
{	global $THIS_RET;

	if($name=='STATE_VALUE')
	{
		if($value=='0.0' || is_null($value))
			return 'None';
		elseif($value=='0.5')
			return 'Half-Day';
		else
			return 'Full-Day';
	}
	else
		return TextInput($value,'attendance_day['.$THIS_RET['STUDENT_ID'].'][COMMENT]');
}
?>
