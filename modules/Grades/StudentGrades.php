<?php
include 'modules/Grades/config.inc.php';

require_once('ProgramFunctions/_makeLetterGrade.fnc.php');
$_CENTRE['allow_edit'] = false;

DrawHeader(ProgramTitle());
Search('student_id');

if(UserStudentID() && !$_REQUEST['modfunc'])
{
$student_RET = DBGet(DBQuery("SELECT first_name||' '||last_name AS full_name FROM students WHERE student_id='".UserStudentID()."'"));
$courses_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") AND ('".DBDate()."'>=s.START_DATE AND (s.END_DATE IS NULL OR '".DBDate()."'<=s.END_DATE)) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)"),array(),array('COURSE_PERIOD_ID'));
//echo '<pre>'; var_dump($courses_RET); echo '</pre>';
if($_REQUEST['id'] && $_REQUEST['id']!='all' && !$courses_RET[$_REQUEST['id']])
	unset($_REQUEST['id']);

if(!$_REQUEST['id'])
{
    DrawHeader('','','<B>'.$student_RET[1]['FULL_NAME'].'</B>');
	DrawHeader('<B>'._('Totals').'</B>',"<A HREF=Modules.php?modname=$_REQUEST[modname]&id=all".($do_stats?"&do_stats=$_REQUEST[do_stats]":'').">"._('Expand All')."</A>");
	if($do_stats)
		DrawHeader('',CheckBoxOnclick('do_stats')._('Include Anonymous Statistics'));

	$LO_columns = array('TITLE'=>_('Course Title'),'TEACHER'=>_('Teacher'),'PERCENT'=>_('Percent'),'GRADE'=>_('Letter'),'UNGRADED'=>_('Ungraded'));
	if($do_stats && $_REQUEST['do_stats'])
		$LO_columns += array('BAR1'=>_('Grade Range'),'BAR2'=>_('Class Rank'));

	if(count($courses_RET))
	{
		$LO_ret = array(0=>array());

		foreach($courses_RET as $course_period_id=>$course)
		{
			$course = $course[1];
			$staff_id = $course['STAFF_ID'];
			$course_id = $course['COURSE_ID'];
			$course_title = $course['TITLE'];
			//echo $staff_id.'+'.$course_id.'+'.$course_period_id.'+'.$course_title.'|';
            $assignments_RET = DBGet(DBQuery("SELECT ASSIGNMENT_ID,TITLE,POINTS FROM GRADEBOOK_ASSIGNMENTS WHERE STAFF_ID='".$staff_id."' AND (COURSE_ID='".$course_id."' OR COURSE_PERIOD_ID='$course_period_id') AND MARKING_PERIOD_ID='".UserMP()."' ORDER BY DUE_DATE DESC,ASSIGNMENT_ID"));
			//echo '<pre>'; var_dump($assignments_RET); echo '</pre>';

			if(!$programconfig[$staff_id])
			{
                $config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".$staff_id."' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
				if(count($config_RET))
					foreach($config_RET as $title=>$value)
						$programconfig[$staff_id][$title] = $value[1]['VALUE'];
				else
					$programconfig[$staff_id] = true;
			}

            $sql = "SELECT s.STUDENT_ID,gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT,sum(".db_case(array('gg.POINTS',"''","1","0")).") AS UNGRADED";
            $sql .= " FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."'";
            if($_REQUEST['include_inactive']=='Y')
                $sql .= " AND ss.START_DATE=(SELECT START_DATE FROM SCHEDULE WHERE STUDENT_ID=s.STUDENT_ID AND SYEAR=ss.SYEAR AND COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID ORDER BY START_DATE DESC LIMIT 1)";
            else
                $sql .= " AND ss.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") AND (CURRENT_DATE>=ss.START_DATE AND (CURRENT_DATE<=ss.END_DATE OR ss.END_DATE IS NULL))";

            $sql .= ") JOIN COURSE_PERIODS cp ON (cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."')
                JOIN STUDENT_ENROLLMENT ssm ON (ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=ss.SYEAR AND ssm.SCHOOL_ID='".UserSchool()."'";

            if($_REQUEST['include_inactive']=='Y')
                $sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR ORDER BY START_DATE DESC LIMIT 1)";
            else
                $sql .= " AND (CURRENT_DATE>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR CURRENT_DATE<=ssm.END_DATE))";
            $sql .= ") JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID OR ga.COURSE_ID=cp.COURSE_ID AND ga.STAFF_ID=cp.TEACHER_ID) AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID),GRADEBOOK_ASSIGNMENT_TYPES gt";
            $sql .= " WHERE gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID=cp.COURSE_ID AND (gg.POINTS IS NOT NULL OR (ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR CURRENT_DATE>(SELECT END_DATE FROM SCHOOL_MARKING_PERIODS WHERE MARKING_PERIOD_ID=ga.MARKING_PERIOD_ID))";
            $sql .= " AND (gg.POINTS IS NOT NULL OR ga.DUE_DATE IS NULL OR ((ga.DUE_DATE>=ss.START_DATE AND (ss.END_DATE IS NULL OR ga.DUE_DATE<=ss.END_DATE)) AND (ga.DUE_DATE>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR ga.DUE_DATE<=ssm.END_DATE))))".($do_stats&&$_REQUEST['do_stats']?'':" AND s.STUDENT_ID='".UserStudentID()."'");
            $sql .= " GROUP BY gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT,s.STUDENT_ID";
            if($do_stats && $_REQUEST['do_stats'])
            {
                $group = array('STUDENT_ID');
                $all_RET = DBGet(DBQuery($sql),array(),$group);
                $points_RET = $all_RET[UserStudentID()];
            }
            else
                 $points_RET = DBGet(DBQuery($sql));
			//echo '<pre>'; var_dump($points_RET); echo '</pre>';
			//echo '<pre>'; var_dump($all_RET); echo '</pre>';

			if(count($points_RET))
			{
				$total = $total_percent = 0;
				$ungraded = 0;
				foreach($points_RET as $partial_points)
				{
                    if($partial_points['PARTIAL_TOTAL']!=0 || $programconfig[$staff_id]['WEIGHT']!='Y')
					{
						$total += $partial_points['PARTIAL_POINTS']*($programconfig[$staff_id]['WEIGHT']=='Y'?$partial_points['FINAL_GRADE_PERCENT']/$partial_points['PARTIAL_TOTAL']:1);
						$total_percent += ($programconfig[$staff_id]['WEIGHT']=='Y'?$partial_points['FINAL_GRADE_PERCENT']:$partial_points['PARTIAL_TOTAL']);
					}
					$ungraded += $partial_points['UNGRADED'];
				}
				if($total_percent!=0)
					$percent = $total/$total_percent;
				else
					$percent = false;

				if($do_stats && $_REQUEST['do_stats'])
				{
					$min_percent = $max_percent = $percent;
					$avg_percent = 0;
					$lower = $higher = 0;
					foreach($all_RET as $xstudent_id=>$student)
					{
						$total = $total_percent = 0;
						foreach($student as $partial_points)
                            if($partial_points['PARTIAL_TOTAL']!=0 || $programconfig[$staff_id]['WEIGHT']!='Y')
							{
								$total += $partial_points['PARTIAL_POINTS'] * ($programconfig[$staff_id]['WEIGHT']=='Y'?$partial_points['FINAL_GRADE_PERCENT']/$partial_points['PARTIAL_TOTAL']:1);
								$total_percent += ($programconfig[$staff_id]['WEIGHT']=='Y'?$partial_points['FINAL_GRADE_PERCENT']:$partial_points['PARTIAL_TOTAL']);
							}
						if($total_percent!=0)
						{
							$total /= $total_percent;

							if($min_percent===false || $total<$min_percent)
								$min_percent = $total;
							if($max_percent===false || $total>$max_percent)
								$max_percent = $total;
							$avg_percent += $total;
							if($xstudent_id!=UserStudentID() && $percent!==false)
								if($total>$percent)
									$higher++;
								else
									$lower++;
						}
					}
					$avg_percent /= count($all_RET);

					$bargraph1 = bargraph1($percent===false?true:$percent,$min_percent,$avg_percent,$max_percent,1);
					$bargraph2 = bargraph2($percent===false?true:0,$lower,$higher);
				}

				$LO_ret[] = array('ID'=>$course_period_id,'TITLE'=>$course['COURSE_TITLE'],'TEACHER'=>substr($course_title,strrpos(str_replace(' - ',' ^ ',$course_title),'^')+2),'PERCENT'=>($percent!==false?number_format(100*$percent,1).'%':'n/a'),'GRADE'=>($percent!==false?'<b>'._makeLetterGrade($percent,$course_period_id,$staff_id).'</b>':'n/a'),'UNGRADED'=>$ungraded)+($do_stats&&$_REQUEST['do_stats']?array('BAR1'=>$bargraph1,'BAR2'=>$bargraph2):array());
			}
			//else
				//$LO_ret[] = array('ID'=>$course_period_id,'TITLE'=>$course['COURSE_TITLE'],'TEACHER'=>substr($course_title,strrpos(str_replace(' - ',' ^ ',$course_title),'^')+2));
		}
		unset($LO_ret[0]);
		$link = array('TITLE'=>array('link'=>"Modules.php?modname=$_REQUEST[modname]".($do_stats?"&do_stats=$_REQUEST[do_stats]":''),'variables'=>array('id'=>'ID')));
		ListOutput($LO_ret,$LO_columns,'Course','Courses',$link,array(),array('center'=>false,'save'=>false,'search'=>false));
	}
	else
		DrawHeader(_('There are no grades available for this student.'));
}
else
{
    DrawHeader('','','<B>'.$student_RET[1]['FULL_NAME'].'</B>');
	if($_REQUEST['id']=='all')
	{
		DrawHeader('All Courses','');
	}
	else
	{
		$courses_RET = array($_REQUEST['id']=>$courses_RET[$_REQUEST['id']]);
		DrawHeader('<B>'.$courses_RET[$_REQUEST['id']][1]['COURSE_TITLE'].'</B> - '.substr($courses_RET[$_REQUEST['id']][1]['TITLE'],strrpos(str_replace(' - ',' ^ ',$courses_RET[$_REQUEST['id']][1]['TITLE']),'^')+2),"<A HREF=Modules.php?modname=$_REQUEST[modname]".($do_stats?"&do_stats=$_REQUEST[do_stats]":'').">Back to Totals</A>");
	}
	if($do_stats)
		DrawHeader('',CheckBoxOnclick('do_stats')._('Include Anonymous Statistics'));
	//echo '<pre>'; var_dump($courses_RET); echo '</pre>';

    foreach($courses_RET as $course_period_id=>$course)
	{
		$course = $course[1];
		$staff_id = $course['STAFF_ID'];
		if(!$programconfig[$staff_id])
		{
			$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$staff_id' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
			if(count($config_RET))
				foreach($config_RET as $title=>$value)
					$programconfig[$staff_id][$title] = $value[1]['VALUE'];
			else
				$programconfig[$staff_id] = true;
		}

		$assignments_RET = DBGet(DBQuery("SELECT ga.ASSIGNMENT_ID,gg.POINTS,gg.COMMENT,ga.TITLE,ga.DESCRIPTION,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR CURRENT_DATE>(SELECT END_DATE FROM SCHOOL_MARKING_PERIODS WHERE MARKING_PERIOD_ID=ga.MARKING_PERIOD_ID) OR gg.POINTS IS NOT NULL) AND (ga.POINTS!='0' OR gg.POINTS IS NOT NULL AND gg.POINTS!='-1') ORDER BY ga.ASSIGNMENT_ID DESC"),array('TITLE'=>'_makeTipTitle'));
		//echo '<pre>'; var_dump($assignments_RET); echo '</pre>';
		if(count($assignments_RET))
		{
			if($do_stats && $_REQUEST['do_stats'])
				$all_RET = DBGet(DBQuery("SELECT ga.ASSIGNMENT_ID,min(".db_case(array('gg.POINTS',"'-1'",'ga.POINTS','gg.POINTS')).") AS MIN,max(".db_case(array('gg.POINTS',"'-1'",'0','gg.POINTS')).") AS MAX,".db_case(array("sum(".db_case(array('gg.POINTS',"'-1'",'0','1')).")","'0'","'0'","sum(".db_case(array('gg.POINTS',"'-1'",'0','gg.POINTS')).") / sum(".db_case(array('gg.POINTS',"'-1'",'0','1')).")"))." AS AVG,sum(CASE WHEN gg.POINTS!='-1' AND gg.POINTS<=g.POINTS AND gg.STUDENT_ID!=g.STUDENT_ID THEN 1 ELSE 0 END) AS LOWER,sum(CASE WHEN gg.POINTS!='-1' AND gg.POINTS>g.POINTS THEN 1 ELSE 0 END) AS HIGHER FROM GRADEBOOK_GRADES gg,GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES g ON (g.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND g.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND g.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR CURRENT_DATE>(SELECT END_DATE FROM SCHOOL_MARKING_PERIODS WHERE MARKING_PERIOD_ID=ga.MARKING_PERIOD_ID) OR g.POINTS IS NOT NULL) AND ga.POINTS!='0' GROUP BY ga.ASSIGNMENT_ID"),array(),array('ASSIGNMENT_ID'));
			//echo '<pre>'; var_dump($all_RET); echo '</pre>';

			$LO_columns = array('TITLE'=>_('Title'),'CATEGORY'=>_('Category'),'POINTS'=>_('Points / Possible'),'PERCENT'=>_('Percent'));
			if($programconfig[$staff_id]['LETTER_GRADE_ALL']!='Y')
				$LO_columns += array('LETTER'=>_('Letter'));
			$LO_columns += array('COMMENT'=>_('Comment'));
			if($do_stats && $_REQUEST['do_stats'])
				$LO_columns += array('BAR1'=>_('Grade Range'),'BAR2'=>_('Class Rank'));

			$LO_ret = array(0=>array());

			foreach($assignments_RET as $assignment)
			{
				if($do_stats && $_REQUEST['do_stats'])
				{
					if($all_RET[$assignment['ASSIGNMENT_ID']])
					{
						$all = $all_RET[$assignment['ASSIGNMENT_ID']][1];

						if($assignment['POINTS']!='-1' && $assignment['POINTS']!='')
						{
							$bargraph1 = bargraph1($assignment['POINTS'],$all['MIN'],$all['AVG'],$all['MAX'],$assignment['POINTS_POSSIBLE']);
							$bargraph2 = bargraph2(0,$all['LOWER'],$all['HIGHER']);
						}
						else
						{
							$bargraph1 = bargraph1(true,$all['MIN'],$all['AVG'],$all['MAX'],$assignment['POINTS_POSSIBLE']);
							$bargraph2 = bargraph2(true);
						}
					}
					else
					{
						$bargraph1 = bargraph1(false);
						$bargraph2 = bargraph2(false);
					}
				}
				$LO_ret[] = array('TITLE'=>$assignment['TITLE'],'CATEGORY'=>$assignment['CATEGORY'],'POINTS'=>($assignment['POINTS']=='-1'?'*':($assignment['POINTS']==''?'<FONT color=red>0</FONT>':rtrim(rtrim($assignment['POINTS'],'0'),'.'))).' / '.$assignment['POINTS_POSSIBLE'],'PERCENT'=>($assignment['POINTS_POSSIBLE']=='0'?'e/c':($assignment['POINTS']=='-1'?'*':number_format(100*$assignment['POINTS']/$assignment['POINTS_POSSIBLE'],1).'%')),'LETTER'=>($programconfig[$staff_id]['LETTER_GRADE_ALL']=='Y'?'':($assignment['POINTS_POSSIBLE']=='0'?'n/a':($assignment['POINTS']=='-1'?'n/a':($assignment['POINTS_POSSIBLE']>=$programconfig[$staff_id]['LETTER_GRADE_MIN']?'<b>'._makeLetterGrade($assignment['POINTS']/$assignment['POINTS_POSSIBLE'],$course['COURSE_PERIOD_ID'],$staff_id).'</b>':'')))),'COMMENT'=>$assignment['COMMENT'].($assignment['POINTS']==''?($assignment['COMMENT']?'<BR>':'').'<FONT color=red>no grade</FONT>':''))+($do_stats&&$_REQUEST['do_stats']?array('BAR1'=>$bargraph1,'BAR2'=>$bargraph2):array());
			}
			if($_REQUEST['id']=='all')
			{
				echo '<BR>';
				DrawHeader('<B>'.substr($course['TITLE'],0,strpos(str_replace(' - ',' ^ ',$course['TITLE']),'^')).'</B> - '.substr($course['TITLE'],strrpos(str_replace(' - ',' ^ ',$course['TITLE']),'^')+2),"<A HREF=Modules.php?modname=$_REQUEST[modname]".($do_stats?"&do_stats=$_REQUEST[do_stats]":'').">Back to Totals</A>");
			}
			unset($LO_ret[0]);
			ListOutput($LO_ret,$LO_columns,'Assignment','Assignments',array(),array(),array('center'=>false,'save'=>$_REQUEST['id']!='all','search'=>false));
		}
		else
			if($_REQUEST['id']!='all')
				DrawHeader(_('There are no grades available for this student.'));
	}
}
}

