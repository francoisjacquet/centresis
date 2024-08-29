<?php
include 'modules/Grades/config.inc.php';

DrawHeader(ProgramTitle());

$sem = GetParentMP('SEM',UserMP());
$fy = GetParentMP('FY',$sem);
$pros = GetChildrenMP('PRO',UserMP());

// if the UserMP has been changed, the REQUESTed MP may not work
if(!$_REQUEST['mp'] || strpos($str="'".UserMP()."','".$sem."','".$fy."',".$pros,"'".ltrim($_REQUEST['mp'],'E')."'")===false)
	$_REQUEST['mp'] = UserMP();

$course_period_id = UserCoursePeriod();

function credit($i, $v) {
$course_detail = DBGet(DBQuery("SELECT * FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID = '".$i."'"));
$marking_detail = DBGet(DBQuery("SELECT * FROM SCHOOL_MARKING_PERIODS WHERE MARKING_PERIOD_ID = '".$v."'"));
//echo $course_detail[1]['MARKING_PERIOD_ID'].' : TEST : '.$marking_detail[1]['MARKING_PERIOD_ID'];
if($course_detail['MARKING_PERIOD_ID']==$marking_detail['MARKING_PERIOD_ID']):
	return $course_detail[1]['CREDITS'];
elseif($course_detail[1]['MP']=='FY' && $marking_detail[1]['MP']=='FY'):
	$dot_values = DBGet(DBQuery("SELECT COUNT(*) AS MP_COUNT FROM SCHOOL_MARKING_PERIODS where PARENT_ID = '".$course_detail[1]['MARKING_PERIOD_ID']."' group by PARENT_ID"));
elseif($course_detail[1]['MP']=='FY' && $marking_detail[1]['MP']=='QTR'):
	$dot_values = DBGet(DBQuery("SELECT COUNT(*) AS MP_COUNT FROM SCHOOL_MARKING_PERIODS where PARENT_ID = '".$course_detail[1]['MARKING_PERIOD_ID']."' group by PARENT_ID"));
elseif($course_detail[1]['MP']=='SEM' && $marking_detail[1]['MP']=='QTR'):
	$dot_values = DBGet(DBQuery("SELECT COUNT(*) AS MP_COUNT FROM SCHOOL_MARKING_PERIODS where PARENT_ID = '".$course_detail[1]['MARKING_PERIOD_ID']."' group by PARENT_ID"));
else:
	return 0;
endif;

if($dot_values[1]['MP_COUNT'] > 0):
	return int($course_detail[1]['CREDITS'])/int($dot_values[1]['MP_COUNT']);
else:
	return 0;
endif;
}

//$course_RET = DBGet(DBQuery("SELECT cp.COURSE_ID,c.TITLE as COURSE_NAME, cp.TITLE, cp.GRADE_SCALE_ID, credit($course_period_id, '".$_REQUEST['mp']."') AS CREDITS, (SELECT ATTENDANCE FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID) AS ATTENDANCE FROM COURSE_PERIODS cp, COURSES c WHERE cp.COURSE_ID = c.COURSE_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."'"));
$credits_mp = credit($course_period_id, $_REQUEST['mp']); //echo $credits_mp.' NICK';
$course_RET = DBGet(DBQuery("SELECT cp.COURSE_ID,c.TITLE as COURSE_NAME, cp.TITLE, cp.GRADE_SCALE_ID, '".$credits_mp."' AS CREDITS FROM COURSE_PERIODS cp, COURSES c WHERE cp.COURSE_ID = c.COURSE_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."'"));
if(!$course_RET[1]['GRADE_SCALE_ID'])                                  
	ErrorMessage(array(_('You cannot enter grades for this period.')),'fatal');
$course_title = $course_RET[1]['TITLE'];
$grade_scale_id = $course_RET[1]['GRADE_SCALE_ID'];
$course_id = $course_RET[1]['COURSE_ID'];

$current_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_GRADE_ID,g.GRADE_PERCENT,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_GRADES g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."'"),array(),array('STUDENT_ID'));
$current_completed = count(DBGet(DBQuery("SELECT * FROM GRADES_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND COURSE_PERIOD_ID='".$course_period_id."'")));

$grades_RET = DBGet(DBQuery("SELECT rcg.ID,rcg.TITLE,rcg.GPA_VALUE AS WEIGHTED_GP, rcg.UNWEIGHTED_GP ,gs.GP_SCALE  FROM REPORT_CARD_GRADES rcg, REPORT_CARD_GRADE_SCALES gs WHERE rcg.grade_scale_id = gs.id AND rcg.SYEAR='".UserSyear()."' AND rcg.SCHOOL_ID='".UserSchool()."' AND rcg.GRADE_SCALE_ID='$grade_scale_id' ORDER BY rcg.BREAK_OFF IS NOT NULL DESC,rcg.BREAK_OFF DESC,rcg.SORT_ORDER"),array(),array('ID'));

$cat_union_1 = count(DBGet(DBQuery("SELECT count(1) FROM REPORT_CARD_COMMENTS AS rcm INNER JOIN REPORT_CARD_COMMENT_CATEGORIES AS rc ON (rcm.COURSE_ID=rc.COURSE_ID AND rcm.CATEGORY_ID=rc.ID)")));
$cat_union_2 = count(DBGet(DBQuery("SELECT count(1) FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND COURSE_ID='0' AND SYEAR='".UserSyear()."'")));
$cat_union_3 = count(DBGet(DBQuery("SELECT count(1) FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND COURSE_ID IS NULL AND SYEAR='".UserSyear()."'")));

$categories_SQL = "SELECT rc.ID,rc.TITLE,rc.COLOR,1,rc.SORT_ORDER FROM REPORT_CARD_COMMENT_CATEGORIES rc WHERE rc.COURSE_ID='".$course_id."'";
$categories_SQL .= ($cat_union_2>0)?" UNION SELECT 0 IS NULL,'All Courses',NULL,2,NULL":"";
$categories_SQL .= ($cat_union_3>0)?" UNION SELECT -1,'General',NULL,3,NULL":"";
$categories_RET = DBGet(DBQuery($categories_SQL),array(),array('ID'));

