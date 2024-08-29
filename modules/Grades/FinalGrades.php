<?php
require_once('modules/Grades/DeletePromptX.fnc.php');

if($_REQUEST['modfunc']=='delete')
{
	if(($dp=DeletePromptX('final grade')))
	{
		DBQuery("DELETE FROM STUDENT_REPORT_CARD_GRADES WHERE SYEAR='".UserSyear()."' AND STUDENT_ID='".$_REQUEST['student_id']."' AND COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."' AND MARKING_PERIOD_ID='".$_REQUEST['marking_period_id']."'");
		DBQuery("DELETE FROM STUDENT_REPORT_CARD_COMMENTS WHERE SYEAR='".UserSyear()."' AND STUDENT_ID='".$_REQUEST['student_id']."' AND COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."' AND MARKING_PERIOD_ID='".$_REQUEST['marking_period_id']."'");
		$_REQUEST['modfunc'] = 'save';
	}
	elseif($dp===false)
		$_REQUEST['modfunc'] = 'save';
}

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['mp_arr']) && count($_REQUEST['st_arr']))
	{
	$mp_list = '\''.implode('\',\'',$_REQUEST['mp_arr']).'\'';
	$last_mp = end($_REQUEST['mp_arr']);
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

	$extra['SELECT'] .= ",rpg.TITLE as GRADE_TITLE,sg1.GRADE_PERCENT,sg1.COMMENT as COMMENT_TITLE,sg1.STUDENT_ID,sg1.COURSE_PERIOD_ID,sg1.MARKING_PERIOD_ID,c.TITLE as COURSE_TITLE,rc_cp.TITLE AS TEACHER,sp.SORT_ORDER";
	if($_REQUEST['elements']['period_absences']=='Y')
		$extra['SELECT'] .= ",rc_cp.DOES_ATTENDANCE,
				(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
					WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.STATE_CODE='A' AND ap.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND ap.STUDENT_ID=ssm.STUDENT_ID) AS YTD_ABSENCES,
				(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
					WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.STATE_CODE='A' AND ap.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND sg1.MARKING_PERIOD_ID=ap.MARKING_PERIOD_ID AND ap.STUDENT_ID=ssm.STUDENT_ID) AS MP_ABSENCES";
	if($_REQUEST['elements']['comments']=='Y')
		$extra['SELECT'] .= ',sg1.MARKING_PERIOD_ID AS COMMENTS_RET';
	$extra['FROM'] .= ",STUDENT_REPORT_CARD_GRADES sg1 LEFT OUTER JOIN REPORT_CARD_GRADES rpg ON (rpg.ID=sg1.REPORT_CARD_GRADE_ID),
					COURSE_PERIODS rc_cp,COURSES c,SCHOOL_PERIODS sp";
	$extra['WHERE'] .= " AND sg1.MARKING_PERIOD_ID IN (".$mp_list.")
					AND rc_cp.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND c.COURSE_ID = rc_cp.COURSE_ID AND sg1.STUDENT_ID=ssm.STUDENT_ID AND sp.PERIOD_ID=rc_cp.PERIOD_ID";
	$extra['ORDER'] .= ",sp.SORT_ORDER,c.TITLE";
	$extra['functions']['TEACHER'] = '_makeTeacher';
	if($_REQUEST['elements']['comments']=='Y')
		$extra['functions']['COMMENTS_RET'] = '_makeComments';
	$extra['group']	= array('STUDENT_ID');


	$extra['group'][] = 'COURSE_PERIOD_ID';
	$extra['group'][] = 'MARKING_PERIOD_ID';

	$RET = GetStuList($extra);

	// GET THE COMMENTS
	if($_REQUEST['elements']['comments']=='Y')
		$comments_RET = DBGet(DBQuery("SELECT ID,TITLE,SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"),array(),array('ID'));

	if($_REQUEST['elements']['mp_tardies']=='Y' || $_REQUEST['elements']['ytd_tardies']=='Y')
	{
		// GET THE ATTENDANCE
		unset($extra);
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
		$extra['SELECT_ONLY'] .= "ap.SCHOOL_DATE,ap.COURSE_PERIOD_ID,ac.ID AS ATTENDANCE_CODE,ap.MARKING_PERIOD_ID,ssm.STUDENT_ID";
		$extra['FROM'] .= ",ATTENDANCE_CODES ac,ATTENDANCE_PERIOD ap";
		$extra['WHERE'] .= " AND ac.ID=ap.ATTENDANCE_CODE AND (ac.DEFAULT_CODE!='Y' OR ac.DEFAULT_CODE IS NULL) AND ac.SYEAR=ssm.SYEAR AND ap.STUDENT_ID=ssm.STUDENT_ID";
		$extra['group'][] = 'STUDENT_ID';
		$extra['group'][] = 'ATTENDANCE_CODE';
		$extra['group'][] = 'MARKING_PERIOD_ID';
		//Widgets('course'); // mab - these shouldn't be necessary because the student list is specified and the $_REQUEST values aren't passed from the select phase of search/select anyway
		//Widgets('gpa');
		//Widgets('class_rank');
		//Widgets('letter_grade');
		$attendance_RET = GetStuList($extra);
	}

	if($_REQUEST['elements']['mp_absences']=='Y' || $_REQUEST['elements']['ytd_absences']=='Y')
	{
		// GET THE DAILY ATTENDANCE
		unset($extra);
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
		$extra['SELECT_ONLY'] .= "ad.SCHOOL_DATE,ad.MARKING_PERIOD_ID,ad.STATE_VALUE,ssm.STUDENT_ID";
		$extra['FROM'] .= ",ATTENDANCE_DAY ad";
		$extra['WHERE'] .= " AND ad.STUDENT_ID=ssm.STUDENT_ID AND ad.SYEAR=ssm.SYEAR AND (ad.STATE_VALUE='0.0' OR ad.STATE_VALUE='.5') AND ad.SCHOOL_DATE<='".GetMP($last_mp,'END_DATE')."'";
		$extra['group'][] = 'STUDENT_ID';
		$extra['group'][] = 'MARKING_PERIOD_ID';
		//Widgets('course'); // mab - same as above
		//Widgets('gpa');
		//Widgets('class_rank');
		//Widgets('letter_grade');
		$attendance_day_RET = GetStuList($extra);
	}

	if(count($RET))
	{
		DrawHeader(ProgramTitle());

		$columns = array('FULL_NAME'=>_('Student'),'COURSE_TITLE'=>_('Course'));
		if($_REQUEST['elements']['teacher']=='Y')
			$columns += array('TEACHER'=>'Teacher');
		if($_REQUEST['elements']['period_absences']=='Y')
			$columns['ABSENCES'] = 'Abs<BR>YTD / MP';
		foreach($_REQUEST['mp_arr'] as $mp)
		{
			if($_REQUEST['elements']['percents']=='Y')
				$columns[$mp.'%'] = '%';
			$columns[$mp] = GetMP($mp);
		}
		if($_REQUEST['elements']['comments']=='Y')
			$columns['COMMENT'] = 'Comment';
		$i = 0;
		foreach($RET as $student_id=>$course_periods)
		{
			$course_period_id = key($course_periods);
			$grades_RET[$i+1]['FULL_NAME'] = $course_periods[$course_period_id][key($course_periods[$course_period_id])][1]['FULL_NAME'];

			$grades_RET[$i+1]['bgcolor'] = 'FFFFFF';
			foreach($course_periods as $course_period_id=>$mps)
			{
				$i++;
				$grades_RET[$i]['STUDENT_ID'] = $student_id;
				$grades_RET[$i]['COURSE_PERIOD_ID'] = $course_period_id;
				$grades_RET[$i]['MARKING_PERIOD_ID'] = key($mps);

				$grades_RET[$i]['COURSE_TITLE'] = $mps[key($mps)][1]['COURSE_TITLE'];
				$grades_RET[$i]['TEACHER'] = $mps[$last_mp][1]['TEACHER'];
				foreach($_REQUEST['mp_arr'] as $mp)
				{
					if($mps[$mp])
					{
						$grades_RET[$i][$mp] = $mps[$mp][1]['GRADE_TITLE'];
						if($_REQUEST['elements']['percents']=='Y' && $mps[$mp][1]['GRADE_PERCENT']>0)
							$grades_RET[$i][$mp.'%'] = $mps[$mp][1]['GRADE_PERCENT'].'%';
						$last_mp = $mp;
					}
				}
				if($_REQUEST['elements']['period_absences']=='Y')
					if(strpos($mps[$last_mp][1]['DOES_ATTENDANCE'],',0,')!==false)
						$grades_RET[$i]['ABSENCES'] = $mps[$last_mp][1]['YTD_ABSENCES'].' / '.$mps[$last_mp][1]['MP_ABSENCES'];
					else
						$grades_RET[$i]['ABSENCES'] = 'n/a';
				if($_REQUEST['elements']['comments']=='Y')
				{
					$sep = '';
					foreach($mps[$last_mp][1]['COMMENTS_RET'] as $comment)
					{
						$grades_RET[$i]['COMMENT'] .= $sep.$comments_RET[$comment['REPORT_CARD_COMMENT_ID']][1]['SORT_ORDER'];
						if($comment['COMMENT'])
							$grades_RET[$i]['COMMENT'] .= '('.($comment['COMMENT']!=' '?$comment['COMMENT']:'&middot;').')';
						$sep = ', ';
					}
					if($mps[$last_mp][1]['COMMENT_TITLE'])
						$grades_RET[$i]['COMMENT'] .= $sep.$mps[$last_mp][1]['COMMENT_TITLE'];
				}
			}
		}

		if(count($_REQUEST['mp_arr'])==1)
		{
			$link['remove']['link'] = PreparePHP_SELF($_REQUEST,array(),array('modfunc'=>'delete'));
			$link['remove']['variables'] = array('student_id'=>'STUDENT_ID','course_period_id'=>'COURSE_PERIOD_ID','marking_period_id'=>'MARKING_PERIOD_ID');
		}
		ListOutput($grades_RET,$columns,'','',$link);
	}
	else
		BackPrompt(_('No Students were found.'));
	}
	else
		BackPrompt(_('You must choose at least one student and one marking period'));
}

