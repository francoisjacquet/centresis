<?php
if($_REQUEST['modfunc']=='save')
{
	$QI = DBQuery("SELECT  
	course_subjects.title AS SUBJECT, 
	courses.title AS COURSE, 
	CONCAT(UCASE(LEFT(course_periods.title, 1)), SUBSTRING(course_periods.title, 2)) AS PERIOD, 
	CONCAT(staff.first_name, ' ', staff.last_name) AS Teacher, 
	(SELECT COUNT(*) 
		FROM ((courses c INNER JOIN (schedule sc INNER JOIN course_periods cp ON (sc.course_period_id = cp.course_period_id) AND (sc.course_id = cp.course_id) AND (sc.syear = cp.syear) AND (sc.school_id = cp.school_id)) ON c.course_id = cp.course_id) 
		INNER JOIN course_subjects cs ON c.subject_id = cs.subject_id) 
		INNER JOIN staff stf ON cp.teacher_id = stf.staff_id
		WHERE sc.end_date Is Null AND sc.syear = '".UserSyear()."' AND sc.school_id = '".UserSchool()."'
		AND cs.title = course_subjects.title
		AND c.title = courses.title
		AND cp.title = course_periods.title
		AND stf.staff_id = staff.staff_id ) AS TOTAL,
	FOUND_ROWS() AS TOTAL_STUDENTS
		FROM ((courses INNER JOIN (schedule INNER JOIN course_periods ON (schedule.course_period_id = course_periods.course_period_id) AND (schedule.course_id = course_periods.course_id) AND (schedule.syear = course_periods.syear) AND (schedule.school_id = course_periods.school_id)) ON courses.course_id = course_periods.course_id) INNER JOIN course_subjects ON courses.subject_id = course_subjects.subject_id) INNER JOIN staff ON course_periods.teacher_id = staff.staff_id
		WHERE schedule.end_date Is Null AND schedule.syear = '".UserSyear()."'
		AND schedule.school_id = '".UserSchool()."'
		GROUP BY course_subjects.title, courses.title, course_periods.title,staff.first_name, staff.last_name");
	$RET = DBGet($QI);
	if(count($RET))
	{
		echo '<BR><BR><BR>';
		unset($_CENTRE['DrawHeader']);
		$handle = PDFStart();
		echo '<!-- MEDIA SIZE 8.5x11in -->';
		DrawHeader(Config('TITLE').' - '._('Class Enrollment'));
		echo '<font face="\'lucida sans unicode\'" size=2.4 color="#000000">';
		echo '<style>.sub-header tbody tr td, .sub-header tr td { font-size:14px !important; } .sub-header tr td { line-height: 18px; } 
					 table tbody table tr:first-child td, .sub-header tbody tr:first-child td { background-color: #000000; color: #414141 !important; } </style>';

		echo '<TABLE width=100% border=0 cellpadding=3 cellspacing=0><TR><TD border=0 bgcolor=#000000 align="left"><font color="#FFFFFF">Class Enrollment</font></TD><TD border=0 bgcolor=#000000 align="right"><font color="#FFFFFF">'.GetSchool(UserSchool()).'</font></TD></TR></TABLE>';

			ListOutput($RET, array('SUBJECT'=>_('Subject'), 'COURSE'=>_('Course'), 'PERIOD'=>_('Course Period'), 'TEACHER'=>_('Teacher'), 'TOTAL'=>_('Total Enrollee')),'Result','Results',array(),array(),array('center'=>false,'print'=>false,'count'=>false,'header_color'=>'#000000'));

		foreach($RET as $key):
			$total_student += $key['TOTAL'];
		endforeach;

		echo '<BR>';
		echo '<TABLE width=100% border=0 cellpadding=10 cellspacing=0><TR><TD border=0 bgcolor=#000000 align="left"><font color="#FFFFFF">Average</font></TD><TD border=0 bgcolor=#000000 align="right"><font color="#FFFFFF"><b>'.round($total_student/COUNT($RET), 0).'</b></font></TD></TR></TABLE>';

			echo '<!-- NEW PAGE -->';
		PDFStop($handle);
	}
}


if(!$_REQUEST['modfunc'])
{
	DrawHeader(ProgramTitle());

	$LO_options = array('1`'=>true,'search'=>false,'print'=>true);
	echo '<TABLE width=92%"><TBODY><TR>';
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_CENTRE_PDF=true method=POST>";
		echo '<INPUT type=submit value="'._('Create Class Enrollment').'">';

	$QI = DBQuery("SELECT  
	course_subjects.title AS SUBJECT, 
	courses.title AS COURSE, 
	CONCAT(UCASE(LEFT(course_periods.title, 1)), SUBSTRING(course_periods.title, 2)) AS PERIOD, 
	CONCAT(staff.first_name, ' ', staff.last_name) AS Teacher, 
	(SELECT COUNT(*) 
		FROM ((courses c INNER JOIN (schedule sc INNER JOIN course_periods cp ON (sc.course_period_id = cp.course_period_id) AND (sc.course_id = cp.course_id) AND (sc.syear = cp.syear) AND (sc.school_id = cp.school_id)) ON c.course_id = cp.course_id) 
		INNER JOIN course_subjects cs ON c.subject_id = cs.subject_id) 
		INNER JOIN staff stf ON cp.teacher_id = stf.staff_id
		WHERE sc.end_date Is Null AND sc.syear = '".UserSyear()."' AND sc.school_id = '".UserSchool()."'
		AND cs.title = course_subjects.title
		AND c.title = courses.title
		AND cp.title = course_periods.title
		AND stf.staff_id = staff.staff_id ) AS TOTAL,
	FOUND_ROWS() AS TOTAL_STUDENTS
		FROM ((courses INNER JOIN (schedule INNER JOIN course_periods ON (schedule.course_period_id = course_periods.course_period_id) AND (schedule.course_id = course_periods.course_id) AND (schedule.syear = course_periods.syear) AND (schedule.school_id = course_periods.school_id)) ON courses.course_id = course_periods.course_id) INNER JOIN course_subjects ON courses.subject_id = course_subjects.subject_id) INNER JOIN staff ON course_periods.teacher_id = staff.staff_id
		WHERE schedule.end_date Is Null AND schedule.syear = '".UserSyear()."'
		AND schedule.school_id = '".UserSchool()."'
		GROUP BY course_subjects.title, courses.title, course_periods.title,staff.first_name, staff.last_name");
	$RET = DBGet($QI);

	echo '<TD valign=top>';
	ListOutput($RET,array('SUBJECT'=>_('Subject'), 'COURSE'=>_('Course'), 'PERIOD'=>_('Course Period'), 'TEACHER'=>_('Teacher'), 'TOTAL'=>_('Total Enrollee')),'Result','Results', $link, array(), $LO_options);
	echo '</TD>';

	foreach($RET as $key):
		$total_student += $key['TOTAL'];
	endforeach;
	echo '<TABLE width="92%" cellpadding="6"><tbody>
		<TR>
			<TD bgcolor="#FFFFFF" class="cont_submenu"><div style="position: relative;" id="LOx1"></div><b>Average</b><div style="position: relative;" id="LOy0"></div></TD>
		</TR>
		<TR bgcolor="#FFFFCC"><td bgcolor="#FFFFCC" class="LO_field"><font color="#000000"><b>'.round($total_student/COUNT($RET), 0).'<b></font></td></TR>';
	echo '</tbody></TABLE>';

	echo '<BR><CENTER><INPUT type=submit value="'._('Create Class Enrollment').'"></CENTER>';

}
	echo '</FORM>';
echo '</TR></TBODY></TABLE>';
echo '<style>thead { background: none !important; } input[type="submit"] { padding:10px !important; }</style>';

?>