if($_REQUEST['tab_id']=='' || !$categories_RET[$_REQUEST['tab_id']])
	$_REQUEST['tab_id'] = key($categories_RET).'';

$comment_codes_RET = DBGet(DBQuery("SELECT SCALE_ID,TITLE,SHORT_NAME FROM REPORT_CARD_COMMENT_CODES WHERE SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER,ID"),array(),array('SCALE_ID'));
$commentsA_select = array();
foreach($comment_codes_RET as $scale_id=>$codes)
	foreach($codes as $code)
		$commentsA_select[$scale_id][$code['TITLE']] = $code['SHORT_NAME'] ? array($code['TITLE'],$code['SHORT_NAME']) : $code['TITLE'];

if($_REQUEST['tab_id']=='-1')
{
	$commentsB_RET = DBGet(DBQuery("SELECT ID,TITLE,SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID IS NULL ORDER BY SORT_ORDER"),array(),array('ID'));
	$current_commentsB_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL)"),array(),array('STUDENT_ID'));
	$max_current_commentsB = 0;
	foreach($current_commentsB_RET as $comments)
		if(count($comments)>$max_current_commentsB)
			$max_current_commentsB = count($comments);
}
elseif($_REQUEST['tab_id']=='0')
{
	$commentsA_RET = DBGet(DBQuery("SELECT ID,TITLE,SCALE_ID FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID='0' ORDER BY SORT_ORDER"));
	$current_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID='0')"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
}
elseif($_REQUEST['tab_id'])
{
	$commentsA_RET = DBGet(DBQuery("SELECT ID,TITLE,SCALE_ID FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID='".$course_id."' AND CATEGORY_ID='".$_REQUEST['tab_id']."' ORDER BY SORT_ORDER"));
	$current_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE CATEGORY_ID='".$_REQUEST['tab_id']."')"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
}

$grades_select = array(''=>'');
foreach($grades_RET as $key=>$grade)
{
	$grade = $grade[1];
	$grades_select += array($grade['ID']=>array($grade['TITLE'],'<b>'.$grade['TITLE'].'</b>'));
}
$commentsB_select = array();
if(0)
foreach($commentsB_RET as $id=>$comment)
	$commentsB_select += array($id=>array($comment[1]['SORT_ORDER'],$comment[1]['TITLE']));
else
foreach($commentsB_RET as $id=>$comment)
	$commentsB_select += array($id=>array($comment[1]['SORT_ORDER'].' - '.($commentsB_len && strlen($comment[1]['TITLE'])>$commentsB_len+3?substr($comment[1]['TITLE'],0,$commentsN_len-1).'...':$comment[1]['TITLE']),$comment[1]['TITLE']));

