<?php
Widgets('request');
Widgets('mailing_labels');
$extra['force_search'] = true;

if(!$_REQUEST['search_modfunc'] || $_CENTRE['modules_search'])
{
	DrawHeader(ProgramTitle());

	$extra['new'] = true;
	$extra['action'] .= "&_CENTRE_PDF=true";
	Search('student_id',$extra);
}
else
{
	$columns = array('COURSE_TITLE'=>'Course','WITH_FULL_NAME'=>'');

	$extra['SELECT'] .= ',c.TITLE AS COURSE_TITLE,srp.PRIORITY,srp.MARKING_PERIOD_ID,srp.WITH_TEACHER_ID,srp.NOT_TEACHER_ID,srp.WITH_PERIOD_ID,srp.NOT_PERIOD_ID';
	$extra['FROM'] .= ',COURSES c,SCHEDULE_REQUESTS srp';
	$extra['WHERE'] .= ' AND ssm.STUDENT_ID=srp.STUDENT_ID AND ssm.SYEAR=srp.SYEAR AND srp.COURSE_ID = c.COURSE_ID';
	
	$extra['functions'] += array('WITH_FULL_NAME'=>'_makeExtra');
	$extra['group'] = array('STUDENT_ID');
	if($_REQUEST['mailing_labels']=='Y')
		$extra['group'][] = 'ADDRESS_ID';	
	
	$RET = GetStuList($extra);

	if(count($RET))
	{
		$__DBINC_NO_SQLSHOW = true;
		$handle = PDFStart();
		foreach($RET as $student_id=>$courses)
		{
			if($_REQUEST['mailing_labels']=='Y')
			{
				foreach($courses as $address)
				{
					echo '<BR><BR><BR>';
					unset($_CENTRE['DrawHeader']);
					DrawHeader(Config('TITLE').' - '._('Student Requests'));
					DrawHeader($address[1]['FULL_NAME'],$address[1]['STUDENT_ID']);
					DrawHeader($address[1]['GRADE_ID']);
					DrawHeader(GetSchool(UserSchool()));
					DrawHeader(ProperDate(DBDate()));
		
					echo '<BR><BR><TABLE width=100%><TR><TD width=50> &nbsp; </TD><TD>'.$address[1]['MAILING_LABEL'].'</TD></TR></TABLE><BR>';
					
					ListOutput($address,$columns,'Request','Requests',array(),array(),array('center'=>false,'print'=>false));
					echo '<!-- NEW PAGE -->';				
				}
			}
			else
			{
				unset($_CENTRE['DrawHeader']);
				DrawHeader(Config('TITLE').' - '._('Student Requests'));
				DrawHeader($courses[1]['FULL_NAME'],$courses[1]['STUDENT_ID']);
				DrawHeader($courses[1]['GRADE_ID']);
				DrawHeader(GetSchool(UserSchool()));
				DrawHeader(ProperDate(DBDate()));
				
				ListOutput($courses,$columns,'Request','Requests',array(),array(),array('center'=>false,'print'=>false));
				echo '<!-- NEW PAGE -->';
			}
		}
		PDFStop($handle);
	}
	else
		BackPrompt(_('No Students were found.'));
}

function _makeExtra($value,$title='')
{	global $THIS_RET;

	if($THIS_RET['WITH_TEACHER_ID'])
		$return .= Localize('colon',_('With')).'&nbsp;'.GetTeacher($THIS_RET['WITH_TEACHER_ID']).'<BR>';
	if($THIS_RET['NOT_TEACHER_ID'])
		$return .= Localize('colon',_('Not With')).'&nbsp;'.GetTeacher($THIS_RET['NOT_TEACHER_ID']).'<BR>';
	if($THIS_RET['WITH_PERIOD_ID'])
		$return .= Localize('colon',_('On')).'&nbsp;'.GetPeriod($THIS_RET['WITH_PERIOD_ID']).'<BR>';
	if($THIS_RET['NOT_PERIOD_ID'])
		$return .= Localize('colon',_('Not On')).'&nbsp;'.GetPeriod($THIS_RET['NOT_PERIOD_ID']).'<BR>';
	if($THIS_RET['PRIORITY'])
		$return .= Localize('colon',_('Priority')).'&nbsp;'.$THIS_RET['PRIORITY'].'<BR>';
	if($THIS_RET['MARKING_PERIOD_ID'])
		$return .= Localize('colon',_('Marking Period')).'&nbsp;'.GetMP($THIS_RET['MARKING_PERIOD_ID']).'<BR>';

	return $return;
}

?>