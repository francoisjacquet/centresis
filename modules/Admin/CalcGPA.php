<?php
$QI = DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER ");
$RET = DBGet($QI);

$SCALE_RET = DBGet(DBQuery("SELECT * from schools where ID = '".UserSchool()."'"));

DrawHeader(ProgramTitle());

$mps = GetAllMP('PRO',UserMP());
$mps = explode(',',str_replace("'",'',$mps));
$table = '<TABLE><TR><TD valign=top><TABLE>
	</TR>
		<TD align=right valign=top><font color=gray>Calculate GPA for</font></TD>
		<TD>';

foreach($mps as $mp)
{
	if($mp!='0')
		$table .= '<INPUT type=radio name=marking_period_id value='.$mp.($mp==UserMP()?' CHECKED':'').'>'.GetMP($mp).'<BR>';
}

$table .= '</TD>
	</TR>
	<TR>
		<TD colspan = 2 align=center><font color=gray>'.sprintf(_('GPA based on a scale of %d'),$SCALE_RET[1]['REPORTING_GP_SCALE']).'</TD>
	</TR>'.
//	<TR>
//		<TD align=right valign=top><font color=gray>Base class rank on</font></TD>
//		<TD><INPUT type=radio name=rank value=WEIGHTED_GPA CHECKED>Weighted GPA<BR><INPUT type=radio name=rank value=GPA>Unweighted GPA</TD>
'</TABLE></TD><TD width=350><small>'._('GPA calculation modifies existing records.').'<BR><BR>'._('Weighted and unweighted GPA is calculated by dividing the weighted and unweighted grade points configured for each letter grade (assigned in the Report Card Codes setup program) by the base grading scale specified in the school setup.').' </small></TD></TR></TABLE>';

$go = Prompt(_('GPA Calculation'),_('Calculate GPA and Class Rank'),$table);
if($go)
{
	DBQuery("SELECT calc_cum_gpa_mp('".$_REQUEST['marking_period_id']."')");
    $s_rank = DBGet(DBQuery("SELECT mp.syear, se.student_id AS STUDENT_ID,
    (SELECT count(*)+1 
       FROM student_mp_stats sgm3
       WHERE sgm3.cum_cr_weighted_factor > sgm.cum_cr_weighted_factor
         AND sgm3.marking_period_id = mp.marking_period_id 
         AND sgm3.student_id in (select distinct sgm2.student_id 
                FROM student_mp_stats sgm2, student_enrollment se2
                WHERE sgm2.student_id = se2.student_id 
                    AND sgm2.marking_period_id = mp.marking_period_id 
                    AND se2.grade_id = se.grade_id
                    AND se2.syear = se.syear)
    ) AS rank
    FROM student_enrollment se, student_mp_stats sgm, marking_periods mp
    
    WHERE 
    se.student_id = sgm.student_id
    AND sgm.marking_period_id = mp.marking_period_id
    AND mp.marking_period_id = '".$_REQUEST['marking_period_id']."'
    AND se.syear = mp.syear
    AND NOT sgm.cum_cr_weighted_factor is null
    ORDER BY grade_id, rank "));
	$cs_rank = $s_rank[1][rank];
	$stud_id = $s_rank[1][STUDENT_ID];

    $rank = DBGet(DBQuery("SELECT set_class_rank_mp('".$_REQUEST['marking_period_id']."')"));
	//print_r($rank);
	foreach($rank[1] as $key=>$val):
		$the_rank = $val;
	endforeach;
	DBQuery("UPDATE STUDENT_GPA_CALCULATED SET CLASS_RANK='$the_rank' WHERE STUDENT_ID='$stud_id' AND MARKING_PERIOD_ID='".$_REQUEST['marking_period_id']."'");
	unset($_REQUEST['delete_ok']);
	DrawHeader('<IMG SRC=assets/check.gif>'.sprintf(_('GPA and class rank for %s has been calculated.'),GetMP($_REQUEST['marking_period_id'])));
	Prompt(_('GPA Calculation'),_('Calculate GPA and Class Rank'),$table);
}
?>
