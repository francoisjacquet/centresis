<?php
include 'modules/Grades/config.inc.php';

//sets whether editing final grading percentages is allowed.
$edit_fgp = 'Y';

if($_REQUEST['values'])
{
	DBQuery("DELETE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".User('STAFF_ID')."' AND PROGRAM='Gradebook'");
	foreach($_REQUEST['values'] as $title=>$value)
		DBQuery("INSERT INTO PROGRAM_USER_CONFIG (USER_ID,PROGRAM,TITLE,VALUE) values('".User('STAFF_ID')."','Gradebook','$title','".str_replace("\'","''",str_replace('%','',$value))."')");
}

$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".User('STAFF_ID')."' AND PROGRAM='Gradebook'" ),array(),array('TITLE'));
if(count($config_RET))
{
	foreach($config_RET as $title=>$value)
		$programconfig[$title] = $value[1]['VALUE'];
}

$grades = DBGet(DBQuery("SELECT cp.TITLE AS CP_TITLE,c.TITLE AS COURSE_TITLE,cp.COURSE_PERIOD_ID,rcg.TITLE,rcg.ID FROM REPORT_CARD_GRADES rcg,COURSE_PERIODS cp,COURSES c,SCHOOL_PERIODS sp WHERE cp.COURSE_ID=c.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.SCHOOL_ID=rcg.SCHOOL_ID AND cp.SYEAR=rcg.SYEAR AND cp.SYEAR='".UserSyear()."' AND rcg.GRADE_SCALE_ID=cp.GRADE_SCALE_ID AND cp.GRADE_SCALE_ID IS NOT NULL AND DOES_BREAKOFF='Y' ORDER BY sp.SORT_ORDER,rcg.BREAK_OFF IS NOT NULL DESC,rcg.BREAK_OFF DESC,rcg.SORT_ORDER DESC"),array(),array('COURSE_PERIOD_ID'));

echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeader('Gradebook - '.ProgramTitle());
DrawHeader('','<INPUT type=submit value="'._('Save').'">');
echo '<BR>';
PopTable('header',_('Configuration'));
echo '<TABLE>';

echo '<TR><TD colspan=3>';
//echo '<TABLE><TR><TD><b>Assignments</b></TD></TR></TABLE>';
echo '</TD></TR>';
echo '<TR><TD width=1></TD>';
echo '<TD><fieldset>';
echo '<legend><b>'._('Assignments').'</b></legend>';
echo '<TABLE>';
if(count($grades))
{
	//if(!$programconfig['ROUNDING'])
	//	$programconfig['ROUNDING'] = 'NORMAL';
	echo '<TR><TD colspan=3>'.DrawRoundedRect('<TABLE><TR><TD colspan=8><font color=white size=-2><B>'._('Score Rounding').'</B></font></TD></TR><TR><TD align=right><INPUT type=radio name=values[ROUNDING] value=UP'.(($programconfig['ROUNDING']=='UP')?' CHECKED':'').'></TD><TD><font color=white size=-1>'._('Up').'</font></TD><TD align=right><INPUT type=radio name=values[ROUNDING] value=DOWN'.(($programconfig['ROUNDING']=='DOWN')?' CHECKED':'').'></TD><TD><font color=white size=-1>'._('Down').'</font></TD><TD align=right><INPUT type=radio name=values[ROUNDING] value=NORMAL'.(($programconfig['ROUNDING']=='NORMAL')?' CHECKED':'').'></TD><TD><font color=white size=-1>'._('Normal').'</font></TD><TD align=right><INPUT type=radio name=values[ROUNDING] value=""'.(($programconfig['ROUNDING']=='')?' CHECKED':'').'><font color=white size=-1>'._('None').'</font></TD></TR></TABLE>','',Preferences('HEADER')).'</TD></TR>';
}
if(!$programconfig['ASSIGNMENT_SORTING'])
	$programconfig['ASSIGNMENT_SORTING'] = 'ASSIGNMENT_ID';
