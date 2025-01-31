<?php

function GetStaffList(& $extra)
{	global $profiles_RET,$_CENTRE;

	$functions = array('PROFILE'=>'makeProfile');
	switch(User('PROFILE'))
	{
		case 'admin':
		case 'teacher':
			if($_REQUEST['expanded_view']=='true')
			{
				$select = ',LAST_LOGIN';
				$extra['columns_after']['LAST_LOGIN'] = _('Last Login');
				$functions['LAST_LOGIN'] = 'makeLogin';

				$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM staff_FIELDS cf WHERE ((SELECT VALUE FROM PROGRAM_USER_CONFIG WHERE TITLE=cast(cf.ID AS CHAR) AND PROGRAM='StaffFieldsView' AND USER_ID='".User('STAFF_ID')."')='Y'".($extra['staff_fields']['view']?" OR cf.ID IN (".$extra['staff_fields']['view'].")":'').") ORDER BY cf.SORT_ORDER,cf.TITLE"));

				foreach($view_fields_RET as $field)
				{
					$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
					if($field['TYPE']=='date')
						$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
					elseif($field['TYPE']=='numeric')
						$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
					elseif($field['TYPE']=='codeds')
						$functions['CUSTOM_'.$field['ID']] = 'StaffDeCodeds';
					elseif($field['TYPE']=='exports')
						$functions['CUSTOM_'.$field['ID']] = 'StaffDeCodeds';
					$select .= ',s.CUSTOM_'.$field['ID'];
				}
				$extra['SELECT'] .= $select;
			}
			else
			{
				if(!$extra['columns_after'])
					$extra['columns_after'] = array();

				if($extra['staff_fields']['view'])
				{
					$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM staff_FIELDS cf WHERE cf.ID IN (".$extra['staff_fields']['view'].") ORDER BY cf.SORT_ORDER,cf.TITLE"));
					foreach($view_fields_RET as $field)
					{
						$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
						if($field['TYPE']=='date')
							$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
						elseif($field['TYPE']=='numeric')
							$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
						elseif($field['TYPE']=='codeds')
							$functions['CUSTOM_'.$field['ID']] = 'StaffDeCodeds';
						elseif($field['TYPE']=='exports')
							$functions['CUSTOM_'.$field['ID']] = 'StaffDeCodeds';
						$select .= ',s.CUSTOM_'.$field['ID'];
					}
					$extra['SELECT'] .= $select;
				}
			}
			if(User('PROFILE')!='admin')
			{
				$extra['WHERE'] .= " AND (s.STAFF_ID='".User('STAFF_ID')."' OR s.PROFILE='parent' AND exists(SELECT '' FROM STUDENTS_JOIN_USERS _sju,STUDENT_ENROLLMENT _sem,SCHEDULE _ss WHERE _sju.STAFF_ID=s.STAFF_ID AND _sem.STUDENT_ID=_sju.STUDENT_ID AND _sem.SYEAR='".UserSYEAR()."' AND _ss.STUDENT_ID=_sem.STUDENT_ID AND _ss.COURSE_PERIOD_ID='".UserCoursePeriod()."'";
				if($_REQUEST['include_inactive']!='Y')
					$extra['WHERE'] .= " AND _ss.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") AND ('".DBDate()."'>=_sem.START_DATE AND ('".DBDate()."'<=_sem.END_DATE OR _sem.END_DATE IS NULL)) AND ('".DBDate()."'>=_ss.START_DATE AND ('".DBDate()."'<=_ss.END_DATE OR _ss.END_DATE IS NULL))";
				$extra['WHERE'] .= "))";
			}

			$profiles_RET = DBGet(DBQuery("SELECT * FROM user_profiles"),array(),array('ID'));
			$sql = "SELECT
					CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,
					s.PROFILE,s.PROFILE_ID,s.STAFF_ID,s.SCHOOLS ".$extra['SELECT']."
				FROM
					STAFF s ".$extra['FROM']."
				WHERE
					s.SYEAR='".UserSyear()."'";
			//$sql = appendStaffSQL($sql,array('NoSearchTerms'=>$extra['NoSearchTerms']));
			if($_REQUEST['_search_all_schools']!='Y')
				$sql .= " AND (s.SCHOOLS LIKE '%,".UserSchool().",%' OR s.SCHOOLS IS NULL OR s.SCHOOLS='') ";

			$sql .= $extra['WHERE'].' ';
			//$sql .= CustomFields('where','staff',array('NoSearchTerms'=>$extra['NoSearchTerms']));
			// it would be easier to sort on full_name but postgres sometimes yields strange results
			$sql .= 'ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME';

			if ($extra['functions'])
				$functions += $extra['functions'];

			return DBGet(DBQuery($sql),$functions);
		break;
	}
}

function appendStaffSQL($sql,$extra)
{	global $_CENTRE;

	if($_REQUEST['usrid'])
	{
		$sql .= " AND s.STAFF_ID='".$_REQUEST['usrid']."'";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('User ID')).' </b></font>'.$_REQUEST['usrid'].'<BR>';
	}
	if($_REQUEST['last'])
	{
		$sql .= " AND UPPER(s.LAST_NAME) LIKE '".strtoupper($_REQUEST['last'])."%'";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Last Name starts with')).' </b></font>'.$_REQUEST['last'].'<BR>';
	}
	if($_REQUEST['first'])
	{
		$sql .= " AND UPPER(s.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%'";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('First Name starts with')).' </b></font>'.$_REQUEST['first'].'<BR>';
	}
	if($_REQUEST['profile'])
	{
		$sql .= " AND s.PROFILE='".$_REQUEST['profile']."'";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Profile')).' </b></font>'.$_REQUEST['profile'].'<BR>';
	}
	if($_REQUEST['username'])
	{
		$sql .= " AND UPPER(s.USERNAME) LIKE '".strtoupper($_REQUEST['username'])."%'";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('UserName starts with')).' </b></font>'.$_REQUEST['username'].'<BR>';
	}

	return $sql;
}

function makeProfile($value)
{	global $THIS_RET,$profiles_RET;

	if($value=='admin')
		$return = _('Administrator');
	elseif($value=='teacher')
		$return = _('Teacher');
	elseif($value=='parent')
		$return = _('Parent');
	elseif($value=='none')
		$return = _('No Access');
	else $return = $value;
	if($THIS_RET['PROFILE_ID'])
		$return .= ' / '.($profiles_RET[$THIS_RET['PROFILE_ID']]?$profiles_RET[$THIS_RET['PROFILE_ID']][1]['TITLE']:'<FONT color=red>'.$THIS_RET['PROFILE_ID'].'</FONT>');
	elseif($value!='none')
		$return .= ' w/Custom';

	return $return;
}

function makeLogin($value)
{
	if($value)
		return ProperDate(substr($value,0,10)).substr($value,10);
}
?>
