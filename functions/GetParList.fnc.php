<?php

function GetParList(& $extra)
{	global $contacts_RET,$view_other_RET,$_CENTRE;

		$functions = array();

	if($extra['functions'])
		$functions += $extra['functions'];

		$extra['DATE'] = DBDate();

	if($_REQUEST['expanded_view']=='true')
	{
		if(!$extra['columns_after'])
			$extra['columns_after'] = array();

		$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM CUSTOM_FIELDS cf WHERE ((SELECT VALUE FROM PROGRAM_USER_CONFIG WHERE TITLE=cast(cf.ID AS CHAR) AND PROGRAM='StudentFieldsView' AND USER_ID='".User('STAFF_ID')."')='Y'".($extra['student_fields']['view']?" OR cf.ID IN (".$extra['student_fields']['view'].")":'').") ORDER BY cf.SORT_ORDER,cf.TITLE"));
		$view_address_RET = DBGet(DBQuery("SELECT VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE='ADDRESS' AND USER_ID='".User('STAFF_ID')."'"));
		$view_address_RET = $view_address_RET[1]['VALUE'];
		$view_other_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE IN ('CONTACT_INFO','HOME_PHONE','GUARDIANS','ALL_CONTACTS') AND USER_ID='".User('STAFF_ID')."'"),array(),array('TITLE'));

		if(!count($view_fields_RET) && !isset($view_address_RET) && !isset($view_other_RET['CONTACT_INFO']))
		{
			$extra['columns_after'] = array('CONTACT_INFO'=>'<IMG SRC=assets/down_phone_button.gif border=0>','CUSTOM_200000000'=>'Gender','CUSTOM_200000001'=>'Ethnicity','ADDRESS'=>'Mailing Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode') + $extra['columns_after'];
			$select = ',ssm.STUDENT_ID AS CONTACT_INFO,s.CUSTOM_200000000,s.CUSTOM_200000001,coalesce(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,coalesce(a.MAIL_CITY,a.CITY) AS CITY,coalesce(a.MAIL_STATE,a.STATE) AS STATE,coalesce(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE ';
			$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.RESIDENCE='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
			$functions['CONTACT_INFO'] = 'makeContactInfo';
			$RET = DBGet(DBQuery("SELECT ID,TYPE FROM CUSTOM_FIELDS WHERE ID IN ('200000000','200000001')"),array(),array('ID'));
			// if gender and ethnicity are converted to codeds or exports type
			if($RET['200000000'][1]['TYPE']=='codeds' || $RET['200000000'][1]['TYPE']=='exports')
				$functions['CUSTOM_200000000'] = 'DeCodeds';
			if($RET['200000001'][1]['TYPE']=='codeds' || $RET['200000001'][1]['TYPE']=='exports')
				$functions['CUSTOM_200000001'] = 'DeCodeds';
			$extra['singular'] = 'Student Address';
			$extra['plural'] = 'Student Addresses';

			$extra2['NoSearchTerms'] = true;
			$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID ';
			$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
			$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
			$extra2['ORDER_BY'] .= 'sjp.CUSTODY';
			$extra2['group'] = array('STUDENT_ID','PERSON_ID');
		}
		else
		{
			foreach($view_fields_RET as $field)
			{
				$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
				if($field['TYPE']=='date')
					$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
				elseif($field['TYPE']=='numeric')
					$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
				elseif($field['TYPE']=='codeds')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				elseif($field['TYPE']=='exports')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				$select .= ',s.CUSTOM_'.$field['ID'];
			}
			if($view_address_RET)
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.".$view_address_RET."='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$extra['columns_after'] += array('ADDRESS'=>ucwords(strtolower(str_replace('_',' ',$view_address_RET))).' Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode');
				if($view_address_RET!='MAILING')
					$select .= ",a.ADDRESS_ID,a.ADDRESS,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS";
				else
					$select .= ",a.ADDRESS_ID,coalesce(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,coalesce(a.MAIL_CITY,a.CITY) AS CITY,coalesce(a.MAIL_STATE,a.STATE) AS STATE,coalesce(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS ";
				$extra['singular'] = 'Student Address';
				$extra['plural'] = 'Student Addresses';

				if($view_other_RET['HOME_PHONE'][1]['VALUE']=='Y')
				{
					$functions['PHONE'] = 'makePhone';
					$extra['columns_after']['PHONE'] = _('Home Phone');
				}
				if($view_other_RET['GUARDIANS'][1]['VALUE']=='Y' || $view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
				{
					$functions['PARENTS'] = 'makeParents';
					if($view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
						$extra['columns_after']['PARENTS'] = _('Contacts');
					else
						$extra['columns_after']['PARENTS'] = _('Guardians');
				}
			}
			elseif($_REQUEST['addr'] || $extra['addr'])
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$distinct = 'DISTINCT ';
			}
		}
		$extra['SELECT'] .= $select;
	}
	else
	{
		if($extra['student_fields']['view'])
		{
			if(!$extra['columns_after'])
				$extra['columns_after'] = array();

			$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM CUSTOM_FIELDS cf WHERE cf.ID IN (".$extra['student_fields']['view'].") ORDER BY cf.SORT_ORDER,cf.TITLE"));
			foreach($view_fields_RET as $field)
			{
				$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
				if($field['TYPE']=='date')
					$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
				elseif($field['TYPE']=='numeric')
					$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
				elseif($field['TYPE']=='codeds')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				elseif($field['TYPE']=='exports')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				$select .= ',s.CUSTOM_'.$field['ID'];
			}
			$extra['SELECT'] .= $select;
		}
		if($_REQUEST['addr'] || $extra['addr'])
		{
			$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
			$distinct = 'DISTINCT ';
		}
	}

	switch(User('PROFILE'))
	{
		case 'admin':
			$sql = 'SELECT ';
			//$sql = 'SELECT '.$distinct;
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(p.LAST_NAME,', ',p.FIRST_NAME) AS FULL_NAME, p.PERSON_ID, ";
				else
					$sql .= "CONCAT(p.LAST_NAME,', ',p.FIRST_NAME) AS FULL_NAME, p.PERSON_ID, ";
				$sql .='p.LAST_NAME,p.FIRST_NAME '.$extra['SELECT'];
			}

			$sql .= " FROM PEOPLE p";
			$sql .= " ".$extra['FROM']." WHERE TRUE";
		break;

		case 'teacher':
			$sql = 'SELECT ';
			//$sql = 'SELECT '.$distinct;
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.CUSTOM_200000002,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',coalesce(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
				if($_REQUEST['include_inactive']=='Y')
				{
					$sql .= ','.db_case(array("('".date("Y-m-d", strtotime($extra['DATE']))."'>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR '".date("Y-m-d", strtotime($extra['DATE']))."'<=ssm.END_DATE))",'TRUE',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE';
					$sql .= ','.db_case(array("('".date("Y-m-d", strtotime($extra['DATE']))."'>=ss.START_DATE AND (ss.END_DATE IS NULL OR '".date("Y-m-d", strtotime($extra['DATE']))."'<=ss.END_DATE)) AND ss.MARKING_PERIOD_ID IN (".GetAllMP($extra['MPTable'],$extra['MP']).")",'TRUE',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE_SCHEDULE';
				}
			}

			$sql .= " FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."'";
			if($_REQUEST['include_inactive']=='Y')
				$sql .= " AND ss.START_DATE=(SELECT START_DATE FROM SCHEDULE WHERE STUDENT_ID=s.STUDENT_ID AND SYEAR=ss.SYEAR AND COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID ORDER BY START_DATE DESC LIMIT 1)";
			else
				$sql .= " AND ss.MARKING_PERIOD_ID IN (".GetAllMP($extra['MPTable'],$extra['MP']).") AND ('".date("Y-m-d", strtotime($extra['DATE']))."'>=ss.START_DATE AND ('".date("Y-m-d", strtotime($extra['DATE']))."'<=ss.END_DATE OR ss.END_DATE IS NULL))";

			$sql .= ") JOIN COURSE_PERIODS cp ON (cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ".($extra['all_courses']=='Y'?"cp.TEACHER_ID='".User('STAFF_ID')."'":"cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'").")
				JOIN STUDENT_ENROLLMENT ssm ON (ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=ss.SYEAR AND ssm.SCHOOL_ID='".UserSchool()."'";

			if($_REQUEST['include_inactive']=='Y')
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR ORDER BY START_DATE DESC LIMIT 1)";
			else
				$sql .= " AND ('".date("Y-m-d", strtotime($extra['DATE']))."'>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR '".date("Y-m-d", strtotime($extra['DATE']))."'<=ssm.END_DATE))";
			$sql .= ")".$extra['FROM']." WHERE TRUE";

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
			{
				$extra['columns_after']['ACTIVE'] = _('School Status');
				$extra['columns_after']['ACTIVE_SCHEDULE'] = _('Course Status');
			}
			//$extra['GROUP'] = "s.STUDENT_ID";
		break;

		case 'parent':
		case 'student':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.CUSTOM_200000002,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',coalesce(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
			}
			$sql .= " FROM STUDENTS s JOIN STUDENT_ENROLLMENT ssm ON (ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."'
					AND ('".date("Y-m-d", strtotime($extra['DATE']))."'>=ssm.START_DATE AND (ssm.END_DATE IS NULL OR '".date("Y-m-d", strtotime($extra['DATE']))."'<=ssm.END_DATE)) AND s.STUDENT_ID".($extra['ASSOCIATED']?" IN (SELECT STUDENT_ID FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".$extra['ASSOCIATED']."')":"='".UserStudentID()."'");
			$sql .= ")".$extra['FROM']." WHERE TRUE";
			//$extra['GROUP'] = "s.STUDENT_ID";
		break;
		default:
			exit(_('Error'));
	}

	//$sql = appendSQL($sql,array('NoSearchTerms'=>$extra['NoSearchTerms']));

	$sql .= ' '.$extra['WHERE'].' ';

	if($extra['GROUP'])
		$sql .= ' GROUP BY '.$extra['GROUP'];

	if(!$extra['ORDER_BY'] && !$extra['SELECT_ONLY'])
	{
		$sql .= ' ORDER BY ';
		// it would be easier to sort on full_name but postgres sometimes yields strange results
		if(Preferences('NAME')=='Common')
			$sql .= 'p.LAST_NAME,p.FIRST_NAME';
		else
			$sql .= 'p.LAST_NAME,p.FIRST_NAME';
		$sql .= $extra['ORDER'];
	}
	elseif($extra['ORDER_BY'])
		$sql .= ' ORDER BY '.$extra['ORDER_BY'];

	if($extra['DEBUG']===true)
		echo '<!--'.$sql.'-->';

	return DBGet(DBQuery($sql),$functions,$extra['group']);
}


function appendParentSQL($sql,$extra)
{	global $_CENTRE;

	if($_REQUEST['last'])
	{
		$sql .= " AND LOWER(p.LAST_NAME) LIKE '".strtolower($_REQUEST['last'])."%'";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Last Name starts with')).' </b></font>'.$_REQUEST['last'].'<BR>';
	}
	if($_REQUEST['first'])
	{
		$sql .= " AND LOWER(p.FIRST_NAME) LIKE '".strtolower($_REQUEST['first'])."%'";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('First Name starts with')).' </b></font>'.$_REQUEST['first'].'<BR>';
	}

	return $sql;
}
?>