if(!$_REQUEST['modfunc'])
{
	DrawHeader(ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		$_CENTRE['allow_edit'] = true;

		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive] method=POST>";
		$extra['header_right'] = SubmitButton(_('Create Grade Lists for Selected Students'));

		$attendance_codes = DBGet(DBQuery("SELECT SHORT_NAME,ID FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL) AND TABLE_NAME='0'"));

		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD colspan=2><b>'.Localize('colon',_('Include on Grade List')).'</b></TD></TR>';

		$extra['extra_header_left'] .= '<TR><TD></TD><TD><TABLE>';
		$extra['extra_header_left'] .= '<TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[teacher] value=Y CHECKED>'._('Teacher').'</TD>';
		$extra['extra_header_left'] .= '<TD></TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[comments] value=Y CHECKED>'._('Comments').'</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[percents] value=Y>'._('Percents').'</TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[ytd_absences] value=Y CHECKED>'._('Year-to-date Daily Absences').'</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[mp_absences] value=Y'.(GetMP(UserMP(),'SORT_ORDER')!=1?' CHECKED':'').'>'._('Daily Absences this quarter').'</TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[ytd_tardies] value=Y>'.Localize('colon',_('Other Attendance Year-to-date')).' <SELECT name="ytd_tardies_code">';
		foreach($attendance_codes as $code)
			$extra['extra_header_left'] .= '<OPTION value='.$code['ID'].'>'.$code['SHORT_NAME'].'</OPTION>';
		$extra['extra_header_left'] .= '</SELECT></TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[mp_tardies] value=Y>'.Localize('colon',_('Other Attendance Year-to-date')).' <SELECT name="mp_tardies_code">';
		foreach($attendance_codes as $code)
			$extra['extra_header_left'] .= '<OPTION value='.$code['ID'].'>'.$code['SHORT_NAME'].'</OPTION>';
		$extra['extra_header_left'] .= '</SELECT></TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[period_absences] value=Y>'._('Period-by-period absences').'</TD>';
		$extra['extra_header_left'] .= '<TD></TD>';
		$extra['extra_header_left'] .= '</TR>';
		$extra['extra_header_left'] .= '</TABLE></TD></TR>';

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
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['new'] = true;
	$extra['options']['search'] = false;
	$extra['force_search'] = true;

	//Widgets('course');
	//Widgets('gpa');
	//Widgets('class_rank');
	//Widgets('letter_grade');

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton(_('Create Grade Lists for Selected Students')).'</CENTER>';
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