if($_REQUEST['modfunc']=='gradebook')
{
	if($_REQUEST['mp'])
	{
		$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".User('STAFF_ID')."' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
		if(count($config_RET))
			foreach($config_RET as $title=>$value)
				$programconfig[User('STAFF_ID')][$title] = $value[1]['VALUE'];
		else
			$programconfig[User('STAFF_ID')] = true;
		$_CENTRE['_makeLetterGrade']['courses'][$course_period_id] = DBGet(DBQuery("SELECT DOES_BREAKOFF,GRADE_SCALE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$course_period_id."'"));

		include 'ProgramFunctions/_makeLetterGrade.fnc.php';

		if(GetMP($_REQUEST['mp'],'MP')=='QTR' || GetMP($_REQUEST['mp'],'MP')=='PRO')
		{
			// Note: The 'active assignment' determination is not fully correct.  It would be easy to be fully correct here but the same determination
			// as in Grades.php is used to avoid apparent inconsistencies in the grade calculations.  See also the note at top of Grades.php.
			$extra['SELECT_ONLY'] = "s.STUDENT_ID,     gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT";
			$extra['FROM'] = " JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID OR ga.COURSE_ID=cp.COURSE_ID AND ga.STAFF_ID=cp.TEACHER_ID) AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID),GRADEBOOK_ASSIGNMENT_TYPES gt";
			$extra['WHERE'] = " AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID=cp.COURSE_ID AND (gg.POINTS IS NOT NULL OR (ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR CURRENT_DATE>(SELECT END_DATE FROM SCHOOL_MARKING_PERIODS WHERE MARKING_PERIOD_ID=ga.MARKING_PERIOD_ID))";
			$extra['WHERE'] .=" AND (gg.POINTS IS NOT NULL OR ga.DUE_DATE IS NULL OR ((ga.DUE_DATE>=ss.START_DATE AND (ss.END_DATE IS NULL OR ga.DUE_DATE<=ss.END_DATE)) AND (ga.DUE_DATE>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR ga.DUE_DATE<=ssm.END_DATE))))";
			$extra['GROUP'] = "gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT,s.STUDENT_ID";
			$extra['group'] = array('STUDENT_ID');
			$points_RET = GetStuList($extra);
			//echo '<pre>'; var_dump($points_RET); echo '</pre>';
			unset($extra);

			if(count($points_RET))
			{
				foreach($points_RET as $student_id=>$student)
				{
					$total = $total_percent = 0;
					foreach($student as $partial_points)
						if($partial_points['PARTIAL_TOTAL']!=0 || $programconfig[User('STAFF_ID')]['WEIGHT']!='Y')
						{
							$total += $partial_points['PARTIAL_POINTS']*($programconfig[User('STAFF_ID')]['WEIGHT']=='Y'?$partial_points['FINAL_GRADE_PERCENT']/$partial_points['PARTIAL_TOTAL']:1);
							$total_percent += ($programconfig[User('STAFF_ID')]['WEIGHT']=='Y'?$partial_points['FINAL_GRADE_PERCENT']:$partial_points['PARTIAL_TOTAL']);
						}
					if($total_percent!=0)
						$total /= $total_percent;

					$import_RET[$student_id] = array(1=>array('REPORT_CARD_GRADE_ID'=>_makeLetterGrade($total,$course_period_id,0,'ID'),'GRADE_PERCENT'=>round(100*$total,1)));
				}
			}
		}
		elseif(GetMP($_REQUEST['mp'],'MP')=='SEM' || GetMP($_REQUEST['mp'],'MP')=='FY')
		{
			if(GetMP($_REQUEST['mp'],'MP')=='SEM')
			{
				$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,'Y' AS DOES_GRADES,NULL AS DOES_EXAM FROM SCHOOL_MARKING_PERIODS WHERE MP='QTR' AND PARENT_ID='$_REQUEST[mp]' UNION SELECT MARKING_PERIOD_ID,NULL AS DOES_GRADES,DOES_EXAM FROM SCHOOL_MARKING_PERIODS WHERE MP='SEM' AND MARKING_PERIOD_ID='$_REQUEST[mp]'"));
				$prefix = 'SEM-';
			}
			else
			{
				$RET = DBGet(DBQuery("SELECT q.MARKING_PERIOD_ID,'Y' AS DOES_GRADES,NULL AS DOES_EXAM FROM SCHOOL_MARKING_PERIODS q,SCHOOL_MARKING_PERIODS s WHERE q.MP='QTR' AND s.MP='SEM' AND q.PARENT_ID=s.MARKING_PERIOD_ID AND s.PARENT_ID='$_REQUEST[mp]' UNION SELECT MARKING_PERIOD_ID,DOES_GRADES,DOES_EXAM FROM SCHOOL_MARKING_PERIODS WHERE MP='SEM' AND PARENT_ID='$_REQUEST[mp]' UNION SELECT MARKING_PERIOD_ID,NULL AS DOES_GRADES,DOES_EXAM FROM SCHOOL_MARKING_PERIODS WHERE MP='FY' AND MARKING_PERIOD_ID='$_REQUEST[mp]'"));
				$prefix = 'FY-';
			}
			foreach($RET as $mp)
			{
				if($mp['DOES_GRADES']=='Y')
					$mps .= "'$mp[MARKING_PERIOD_ID]',";
				if($mp['DOES_EXAM']=='Y')
					$mps .= "'E$mp[MARKING_PERIOD_ID]',";
			}
			$mps = substr($mps,0,-1);

			$percents_RET = DBGet(DBQuery("SELECT STUDENT_ID,GRADE_PERCENT,MARKING_PERIOD_ID FROM STUDENT_REPORT_CARD_GRADES WHERE COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID IN ($mps)"),array(),array('STUDENT_ID'));

			foreach($percents_RET as $student_id=>$percents)
			{
				$total = $total_percent = 0;
				foreach($percents as $percent)
				{
					$total += $percent['GRADE_PERCENT'] * $programconfig[User('STAFF_ID')][$prefix.$percent['MARKING_PERIOD_ID']];
					$total_percent += $programconfig[User('STAFF_ID')][$prefix.$percent['MARKING_PERIOD_ID']];
				}
				$total /= $total_percent;

				$import_RET[$student_id] = array(1=>array('REPORT_CARD_GRADE_ID'=>_makeLetterGrade($total/100,$course_period_id,0,'ID'),'GRADE_PERCENT'=>round($total,1)));
			}
		}
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if($_REQUEST['modfunc']=='grades')
{
	if($_REQUEST['prev_mp'])
	{
		include 'ProgramFunctions/_makePercentGrade.fnc.php';

		$import_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_GRADE_ID,g.GRADE_PERCENT FROM STUDENT_REPORT_CARD_GRADES g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."'"),array(),array('STUDENT_ID'));
		foreach($import_RET as $student_id=>$grade)
			$import_RET[$student_id][1]['GRADE_PERCENT'] = _makePercentGrade($grade[1]['REPORT_CARD_GRADE_ID'],$course_period_id);

		unset($_SESSION['_REQUEST_vars']['prev_mp']);
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if($_REQUEST['modfunc']=='comments')
{
	if($_REQUEST['prev_mp'])
	{
		$import_comments_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_GRADES g WHERE g.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."'"),array(),array('STUDENT_ID'));
		$import_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g WHERE g.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NOT NULL)"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
		//echo '<pre>'; var_dump($import_commentsA_RET); echo '</pre>';
		$import_commentsB_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID FROM STUDENT_REPORT_CARD_COMMENTS g WHERE g.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL)"),array(),array('STUDENT_ID'));

		foreach($import_commentsB_RET as $comments)
			if(count($comments)>$max_current_commentsB)
				$max_current_commentsB = count($comments);

		unset($_SESSION['_REQUEST_vars']['prev_mp']);
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if($_REQUEST['modfunc']=='clearall')
{
	foreach($current_RET as $student_id=>$prev)
	{
		$current_RET[$student_id][1]['REPORT_CARD_GRADE_ID'] = '';
		$current_RET[$student_id][1]['GRADE_PERCENT'] = '';
		$current_RET[$student_id][1]['COMMENT'] = '';
	}
	foreach($current_commentsA_RET as $student_id=>$comments)
		foreach($comments as $id=>$comment)
			$current_commentsA_RET[$student_id][$id][1]['COMMENT'] = '';
	foreach($current_commentsB_RET as $student_id=>$comment)
		foreach($comment as $i=>$comment)
			$current_commentsB_RET[$student_id][$i] = '';
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if($_REQUEST['values'] && $_POST['values'])
{
	include 'ProgramFunctions/_makeLetterGrade.fnc.php';
	include 'ProgramFunctions/_makePercentGrade.fnc.php';
	$completed = true;
	foreach($_REQUEST['values'] as $student_id=>$columns)
	{
		$sql = $sep = '';
		if($current_RET[$student_id])
		{
			if($columns['percent']!='')
			{
				$percent = rtrim($columns['percent'],'%');
				if($percent>999.9)
					$percent = '999.9';
				elseif($percent<0)
					$percent = '0';
				if($columns['grade'] || $percent!='')
				{
					$grade = ($columns['grade']?$columns['grade']:_makeLetterGrade($percent/100,$course_period_id,0,'ID'));
					$letter = $grades_RET[$grade][1]['TITLE'];
					$weighted = $grades_RET[$grade][1]['WEIGHTED_GP'];
					$unweighted = $grades_RET[$grade][1]['UNWEIGHTED_GP'];
					$scale = $grades_RET[$grade][1]['GP_SCALE'];
				}
				else
					$grade = $letter = $weighted = $unweighted = $scale = '';
				$sql .= "GRADE_PERCENT='".$percent."'";
				$sql .= ",REPORT_CARD_GRADE_ID='".$grade."',GRADE_LETTER='".$letter."',WEIGHTED_GP='".$weighted."',UNWEIGHTED_GP='".$unweighted."',GP_SCALE='".$scale."'";
				//bjj can we use $percent all the time?  TODO: rework this so updates to credits occur when grade is changed
				$sql .= ",COURSE_TITLE='".$course_RET[1]['COURSE_NAME']."'";
				$sql .= ",CREDIT_ATTEMPTED='".($course_RET[1]['CREDITS']>0?$course_RET[1]['CREDITS']:'0')."'";
				$sql .= ",CREDIT_EARNED='".($weighted&&$weighted>0?$course_RET[1]['CREDITS']:'0')."'";
				$sep = ',';
			}
			elseif($columns['grade'])
			{
				$percent = _makePercentGrade($columns['grade'],$course_period_id);
				$grade = $columns['grade'];
				$letter = $grades_RET[$grade][1]['TITLE'];
				$weighted = $grades_RET[$grade][1]['WEIGHTED_GP'];
				$unweighted = $grades_RET[$grade][1]['UNWEIGHTED_GP'];
				$scale = $grades_RET[$grade][1]['GP_SCALE'];
				$sql .= "GRADE_PERCENT='".$percent."'";
				$sql .= ",REPORT_CARD_GRADE_ID='".$grade."',GRADE_LETTER='".$letter."',WEIGHTED_GP='".$weighted."',UNWEIGHTED_GP='".$unweighted."',GP_SCALE='".$scale."'";
				$sql .= ",COURSE_TITLE='".$course_RET[1]['COURSE_NAME']."'";
				$sql .= ",CREDIT_ATTEMPTED='".($course_RET[1]['CREDITS']>0?$course_RET[1]['CREDITS']:'0')."'";
				$sql .= ",CREDIT_EARNED='".($weighted&&$weighted>0?$course_RET[1]['CREDITS']:'0')."'";
				$sep = ',';
			}
			elseif(isset($columns['percent']) || isset($columns['grade']))
			{
				$percent = $grade = '';
				$sql .= "GRADE_PERCENT=NULL";
				$sql .= ",REPORT_CARD_GRADE_ID=NULL,GRADE_LETTER=NULL,WEIGHTED_GP='NULL',UNWEIGHTED_GP='NULL',GP_SCALE='NULL'";
				$sql .= ",COURSE_TITLE='".$course_RET[1]['COURSE_NAME']."'";
				$sql .= ",CREDIT_ATTEMPTED='".($course_RET[1]['CREDITS']>0?$course_RET[1]['CREDITS']:'0')."'";
				$sql .= ",CREDIT_EARNED='0'";
				$sep = ',';
			}
			else
			{
				$percent = $current_RET[$student_id][1]['GRADE_PERCENT'];
				$grade = $current_RET[$student_id][1]['REPORT_CARD_GRADE_ID'];
			}

			if(isset($columns['comment']))
				$sql .= $sep."COMMENT='".str_replace("\'","''",$columns['comment'])."'";
			if($sql)
				$sql = "UPDATE STUDENT_REPORT_CARD_GRADES SET ".$sql." WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."'";
		}
		elseif($columns['percent']!='' || $columns['grade'] || $columns['comment'])
		{ 

			$letter = $grades_RET[$grade][1]['TITLE'];
			$weighted = ($grades_RET[$grade][1]['WEIGHTED_GP'])?$grades_RET[$grade][1]['WEIGHTED_GP']:0;
			$unweighted = ($grades_RET[$grade][1]['UNWEIGHTED_GP'])?$grades_RET[$grade][1]['UNWEIGHTED_GP']:0;
			$scale = ($grades_RET[$grade][1]['GP_SCALE'])?$grades_RET[$grade][1]['GP_SCALE']:0;
								
			if($columns['percent']!='')
			{
				$percent = rtrim($columns['percent'],'%');
				if($percent>999.9)
					$percent = '999.9';
				elseif($percent<0)
					$percent = '0';
				if($columns['grade'])
					$grade = $columns['grade'];
				else
					$grade = ($percent!=''?_makeLetterGrade($percent/100,$course_period_id,0,'ID'):'');
			}
			elseif($columns['grade'])
			{
					$percent = _makePercentGrade($columns['grade'],$course_period_id);
					$grade = $columns['grade'];
			}
			else
				$percent = $grade = $letter = $weighted = $unweighted = $scale = '';


 			$sql = "INSERT INTO STUDENT_REPORT_CARD_GRADES (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_PERIOD_ID,MARKING_PERIOD_ID,REPORT_CARD_GRADE_ID,GRADE_PERCENT,COMMENT,GRADE_LETTER,WEIGHTED_GP,UNWEIGHTED_GP,GP_SCALE,COURSE_TITLE,CREDIT_ATTEMPTED,CREDIT_EARNED)
				values('".UserSyear()."','".UserSchool()."','".$student_id."','".$course_period_id."','".$_REQUEST['mp']."','".$grade."','".$percent."','".str_replace("\'","''",$columns['comment'])."','".$grades_RET[$grade][1]['TITLE']."','".$weighted."','".$unweighted."','".$scale."','".$course_RET[1]['COURSE_NAME']."','".($course_RET[1]['CREDITS']>0?$course_RET[1]['CREDITS']:'0')."','".($weighted&&$weighted>0?$course_RET[1]['CREDITS']:'0')."')";
		}
		else
			$percent = $grade = '';

		if($sql)
		{
			//echo $sql . ' NICK-SQL';
			DBQuery($sql);
		}
		//DBQuery("DELETE FROM STUDENT_REPORT_CARD_GRADES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."'");
		if(!($does_letter_percent<0?$grade:($does_letter_percent>0?$percent!='':$percent!=''&&$grade)))
			$completed = false;

		foreach($columns['commentsA'] as $id=>$comment)
			if($current_commentsA_RET[$student_id][$id])
				if($comment)
					DBQuery("UPDATE STUDENT_REPORT_CARD_COMMENTS SET COMMENT='".str_replace("\'","''",$comment)."' WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$id."'");
				else
					DBQuery("DELETE FROM STUDENT_REPORT_CARD_COMMENTS WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$id."'");
			elseif($comment)
					DBQuery("INSERT INTO STUDENT_REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_PERIOD_ID,MARKING_PERIOD_ID,REPORT_CARD_COMMENT_ID,COMMENT)
						values('".UserSyear()."','".UserSchool()."','".$student_id."','".$course_period_id."','".$_REQUEST['mp']."','".$id."','".$comment."')");

		// create mapping for current
		$old = array();
		foreach($current_commentsB_RET[$student_id] as $i=>$comment)
			$old[$comment['REPORT_CARD_COMMENT_ID']] = $i;
		// create change list
		$change = array();
		foreach($columns['commentsB'] as $i=>$comment)
			$change[$i] = array('REPORT_CARD_COMMENT_ID'=>0);
		// prune changes already in current set and reserve if in change list
		foreach($columns['commentsB'] as $i=>$comment)
			if($comment)
				if($old[$comment])
				{
					if($change[$old[$comment]])
						$change[$old[$comment]]['REPORT_CARD_COMMENT_ID'] = $comment;
					$columns['commentsB'][$i] = false;
				}
		// assign changes at their index if possible
		$new = array();
		foreach($columns['commentsB'] as $i=>$comment)
			if($comment)
				if(!$new[$comment])
				{
					if(!$change[$i]['REPORT_CARD_COMMENT_ID'])
					{
						$change[$i]['REPORT_CARD_COMMENT_ID'] = $comment;
						$new[$comment] = $i;
						$columns['commentsB'][$i] = false;
					}
				}
				else
					$columns['commentsB'][$i] = false;
		// assign remaining changes to first available
		reset($change);
		foreach($columns['commentsB'] as $i=>$comment)
			if($comment)
			{
				if(!$new[$comment])
				{
					while($change[key($change)]['REPORT_CARD_COMMENT_ID'])
						next($change);
					$change[key($change)]['REPORT_CARD_COMMENT_ID'] = $comment;
					$new[$comment] = key($change);
				}
				$columns['commentsB'][$i] = false;
			}

		// update the db
		foreach($change as $i=>$comment)
			if($current_commentsB_RET[$student_id][$i])
				if($comment['REPORT_CARD_COMMENT_ID'])
				{
					if($comment['REPORT_CARD_COMMENT_ID']!=$current_commentsB_RET[$student_id][$i]['REPORT_CARD_COMMENT_ID'])
						DBQuery("UPDATE STUDENT_REPORT_CARD_COMMENTS SET REPORT_CARD_COMMENT_ID='".$comment['REPORT_CARD_COMMENT_ID']."' WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$current_commentsB_RET[$student_id][$i]['REPORT_CARD_COMMENT_ID']."'");
				}
				else
					DBQuery("DELETE FROM STUDENT_REPORT_CARD_COMMENTS WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$current_commentsB_RET[$student_id][$i]['REPORT_CARD_COMMENT_ID']."'");
			else
				if($comment['REPORT_CARD_COMMENT_ID'])
					DBQuery("INSERT INTO STUDENT_REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_PERIOD_ID,MARKING_PERIOD_ID,REPORT_CARD_COMMENT_ID)
						values('".UserSyear()."','".UserSchool()."','".$student_id."','".$course_period_id."','".$_REQUEST['mp']."','".$comment['REPORT_CARD_COMMENT_ID']."')");
	}

	if($completed)
	{
		if(!$current_completed)
			DBQuery("INSERT INTO GRADES_COMPLETED (STAFF_ID,MARKING_PERIOD_ID,COURSE_PERIOD_ID) values('".User('STAFF_ID')."','".$_REQUEST['mp']."','$course_period_id')");
	}
	else
		if($current_completed)
			DBQuery("DELETE FROM GRADES_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND COURSE_PERIOD_ID='".$course_period_id."'");

	$current_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_GRADE_ID,g.GRADE_PERCENT,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_GRADES g WHERE g.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."'"),array(),array('STUDENT_ID'));
	if($_REQUEST['tab_id']=='-1')
	{
        	$current_commentsB_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL)"),array(),array('STUDENT_ID'));
        	$max_current_commentsB = 0;
        	foreach($current_commentsB_RET as $comments)
                	if(count($comments)>$max_current_commentsB)
                        	$max_current_commentsB = count($comments);
	}
	elseif($_REQUEST['tab_id']=='0')
        	$current_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID='0')"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
	elseif($_REQUEST['tab_id'])
		$current_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE CATEGORY_ID='".$_REQUEST['tab_id']."')"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
	$current_completed = count(DBGet(DBQuery("SELECT '' FROM GRADES_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND COURSE_PERIOD_ID='".$course_period_id."'")));
	unset($_SESSION['_REQUEST_vars']['values']);
}

if($_REQUEST['values'] && $_POST['values'] && $_REQUEST['submit']['cancel'])
{
	unset($_SESSION['_REQUEST_vars']['values']);
}

$time = strtotime(DBDate('mysql'));

$mps_select = '<SELECT name=mp onchange="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include_inactive='.$_REQUEST['include_inactive'].'&mp=\'+this.options[selectedIndex].value">';
if($pros!='')
	foreach(explode(',',str_replace("'",'',$pros)) as $pro)
	{
		if($_REQUEST['mp']==$pro && GetMP($pro,'POST_START_DATE') && ($time>=strtotime(GetMP($pro,'POST_START_DATE')) && $time<=strtotime(GetMP($pro,'POST_END_DATE'))))
			$allow_edit = true;
		if(GetMP($pro,'DOES_GRADES')=='Y')
			$mps_select .= "<OPTION value=".$pro.(($pro==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($pro)."</OPTION>";
	}

if($_REQUEST['mp']==UserMP() && GetMP(UserMP(),'POST_START_DATE') && ($time>=strtotime(GetMP(UserMP(),'POST_START_DATE')) && $time<=strtotime(GetMP(UserMP(),'POST_END_DATE'))))
	$allow_edit = true;
$mps_select .= "<OPTION value=".UserMP().((UserMP()==$_REQUEST['mp'])?' SELECTED':'').">".GetMP(UserMP())."</OPTION>";

if(($_REQUEST['mp']==$sem || $_REQUEST['mp']=='E'.$sem) && GetMP($sem,'POST_START_DATE') && ($time>=strtotime(GetMP($sem,'POST_START_DATE')) && $time<=strtotime(GetMP($sem,'POST_END_DATE'))))
	$allow_edit = true;
if(GetMP($sem,'DOES_GRADES')=='Y')
	$mps_select .= "<OPTION value=$sem".(($sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)."</OPTION>";
if(GetMP($sem,'DOES_EXAM')=='Y')
	$mps_select .= "<OPTION value=E$sem".(('E'.$sem==$_REQUEST['mp'])?' SELECTED':'').">".sprintf(_('%s Exam'),GetMP($sem))."</OPTION>";

if(($_REQUEST['mp']==$fy || $_REQUEST['mp']=='E'.$fy) && GetMP($fy,'POST_START_DATE') && ($time>=strtotime(GetMP($fy,'POST_START_DATE')) && $time<=strtotime(GetMP($fy,'POST_END_DATE'))))
	$allow_edit = true;
if(GetMP($fy,'DOES_GRADES')=='Y')
	$mps_select .= "<OPTION value=".$fy.(($fy==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($fy)."</OPTION>";
if(GetMP($fy,'DOES_EXAM')=='Y')
	$mps_select .= "<OPTION value=E".$fy.(('E'.$fy==$_REQUEST['mp'])?' SELECTED':'').">".sprintf(_('%s Exam'),GetMP($fy))."</OPTION>";

$mps_select .= '</SELECT>';

// if running as a teacher program then centre[allow_edit] will already be set according to admin permissions
if(!isset($_CENTRE['allow_edit']))
	$_CENTRE['allow_edit'] = $teacher_allow_edit||$allow_edit;

$extra['SELECT'] = ",ssm.STUDENT_ID AS REPORT_CARD_GRADE";
$extra['functions'] = array('REPORT_CARD_GRADE'=>'_makeLetterPercent');

if(substr($_REQUEST['mp'],0,1)!='E' && GetMP($_REQUEST['mp'],'DOES_COMMENTS')=='Y')
{
	foreach($commentsA_RET as $value)
	{
        $extra['SELECT'] .= ',\''.$value['ID'].'\' AS CA'.$value['ID'].',\''.$value['SCALE_ID'].'\' AS CAC'.$value['ID'];
		$extra['functions'] += array('CA'.$value['ID']=>'_makeCommentsA');
	}
	for($i=1; $i<=$max_current_commentsB; $i++)
	{
		$extra['SELECT'] .= ',\''.$i.'\' AS CB'.$i;
		$extra['functions'] += array('CB'.$i=>'_makeCommentsB');
	}
	if(count($commentsB_select) && AllowEdit())
	{
		$extra['SELECT'] .= ',\''.$i.'\' AS CB'.$i;
		$extra['functions'] += array('CB'.$i=>'_makeCommentsB');
	}
}
$extra['SELECT'] .= ",'' AS COMMENTS,'' AS COMMENT";
$extra['functions'] += array('COMMENT'=>'_makeComment');
$extra['MP'] = UserMP();
$extra['DATE'] = GetMP($_REQUEST['mp'],'END_DATE');

$stu_RET = GetStuList($extra);

echo "<FORM action=Modules.php?modname=$_REQUEST[modname]".(count($categories_RET)&&GetMP($_REQUEST['mp'],'DOES_COMMENTS')=='Y'?"&tab_id=$_REQUEST[tab_id]":'')." method=POST>";

if(!$_REQUEST['_CENTRE_PDF'])
{
	if(count($commentsB_RET))
	{
		foreach($commentsB_RET as $comment)
			$tipmessage .= $comment[1]['SORT_ORDER'].' - '.str_replace("'",'&acute;',$comment[1]['TITLE']).'<BR>';
		$tipmessage = button('comment','Comment Codes','# onClick=\'stm(["Report Card Comments","'.$tipmessage.'"],["white","#333366","","","",,"black","#e8e8ff","","","",,,,2,"#333366",2,,,,,"",5,3,50,50]);\'','');
	}

	DrawHeader($mps_select,SubmitButton(_('Save')),CheckBoxOnclick('include_inactive')._('Include Inactive Students'));
	DrawHeader(($current_completed?'<FONT COLOR=green>'._('These grades are complete.').'</FONT>':'<FONT COLOR=red>'._('These grades are NOT complete.').'</FONT>').(AllowEdit()?' | <FONT COLOR=green>You can edit these grades</FONT>':' | <FONT COLOR=red>You can not edit these grades</FONT>'));

	if(AllowEdit())
	{
		if(substr($_REQUEST['mp'],0,1)!='E')
		{
			$gb_header .= "<A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=gradebook&mp=$_REQUEST[mp]>Get Gradebook Grades</A>";
			$prev_mp = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,START_DATE FROM SCHOOL_MARKING_PERIODS WHERE MP='".GetMP($_REQUEST['mp'],'MP')."' AND SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND START_DATE<'".GetMP($_REQUEST['mp'],'START_DATE')."' ORDER BY START_DATE DESC LIMIT 1"));
			$prev_mp = $prev_mp[1];
			if($prev_mp)
			{
				$gb_header .= " | <A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=grades&tab_id=$_REQUEST[tab_id]&mp=$_REQUEST[mp]&prev_mp=$prev_mp[MARKING_PERIOD_ID]>Get $prev_mp[TITLE] Grades</A>";
				$gb_header .= " | <A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=comments&tab_id=$_REQUEST[tab_id]&mp=$_REQUEST[mp]&prev_mp=$prev_mp[MARKING_PERIOD_ID]>Get $prev_mp[TITLE] Comments</A>";
			}
			$gb_header .= ' | ';
		}
		$gb_header .= "<A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=clearall&tab_id=$_REQUEST[tab_id]&mp=$_REQUEST[mp]>Clear All</A>";
	}
	DrawHeader($gb_header,$tipmessage);
}
else
{
	DrawHeader($course_title);
	DrawHeader(GetMP(UserMP()));
}

$LO_columns = array('FULL_NAME'=>_('Student'),'STUDENT_ID'=>_('Centre ID'));
if($_REQUEST['include_inactive']=='Y')
	$LO_columns += array('ACTIVE'=>_('School Status'),'ACTIVE_SCHEDULE'=>_('Course Status'));
$LO_columns += array('REPORT_CARD_GRADE'=>($does_letter_percent<0?_('Letter'):($does_letter_percent>0?_('Percent'):(Preferences('ONELINE','Gradebook')=='Y'?'<NOBR>':'')._('Letter').(Preferences('ONELINE','Gradebook')=='Y'?' ':'<BR>')._('Percent').(Preferences('ONELINE','Gradebook')=='Y'?'</NOBR>':''))));

if(substr($_REQUEST['mp'],0,1)!='E' && GetMP($_REQUEST['mp'],'DOES_COMMENTS')=='Y')
{
	foreach($commentsA_RET as $value)
		$LO_columns += array('CA'.$value['ID']=>$value['TITLE']);
	for($i=1; $i<=$max_current_commentsB; $i++)
		$LO_columns += array('CB'.$i=>sprintf(_('Comment %d'),$i));
	if(count($commentsB_select) && AllowEdit() && !isset($_REQUEST['_CENTRE_PDF']))
		$LO_columns += array('CB'.$i=>_('Add Comment'));
}
if(!$hide_non_attendance_comment || $course_RET[1]['ATTENDANCE']=='Y')
	$LO_columns += array('COMMENT'=>_('Comment'));

foreach($categories_RET as $id=>$category)
	$tabs[] = array('title'=>$category[1]['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&mp=$_REQUEST[mp]&tab_id=$id")+($category[1]['COLOR']?array('color'=>$category[1]['COLOR']):array());
$LO_options = array('yscroll'=>true,'save'=>false,'search'=>false);
if(count($categories_RET) && GetMP($_REQUEST['mp'],'DOES_COMMENTS')=='Y')
{
	$LO_options['header'] = WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&mp=$_REQUEST[mp]&tab_id=$_REQUEST[tab_id]");
	if($categories_RET[$_REQUEST['tab_id']][1]['COLOR'])
		$LO_options['header_color'] = $categories_RET[$_REQUEST['tab_id']][1]['COLOR'];
}
ListOutput($stu_RET,$LO_columns,'Student','Students',false,array(),$LO_options);
echo '<CENTER>'.SubmitButton(_('Save')).'</CENTER>';
echo "</FORM>";

function _makeLetterPercent($student_id,$column)
{	global $THIS_RET,$current_RET,$import_RET,$grades_select,$student_count,$tabindex,$grade_scale_id,$does_letter_percent;

	if($import_RET[$student_id])
	{
		$select_percent = $import_RET[$student_id][1]['GRADE_PERCENT'];
		$select_grade = $import_RET[$student_id][1]['REPORT_CARD_GRADE_ID'];
		$div = false;
	}
	else
	{
		$select_percent = $current_RET[$student_id][1]['GRADE_PERCENT'];
		$select_grade = $current_RET[$student_id][1]['REPORT_CARD_GRADE_ID'];
		$div = true;
	}

	if(!isset($_REQUEST['_CENTRE_PDF']))
	{
		$student_count++;
		$tabindex = $student_count;

		if($does_letter_percent<0)
			$return = SelectInput($select_grade,'values['.$student_id.'][grade]','',$grades_select,false,'tabindex='.$tabindex,$div);
		elseif($does_letter_percent>0)
			$return = TextInput($select_percent==''?'':$select_percent.'%',"values[$student_id][percent]",'','size=5 tabindex='.$tabindex,$div);
		else
		{
			if(AllowEdit() && $div && $select_percent!='' && $select_grade && Preferences('HIDDEN')=='Y')
				$return = '<DIV id='.$student_id.'><div onclick=\'addHTML("'.str_replace('"','\"',(Preferences('ONELINE','Gradebook')=='Y'?'<NOBR>':'').SelectInput($select_grade,'values['.$student_id.'][grade]','',$grades_select,false,'tabindex='.$tabindex,false)).(Preferences('ONELINE','Gradebook')=='Y'?' ':'<BR>').str_replace('"','\"',TextInput($select_percent!=''?$select_percent.'%':'',"values[$student_id][percent]",'','size=5 tabindex='.($tabindex+=100),false)).(Preferences('ONELINE','Gradebook')=='Y'?'</NOBR>':'').'","'.$student_id.'",true);\'><span style=\'border-bottom-style:dotted;border-bottom-width:1px;border-bottom-color:'.Preferences('TITLES').';\'>'.(Preferences('ONELINE','Gradebook')=='Y'?'<NOBR>':'').($grades_select[$select_grade]?$grades_select[$select_grade][1]:'<FONT color=red>'.$select_grade.'</FONT>').(Preferences('ONELINE','Gradebook')=='Y'?' ':'<BR>').$select_percent.'%'.(Preferences('ONELINE','Gradebook')=='Y'?'</NOBR>':'').'</span></div></DIV>';
			else
				$return = (Preferences('ONELINE','Gradebook')=='Y'?'<NOBR>':'').SelectInput($select_grade?$select_grade:($select_percent!=''?' ':''),'values['.$student_id.'][grade]','',$grades_select,false,'tabindex='.$tabindex,false).(Preferences('ONELINE','Gradebook')=='Y'?' ':'<BR>').TextInput($select_percent!=''?$select_percent.'%':($select_grade?'%':''),"values[$student_id][percent]",'','size=5 tabindex='.($tabindex+=100),false).(Preferences('ONELINE','Gradebook')=='Y'?'</NOBR>':'');
		}
	}
	else
	{
		if($does_letter_percent<0)
			$return = ($grades_select[$select_grade]?$grades_select[$select_grade][1]:'<FONT color=red>'.$select_grade.'</FONT>');
		elseif($does_letter_percent>0)
			$return = $select_percent.'%';
		else
			$return = (Preferences('ONELINE','Gradebook')=='Y'?'<NOBR>':'').($grades_select[$select_grade]?$grades_select[$select_grade][1]:'<FONT color=red>'.$select_grade.'</FONT>').(Preferences('ONELINE','Gradebook')=='Y'?' ':'<BR>').$select_percent.'%'.(Preferences('ONELINE','Gradebook')=='Y'?'</NOBR>':'');
	}

	return $return;
}

function _makeComment($value,$column)
{	global $THIS_RET,$current_RET,$import_comments_RET,$tabindex;

	if($import_comments_RET[$THIS_RET['STUDENT_ID']])
	{
		$select = $import_comments_RET[$THIS_RET['STUDENT_ID']][1]['COMMENT'];
		$div = false;
	}
	else
	{
		$select = $current_RET[$THIS_RET['STUDENT_ID']][1]['COMMENT'];
		$div = true;
	}

	if(!isset($_REQUEST['_CENTRE_PDF']))
		$return = TextInput($select,"values[$THIS_RET[STUDENT_ID]][comment]",'','maxlength=255 tabindex='.($tabindex+=100),$div);
	else
		$return = '<small>'.$select.'</small>';

	return $return;
}

function _makeCommentsA($value,$column)
{	global $THIS_RET,$current_commentsA_RET,$import_commentsA_RET,$commentsA_select,$tabindex;

	if($import_commentsA_RET[$THIS_RET['STUDENT_ID']][$value])
	{
		$select = $import_commentsA_RET[$THIS_RET['STUDENT_ID']][$value][1]['COMMENT'];
		$div = false;
	}
	else
	{
		if(!$current_commentsA_RET[$THIS_RET['STUDENT_ID']][$value][1]['COMMENT'] && !$import_commentsA_RET && AllowEdit())
		{
			$select = Preferences('COMMENT_'.$THIS_RET['CAC'.$value],'Gradebook');
			$div = false;
		}
		else
		{
			$select = $current_commentsA_RET[$THIS_RET['STUDENT_ID']][$value][1]['COMMENT'];
			$div = true;
		}
	}

	if(!isset($_REQUEST['_CENTRE_PDF']))
		$return = SelectInput($select,'values['.$THIS_RET['STUDENT_ID'].'][commentsA]['.$value.']','',$commentsA_select[$THIS_RET['CAC'.$value]],'N/A','tabindex='.($tabindex+=100),$div);
	else
		$return = $select!=' ' ? $select : 'o';

	return $return;
}
function _makeCommentsB($value,$column)
{	global $THIS_RET,$current_commentsB_RET,$import_commentsB_RET,$commentsB_RET,$max_current_commentsB,$commentsB_select,$tabindex;

	if($import_commentsB_RET[$THIS_RET['STUDENT_ID']][$value])
	{
		$select = $import_commentsB_RET[$THIS_RET['STUDENT_ID']][$value]['REPORT_CARD_COMMENT_ID'];
		$div = false;
	}
	else
	{
		$select = $current_commentsB_RET[$THIS_RET['STUDENT_ID']][$value]['REPORT_CARD_COMMENT_ID'];
		$div = true;
	}

	if(!isset($_REQUEST['_CENTRE_PDF']))
		if($value>$max_current_commentsB)
			$return = SelectInput('','values['.$THIS_RET['STUDENT_ID'].'][commentsB]['.$value.']','',$commentsB_select,'N/A','tabindex='.($tabindex+=100));
		elseif($import_commentsB_RET[$THIS_RET['STUDENT_ID']][$value] || isset($current_commentsB_RET[$THIS_RET['STUDENT_ID']][$value]))
			$return = SelectInput($select,'values['.$THIS_RET['STUDENT_ID'].'][commentsB]['.$value.']','',$commentsB_select,'N/A','tabindex='.($tabindex+=100),$div);
		else
			$return = '';
	else
		$return = '<small>'.$commentsB_RET[$select][1]['TITLE'].'</small>';

	return $return;
}
?>