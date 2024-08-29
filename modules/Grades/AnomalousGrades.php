<?php

DrawHeader('Gradebook - '.ProgramTitle());

$max_allowed = Preferences('ANOMALOUS_MAX','Gradebook')/100;

echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeader(CheckBoxOnclick('include_all_courses')._('Include All Courses'),'',CheckBoxOnclick('include_inactive')._('Include Inactive Students'));
if(!$_REQUEST['missing'] && !$_REQUEST['negative'] && !$_REQUEST['max_allowed'])
	$_REQUEST['missing'] = $_REQUEST['negative'] = $_REQUEST['max_allowed'] = 'Y';
DrawHeader(Localize('colon',_('Include')).' '.CheckBoxOnclick('missing')._('Missing Grades').' '.CheckBoxOnclick('negative')._('Excused and Negative Grades').' '.CheckBoxOnclick('max_allowed').sprintf(_('Exceed %d%% and Extra Credit Grades'),($max_allowed*100)));
echo '</FORM>';

if($_REQUEST['student_id'])
{
	if($_REQUEST['student_id']!=$_SESSION['student_id'])
	{
		$_SESSION['student_id'] = $_REQUEST['student_id'];
		if($_REQUEST['period'] && $_REQUEST['period']!=$_SESSION['UserCoursePeriod'])
			$_SESSION['UserCoursePeriod'] = $_REQUEST['period'];
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}
}
else
{
	if($_SESSION['student_id'])
	{
		unset($_SESSION['student_id']);
		if($_REQUEST['period'] && $_REQUEST['period']!=$_SESSION['UserCoursePeriod'])
			$_SESSION['UserCoursePeriod'] = $_REQUEST['period'];
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}
}
if($_REQUEST['period'])
{
	if($_REQUEST['period']!=$_SESSION['UserCoursePeriod'])
	{
		$_SESSION['UserCoursePeriod'] = $_REQUEST['period'];
		if($_REQUEST['student_id'])
		{
			if($_REQUEST['student_id']!=$_SESSION['student_id'])
				$_SESSION['student_id'] = $_REQUEST['student_id'];
		}
		else
			unset($_SESSION['student_id']);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}
}

if(UserStudentID())
	$extra['WHERE'] = " AND s.STUDENT_ID='".UserStudentID()."'";

$extra['SELECT'] .= ",gg.POINTS,gg.COMMENT,ga.ASSIGNMENT_TYPE_ID,ga.ASSIGNMENT_ID,gt.TITLE AS TYPE_TITLE,ga.TITLE,ga.POINTS AS TOTAL_POINTS,'' AS LETTER_GRADE";
$extra['FROM'] = " JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID OR ga.COURSE_ID=cp.COURSE_ID AND ga.STAFF_ID=cp.TEACHER_ID) AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID),GRADEBOOK_ASSIGNMENT_TYPES gt";
$extra['WHERE'] .= ' AND (';
// missing
if($_REQUEST['missing'])
$extra['WHERE'] .= 'gg.POINTS IS NULL AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR CURRENT_DATE>(SELECT END_DATE FROM SCHOOL_MARKING_PERIODS WHERE MARKING_PERIOD_ID=ga.MARKING_PERIOD_ID)) OR ';
// excused or negative
if($_REQUEST['negative'])
$extra['WHERE'] .= 'gg.POINTS<0 OR ';
// greater than max percent or extra credit
if($_REQUEST['max_allowed'])
$extra['WHERE'] .= 'gg.POINTS>ga.POINTS*'.$max_allowed.' OR ';
$extra['WHERE'] .= 'FALSE) AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID';
$extra['WHERE'] .=" AND (gg.POINTS IS NOT NULL OR ga.DUE_DATE IS NULL OR ((ga.DUE_DATE>=ss.START_DATE AND (ss.END_DATE IS NULL OR ga.DUE_DATE<=ss.END_DATE)) AND (ga.DUE_DATE>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR ga.DUE_DATE<=ssm.END_DATE))))";

if($_REQUEST['include_all_courses']=='Y')
{
	$extra['SELECT'] .= ',cp.COURSE_PERIOD_ID,cp.TITLE AS COURSE_TITLE';
	$extra['all_courses'] = 'Y';
}

$extra['functions'] = array('POINTS'=>'_makePoints');
if(!UserStudentID())
	$extra['group'] = array('STUDENT_ID');
$students_RET = GetStuList($extra);
//echo '<pre>'; var_dump($students_RET); echo '</pre>';

if(UserStudentID())
{
	$columns = array('POINTS'=>_('Problem'));
	$link = array();
	$group = array();
	$singular = 'Anomalous Grade';
	$plural = 'Anomalous Grades';
}
else
{
	$columns = array('FULL_NAME'=>'Name','STUDENT_ID'=>'Centre ID','POINTS'=>'Problem');
	$link = array('FULL_NAME'=>array('link'=>"Modules.php?modname=$_REQUEST[modname]&include_all_courses=$_REQUEST[include_all_courses]&include_ianctive=$_REQUEST[include_inactive]&missing=$_REQUEST[missing]&negative=$_REQUEST[negative]&max_allowed=$_REQUEST[max_allowed]",'variables'=>array('student_id'=>'STUDENT_ID')));
	if($_REQUEST['include_all_courses']=='Y')
		$link['FULL_NAME']['variables']['period'] = 'COURSE_PERIOD_ID';
	$group = array('STUDENT_ID');
	$singular = 'Student with Anomalous Grades';
	$plural = 'Students with Anomalous Grades';
}
if($_REQUEST['include_all_courses']=='Y')
{
	$columns += array('COURSE_TITLE'=>_('Course'));
}
$columns += array('TYPE_TITLE'=>_('Category'),'TITLE'=>_('Assignment'),'COMMENT'=>_('Comment'));
if($_REQUEST['include_inactive'])
	$columns += array('ACTIVE'=>_('School Status'),'ACTIVE_SCHEDULE'=>_('Course Status'));

$modname = str_replace('AnomalousGrades','Grades',$_REQUEST['modname']);
if(AllowUse($modname))
{
	$link += array('TITLE'=>array('link'=>"Modules.php?modname=$modname&include_inactive=$_REQUEST[include_inactive]",'variables'=>array('type_id'=>'ASSIGNMENT_TYPE_ID','assignment_id'=>'ASSIGNMENT_ID','student_id'=>'STUDENT_ID')));
	if($_REQUEST['include_all_courses']=='Y')
		$link['TITLE']['variables']['period'] = 'COURSE_PERIOD_ID';
}

ListOutput($students_RET,$columns,$singular,$plural,$link,$group,array('center'=>false,'save'=>false,'search'=>false));

function _makePoints($value,$column)
{	global $THIS_RET;

	if($value=='')
		return '<FONT color=#ff0000>'._('Missing').'</FONT>';
	elseif($value=='-1')
		return '<FONT color=#00a000>'._('Excused').'</FONT>';
	elseif($value<0)
		return '<FONT color=#ff0000>'._('Negative').'</FONT>';
	elseif($THIS_RET['TOTAL_POINTS']==0)
		return '<FONT color=#0000ff>'._('Extra Credit').'</FONT>';
	return Percent($value/$THIS_RET['TOTAL_POINTS'],0);
}
?>