function _makeTipTitle($value,$column)
{	global $THIS_RET;

	if(($THIS_RET['DESCRIPTION'] || $THIS_RET['ASSIGNED_DATE'] || $THIS_RET['DUE_DATE']) && !$_REQUEST['_CENTRE_PDF'])
	{
		if($THIS_RET['DESCRIPTION'])
		{
			$tip_title = str_replace(array("'",'"'),array('&#39;','&rdquo;'),$THIS_RET['DESCRIPTION']);
			$tip_title = Localize('colon',_('Description')).' '.str_replace("\r\n",'<BR>',$tip_title);
		}
		if($THIS_RET['ASSIGNED_DATE'])
			$tip_title .= ($tip_title?'<BR>':'').Localize('colon',_('Assigned')).' '.ProperDate($THIS_RET['ASSIGNED_DATE']);
		if($THIS_RET['DUE_DATE'])
			$tip_title .= ($tip_title?'<BR>':'').Localize('colon',_('Due')).' '.ProperDate($THIS_RET['DUE_DATE']);
		$tip_title = '<A HREF=# onMouseOver=\'stm(["'._('Details').'","'.$tip_title.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'>'.$value.'</A>';
	}
	else
		$tip_title = $value;

	return $tip_title;
}

function bargraph1($x,$lo,$avg,$hi,$max)
{
	if($x!==false)
	{
		$scale = $hi>$max?$hi:$max;
		$w1 = round(100*$lo/$scale);
		$w5 = round(100*(1.0-$hi/$scale));
		if($x!==true)
		{
			if($x<$avg)
			{
				$w2 = round(100*($x-$lo)/$scale); $c2 = '#ff0000';
				$w4 = round(100*($hi-$avg)/$scale); $c4 = '#00ff00';
			}
			else
			{
				$w2 = round(100*($avg-$lo)/$scale); $c2 = '#00ff00';
				$w4 = round(100*($hi-$x)/$scale); $c4 = '#ff0000';
			}
			$w3 = 100-$w1-$w2-$w4-$w5;
			return '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0?"<TD width=$w1%></TD>":'').($w2>0?"<TD width=$w2% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c2>&nbsp;</TD>".($w3>0?"<TD width=$w3% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c4>&nbsp;</TD>".($w4>0?"<TD width=$w4% bgcolor=#00a000></TD>":'').($w5>0?"<TD width=$w5%></TD>":'').'</TR></TABLE>';
		}
		else
		{
			$w2 = round(100*($avg-$lo)/$scale);
			$w4 = round(100*($hi-$avg)/$scale);
			return '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0?"<TD width=$w1%></TD>":'').($w2>0?"<TD width=$w2% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=#00ff00>&nbsp;</TD>".($w4>0?"<TD width=$w4% bgcolor=#00a000></TD>":'').($w5>0?"<TD width=$w5%></TD>":'').'</TR></TABLE>';
		}
	}
	else
		return '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0><TD width=100%>&nbsp;</TD></TR></TABLE>';
}

function bargraph2($x,$lo,$hi)
{
	if($x!==false)
		if($x!==true)
		{
			$scale = $lo+$hi+1;
			$w1 = round(100*$lo/$scale);
			$w3 = round(100*$hi/$scale);
			$w2 = 100-$w1-$w3;
			return '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0||$lower>0?"<TD width=$w1%></TD>":'')."<TD width=$w2% bgcolor=#ff0000>&nbsp;</TD>".($w3>0||$higher>0?"<TD width=$w3%></TD>":'').'</TR></TABLE>';
		}
		else
			return '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0><TD width=100%>&nbsp;</TD></TR></TABLE>';
	else
		return '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0><TD width=100%>&nbsp;</TD></TR></TABLE>';
}
?>