echo '<TR><TD colspan=3>'.DrawRoundedRect('<TABLE><TR><TD colspan=6><font color=white size=-2><B>'._('Assignment Sorting').'</B></font></TD></TR><TR><TD align=right><INPUT type=radio name=values[ASSIGNMENT_SORTING] value=ASSIGNMENT_ID'.(($programconfig['ASSIGNMENT_SORTING']=='ASSIGNMENT_ID')?' CHECKED':'').'></TD><TD><font color=white size=-1>'._('Newest First').'</font></TD><TD align=right><INPUT type=radio name=values[ASSIGNMENT_SORTING] value=DUE_DATE'.(($programconfig['ASSIGNMENT_SORTING']=='DUE_DATE')?' CHECKED':'').'></TD><TD><font color=white size=-1>'._('Due Date').'</font></TD><TD align=right><INPUT type=radio name=values[ASSIGNMENT_SORTING] value=ASSIGNED_DATE'.(($programconfig['ASSIGNMENT_SORTING']=='ASSIGNED_DATE')?' CHECKED':'').'></TD><TD><font color=white size=-1>'._('Assigned Date').'</font></TD></TR></TABLE>','',Preferences('HEADER')).'</TD></TR>';

echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=checkbox name=values[WEIGHT] value=Y'.(($programconfig['WEIGHT']=='Y')?' CHECKED':'').'></TD><TD align=left>'._('Weight Grades').'</TD></TR>';
echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=checkbox name=values[DEFAULT_ASSIGNED] value=Y'.(($programconfig['DEFAULT_ASSIGNED']=='Y')?' CHECKED':'').'></TD><TD align=left>'._('Assigned Date defaults to today').'</TD></TR>';
echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=checkbox name=values[DEFAULT_DUE] value=Y'.(($programconfig['DEFAULT_DUE']=='Y')?' CHECKED':'').'></TD><TD align=left>'._('Due Date defaults to today').'</TD></TR>';
echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=checkbox name=values[LETTER_GRADE_ALL] value=Y'.(($programconfig['LETTER_GRADE_ALL']=='Y')?' CHECKED':'').'></TD><TD align=left>'._('Hide letter grades for all gradebook assignments').'</TD></TR>';
echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=text name=values[LETTER_GRADE_MIN] value="'.$programconfig['LETTER_GRADE_MIN'].'" size=3 maxlength=3></TD><TD align=left>'._('Minimum assignment points for letter grade').'</TD></TR>';
echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=text name=values[ANOMALOUS_MAX] value="'.($programconfig['ANOMALOUS_MAX']!=''?$programconfig['ANOMALOUS_MAX']:'100').'" size=3 maxlength=3></TD><TD align=left>% '._('Allowed maximum percent in Anomalous grades').'</TD></TR>';
echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=text name=values[LATENCY] value="'.round($programconfig['LATENCY']).'" size=3 maxlength=3></TD><TD align=left>'._('Days until ungraded assignment grade appears in Parent/Student gradebook views').'</TD></TR>';
echo '</TABLE>';
echo '</fieldset></TD>';
echo '<TD width=1></TD></TR>';

echo '<TR><TD colspan=3>';
//echo '<TABLE><TR><TD><b>Eligibility</b></TD></TR></TABLE>';
echo '</TD></TR>';
echo '<TR><TD width=1></TD>';
echo '<TD><fieldset>';
echo '<legend><b>'._('Eligibility').'</b></legend>';
echo '<TABLE>';
echo '<TR><TD valign=top width=30></TD><TD align=right><INPUT type=checkbox name=values[ELIGIBILITY_CUMULITIVE] value=Y'.(($programconfig['ELIGIBILITY_CUMULITIVE']=='Y')?' CHECKED':'').'></TD><TD align=left>'._('Calculate Eligibility using Cumulative Semester Grades').'</TD></TR>';
echo '</TABLE>';
echo '</fieldset></TD>';
echo '<TD width=1></TD></TR>';

