<?php
if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['mp_arr']))
	{
	$mp_list = '\''.implode('\',\'',$_REQUEST['mp_arr']).'\'';
	$last_mp = end($_REQUEST['mp_arr']);

	$extra['DATE'] = DBDate();

	$q = "SELECT s.STUDENT_ID as Permanent_Number, '10101010' as School_Number, DATE_FORMAT(s.custom_200000004, '%Y-%c-%d') as Birthdate, '".UserSchool()."' as School_Year, c.COURSE_ID as Course_Code, '' as Course_Type, c.TITLE as Course_Title, sg1.GRADE_LETTER as Mark, sg1.CREDIT_ATTEMPTED as Credit_Attempted, sg1.CREDIT_EARNED as Credit_Earned, ( case when sg1.weighted_gp > 0 THEN 'Y' ELSE 'N' END ) as Course_Passed_Indicator, mp.title as Term, mp.start_date as Term_Start_Date, mp.end_date as Term_End_Date FROM students s JOIN student_enrollment ssm ON (ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ('".date("Y-m-d", strtotime($extra['DATE']))."'>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR '".date("Y-m-d", strtotime($extra['DATE']))."'<=ssm.END_DATE)) AND ssm.SCHOOL_ID='".UserSchool()."'), student_report_card_grades sg1 LEFT OUTER JOIN report_card_grades rpg ON (rpg.ID=sg1.REPORT_CARD_GRADE_ID), course_periods rc_cp, courses c, school_gradelevels sg, student_enrollment e, schools sc, school_marking_periods mp, school_periods sp WHERE TRUE AND sg1.MARKING_PERIOD_ID IN (".$mp_list.") AND rc_cp.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND c.COURSE_ID = rc_cp.COURSE_ID AND sg1.STUDENT_ID=ssm.STUDENT_ID AND e.STUDENT_ID=s.STUDENT_ID and e.SCHOOL_ID=sc.ID AND e.GRADE_ID=sg.ID AND sp.PERIOD_ID=rc_cp.PERIOD_ID AND sg1.MARKING_PERIOD_ID=mp.marking_period_id GROUP BY s.STUDENT_ID ORDER BY s.STUDENT_ID,sp.SORT_ORDER,c.TITLE";

	$result = DBQuery($q); //echo $q.'<br>';

	header('Content-Description: File Transfer');
	header("Content-type: text/csv");
	header('Content-Disposition: attachment; filename="exportgrades.csv"');
	header("Expires: 0");
	header("Pragma: public");
	header("Cache-Control: public");

	$fp = fopen('php://output', 'w');
	if ($fp && $result) {

		$n=0;
	
		// fetch a row and write the column names out to the file
		$row = mysql_fetch_assoc($result); //echo count($row);
		$line = "";
		$comma = "";
		foreach($row as $name => $value) {
			$line .= $comma . '"' . str_replace('"', '""', ucwords(str_replace("_", " ", $name))) . '"';
			$comma = ",";
		}
		$line .= "\n";
		fputcsv($fp, $line);
		
		// remove the result pointer back to the start
		mysql_data_seek($result, 0);
		
		$next_line = "";
		while ($row = mysql_fetch_array($result, MYSQLI_NUM)) {
			$total = count($row);
			$comma2 = "";	
			while($total > $n) {
				//echo $row[$n].', <br>';
				$next_line .= $comma2 . '"' . str_replace('"', '""', $row[$n]) . '"';
				$comma2 = ",";			
			$n++;
			}
			$next_line .= "\n";
			fputcsv($fp, $next_line);
		$n=0;
		}
		
		echo $line.$next_line;

		die;
	}
	else 
		BackPrompt(_('No Results Found'));
}
else
	BackPrompt(_('You must choose at least one marking period'));
}

if(!$_REQUEST['modfunc'])
{
	$_REQUEST['search_modfunc'] = 'list';
	DrawHeader(ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		$_CENTRE['allow_edit'] = true;

		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&page=&LO_sort=&LO_direction=&LO_search=&LO_save=1&_CENTRE_PDF=true&include_inactive=$_REQUEST[include_inactive] method=POST>";
		$extra['header_right'] = SubmitButton(_('Export Grade Lists'));

		$attendance_codes = DBGet(DBQuery("SELECT SHORT_NAME,ID FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL) AND TABLE_NAME='0'"));

		$extra['extra_header_left'] = '<TABLE>';

		$mps_RET = DBGet(DBQuery("SELECT PARENT_ID,MARKING_PERIOD_ID,SHORT_NAME FROM SCHOOL_MARKING_PERIODS WHERE MP='QTR' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('PARENT_ID'));
		$extra['extra_header_left'] .= '<TR><TD align=right>'._('Marking Periods').'</TD><TD><TABLE><TR><TD><TABLE>';
		foreach($mps_RET as $sem=>$quarters)
		{
			$extra['extra_header_left'] .= '<TR>';
			foreach($quarters as $qtr)
			{
				$pro = GetChildrenMP('PRO',$qtr['MARKING_PERIOD_ID']);
				if($pro)
				{
					$pros = explode(',',str_replace("'",'',$pro));
					foreach($pros as $pro)
						if(GetMP($pro,'DOES_GRADES')=='Y')
							$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$pro.'>'.GetMP($pro,'SHORT_NAME').'</TD>';
				}
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$qtr['MARKING_PERIOD_ID'].'>'.$qtr['SHORT_NAME'].'</TD>';
			}
			if(GetMP($sem,'DOES_EXAM')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$sem.'>'.sprintf(_('%s Exam'),GetMP($sem,'SHORT_NAME')).'</TD>';
			if(GetMP($sem,'DOES_GRADES')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$sem.'>'.GetMP($sem,'SHORT_NAME').'</TD>';
			$extra['extra_header_left'] .= '</TR>';
		}
		$extra['extra_header_left'] .= '</TABLE></TD>';
		if($sem)
		{
			$fy = GetParentMP('FY',$sem);
			$extra['extra_header_left'] .= '<TD><TABLE><TR>';
			if(GetMP($fy,'DOES_EXAM')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$fy.'>'.sprintf(_('%s Exam'),GetMP($fy,'SHORT_NAME')).'</TD>';
			if(GetMP($fy,'DOES_GRADES')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$fy.'>'.GetMP($fy,'SHORT_NAME').'</TD>';
			$extra['extra_header_left'] .= '</TR></TABLE></TD>';
		}
		$extra['extra_header_left'] .= '</TD></TR></TABLE></TR>';
		$extra['extra_header_left'] .= '</TABLE>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['new'] = true;
	$extra['options']['search'] = false;
	$extra['force_search'] = false;

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton(_('Export Grade Lists')).'</CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function _makeTeacher($teacher,$column)
{
	return substr($teacher,strrpos(str_replace(' - ',' ^ ',$teacher),'^')+2);
}

function _makeComments($value,$column)
{	global $THIS_RET;

	return DBGet(DBQuery('SELECT COURSE_PERIOD_ID,REPORT_CARD_COMMENT_ID,COMMENT,(SELECT SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE REPORT_CARD_COMMENT_ID=ID) AS SORT_ORDER FROM STUDENT_REPORT_CARD_COMMENTS WHERE STUDENT_ID=\''.$THIS_RET['STUDENT_ID'].'\' AND COURSE_PERIOD_ID=\''.$THIS_RET['COURSE_PERIOD_ID'].'\' AND MARKING_PERIOD_ID=\''.$value.'\' ORDER BY SORT_ORDER'));
}
?>