echo '<TR><TD colspan=3>';
//echo '<TABLE><TR><TD><b>Final Grades</b></TD></TR></TABLE>';
echo '</TD></TR>';
echo '<TR><TD width=1></TD>';
echo '<TD><fieldset>';
echo '<legend><b>'._('Final Grades').'</b></legend>';
echo '<TABLE>';

echo '<TR><TD colspan=3>'.DrawRoundedRect('<TABLE><TR><TD colspan=4><font color=white size=-2><B>'._('Input Format').'</B></font></TD></TR><TR><TD align=right><INPUT type=radio name=values[ONELINE] value=""'.(($programconfig['ONELINE']=='')?' CHECKED':'').'></TD><TD><font color=white size=-1>'._('Letter').'<BR>'._('Percent').'</font></TD><TD align=right><INPUT type=radio name=values[ONELINE] value=Y'.(($programconfig['ONELINE']=='Y')?' CHECKED':'').'></TD><TD><font color=white size=-1><NOBR> '._('Letter Percent').'</NOBR></font></TD></TR></TABLE>','',Preferences('HEADER')).'</TD></TR>';

$comment_codes_RET = DBGet(DBQuery("SELECT rccs.ID,rccs.TITLE,rccc.TITLE AS CODE_TITLE FROM REPORT_CARD_COMMENT_CODE_SCALES rccs,REPORT_CARD_COMMENT_CODES rccc WHERE rccs.SCHOOL_ID='".UserSchool()."' AND rccc.SCALE_ID=rccs.ID ORDER BY rccc.SORT_ORDER,rccs.SORT_ORDER,rccs.ID,rccc.ID"),array(),array('ID'));
if($comment_codes_RET)
{
    foreach($comment_codes_RET as $id=>$comments)
    {
    echo '<TR><TD valign=top width=30></TD><TD align=right><SELECT name=values[COMMENT_'.$id.']><OPTION value="">'._('N/A');
    foreach($comments as $key=>$val)
        echo '<OPTION value="'.$val['CODE_TITLE'].'"'.($val['CODE_TITLE']==$programconfig['COMMENT_'.$id]?' selected':'').'>'.$val['CODE_TITLE'];
    echo '</SELECT></TD><TD align=left>'.sprintf(_('Default <B>%s</B> comment code'),$comments[1]['TITLE']).'</TD></TR>';
    }
}
echo '</TABLE>';
echo '</fieldset></TD>';
echo '<TD width=1></TD></TR>';

echo '<TR><TD colspan=3>';
//echo '<TABLE><TR><TD><b>Score Breakoff Points</b></TD></TR></TABLE>';
echo '</TD></TR>';
echo '<TR><TD width=1></TD>';
/*
foreach($grades as $course_period_id=>$cp_grades)
{
	for($i=1;$i<=count($cp_grades);$i++)
		$grades[$course_period_id][$i] = $grades[$course_period_id][$i]['TITLE'];
}
*/

//$grades = array('A+','A','A-','B+','B','B-','C+','C','C-','D+','D','D-','F');
if(count($grades))
{
	echo '<TD><fieldset>';
	echo '<legend><b>'._('Score Breakoff Points').'</b></legend>';
	echo '<TABLE><TR><TD>';
	foreach($grades as $course_period_id=>$cp_grades)
	{
		$table = '<TABLE>';
		$table .= '<TR><TD rowspan=2 align=right width=100><font color=white size=-1>'.$cp_grades[1]['COURSE_TITLE'].' - '.substr($cp_grades[1]['CP_TITLE'],0,strrpos(str_replace(' - ',' ^ ',$cp_grades[1]['CP_TITLE']),'^')).'</font></TD>';
		foreach($cp_grades as $grade)
			$table .= '<TD><B><font color=white>'.$grade['TITLE'].'</font></B></TD>';
		$table .= '</TR>';
		$table .= '<TR>';
		foreach($cp_grades as $grade)
			$table .= '<TD><INPUT type=text name=values['.$course_period_id.'-'.$grade['ID'].'] value="'.$programconfig[$course_period_id.'-'.$grade['ID']].'" size=3 maxlength=5></TD>';
		$table .= '</TR>';
		$table .= '</TABLE>';
		echo DrawRoundedRect($table,'',Preferences('HEADER'));
		echo '</TD></TR><TR><TD>';
	}
	echo '</TD></TR></TABLE>';
	echo '</fieldset></TD>';
}
echo '<TD width=1></TD></TR>';

echo '<TR><TD colspan=3>';
//echo '<TABLE><TR><TD><b>Final Grading Percentages</b></TD></TR></TABLE>';
echo '</TD></TR>';
echo '<TR><TD width=1></TD>';
$year = DBGet(DBQuery("SELECT TITLE,MARKING_PERIOD_ID,DOES_GRADES,DOES_EXAM FROM SCHOOL_MARKING_PERIODS WHERE MP='FY' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
$semesters = DBGet(DBQuery("SELECT TITLE,MARKING_PERIOD_ID,DOES_GRADES,DOES_EXAM FROM SCHOOL_MARKING_PERIODS WHERE MP='SEM' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
$quarters = DBGet(DBQuery("SELECT TITLE,MARKING_PERIOD_ID,PARENT_ID,DOES_GRADES,DOES_EXAM FROM SCHOOL_MARKING_PERIODS WHERE MP='QTR' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('PARENT_ID'));

echo '<TD><fieldset>';
echo '<legend><b>'._('Final Grading Percentages').'</b></legend>';
echo '<TABLE>';
foreach($semesters as $sem)
	if($sem['DOES_GRADES']=='Y')
	{
		$table = '<TABLE>';
		$table .= '<TR><TD rowspan=2 valign=middle><font color=white size=-1>'.$sem['TITLE'].'</font></TD>';
		foreach($quarters[$sem['MARKING_PERIOD_ID']] as $qtr)
			$table .= '<TD><font color=white>'.$qtr['TITLE'].'</font></TD>';
		if($sem['DOES_EXAM']=='Y')
			$table .= '<TD><font color=white>'.$sem['TITLE'].' Exam</font></TD>';
		$table .= '</TR><TR>';
		$total = 0;
		foreach($quarters[$sem['MARKING_PERIOD_ID']] as $qtr)
		{
			if($edit_fgp)
                $table .= '<TD><INPUT type=text name=values[SEM-'.$qtr['MARKING_PERIOD_ID'].'] value="'.$programconfig['SEM-'.$qtr['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
            else
                $table .= '<TD><font color=gray>'.$programconfig['SEM-'.$qtr['MARKING_PERIOD_ID']].'<INPUT type=hidden name=values[SEM-'.$qtr['MARKING_PERIOD_ID'].'] value="'.$programconfig['SEM-'.$qtr['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
			$total += $programconfig['SEM-'.$qtr['MARKING_PERIOD_ID']];
		}
		if($sem['DOES_EXAM']=='Y')
		{
			if($edit_fgp)
                $table .= '<TD><INPUT type=text name=values[SEM-E'.$sem['MARKING_PERIOD_ID'].'] value="'.$programconfig['SEM-E'.$sem['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
			else
                $table .= '<TD><font color=gray>'.$programconfig['SEM-E'.$sem['MARKING_PERIOD_ID']].'<INPUT type=hidden name=values[SEM-E'.$sem['MARKING_PERIOD_ID'].'] value="'.$programconfig['SEM-E'.$sem['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
            $total += $programconfig['SEM-E'.$sem['MARKING_PERIOD_ID']];
		}
		if($total!=100)
			$table .= '<TD><FONT color=red>'._('Total is not 100%!').'</FONT></TD>';
		$table .= '</TR>';
		$table .= '</TABLE>';
		echo '<TR><TD colspan=3>'.DrawRoundedRect($table,'',Preferences('HEADER')).'</TD></TR>';
	}

if($year[1]['DOES_GRADES']=='Y')
{
	$table = '<TABLE>';
	$table .= '<TR><TD rowspan=2 valign=middle><font color=white size=-1>'.$year[1]['TITLE'].'</font></TD>';
	foreach($semesters as $sem)
	{
		foreach($quarters[$sem['MARKING_PERIOD_ID']] as $qtr)
			$table .= '<TD><font color=white>'.$qtr['TITLE'].'</font></TD>';
		if($sem['DOES_GRADES']=='Y')
			$table .= '<TD><font color=white>'.$sem['TITLE'].'</font></TD>';
		if($sem['DOES_EXAM']=='Y')
			$table .= '<TD><font color=white>'.sprintf(_('%s Exam'),$sem['TITLE']).'</font></TD>';
	}
	if($year[1]['DOES_EXAM']=='Y')
		$table .= '<TD><font color=white>'.sprintf(_('%s Exam'),$year[1]['TITLE']).'</font></TD>';
	$table .= '</TR><TR>';
	$total = 0;
	foreach($semesters as $sem)
	{
		foreach($quarters[$sem['MARKING_PERIOD_ID']] as $qtr)
		{
			if($edit_fgp)
                $table .= '<TD><INPUT type=text name=values[FY-'.$qtr['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-'.$qtr['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
			else
                $table .= '<TD><font color=gray>'.$programconfig['FY-'.$qtr['MARKING_PERIOD_ID']].'<INPUT type=hidden name=values[FY-'.$qtr['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-'.$qtr['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
            $total += $programconfig['FY-'.$qtr['MARKING_PERIOD_ID']];
		}
		if($sem['DOES_GRADES']=='Y')
		{
			if($edit_fgp)
                $table .= '<TD><INPUT type=text name=values[FY-'.$sem['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-'.$sem['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
			else
                $table .= '<TD><font color=gray>'.$programconfig['FY-'.$sem['MARKING_PERIOD_ID']].'<INPUT type=hidden name=values[FY-'.$sem['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-'.$sem['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
            $total += $programconfig['FY-'.$sem['MARKING_PERIOD_ID']];
		}
		if($sem['DOES_EXAM']=='Y')
		{
			if($edit_fgp)
                $table .= '<TD><INPUT type=text name=values[FY-E'.$sem['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-E'.$sem['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
			else
                $table .= '<TD><font color=gray>'.$programconfig['FY-E'.$sem['MARKING_PERIOD_ID']].'<INPUT type=hidden name=values[FY-E'.$sem['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-E'.$sem['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
            $total += $programconfig['FY-E'.$sem['MARKING_PERIOD_ID']];
		}
	}
	if($year[1]['DOES_EXAM']=='Y')
	{
		if($edit_fgp)
            $table .= '<TD><INPUT type=text name=values[FY-E'.$year[1]['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-E'.$year[1]['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
		else
            $table .= '<TD><font color=gray>'.$programconfig['GY-E'.$year['MARKING_PERIOD_ID']].'<INPUT type=hidden name=values[FY-E'.$year[1]['MARKING_PERIOD_ID'].'] value="'.$programconfig['FY-E'.$year[1]['MARKING_PERIOD_ID']].'" size=3 maxlength=6></TD>';
        $total += $programconfig['FY-E'.$year[1]['MARKING_PERIOD_ID']];
	}
	if($total!=100)
		$table .= '<TD><FONT color=red>'._('Total is not 100%!').'</FONT></TD>';
	$table .= '</TR>';
	$table .= '</TABLE>';
	echo '<TR><TD colspan=3>'.DrawRoundedRect($table,'',Preferences('HEADER')).'</TD></TR>';
    echo "<INPUT type=hidden name=sqlsyear value='".UserSchool()."'>";
}
echo '</TABLE>';
echo '</fieldset></TD>';
echo '<TD width=1></TD></TR>';

echo '</TABLE>';
PopTable('footer');
echo '<CENTER><INPUT type=submit value="'._('Save').'"></CENTER>';
echo '</FORM>';
?>
