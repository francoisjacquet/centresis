<?php
DrawHeader(ProgramTitle());

	global $_CENTRE;
	if(User('PERSON_ID')!="") :
		#User('STAFF_ID') = $_SESSION['PERSON_ID'];
		$user_staff_id = User('PERSON_ID');
	else :
		$user_staff_id = User('STAFF_ID');
	endif;

if($_REQUEST['values'] && $_POST['values'])
{
	if($_REQUEST['tab']=='password')
	{
		if(strtolower($_REQUEST['values']['new'])!=strtolower($_REQUEST['values']['verify']))
			$error = _('Your new passwords did not match.');
		else
		{
			$password_RET = DBGet(DBQuery("SELECT PASSWORD FROM staff WHERE STAFF_ID='".$user_staff_id."' AND SYEAR='".UserSyear()."'"));
			if(strtolower(DecryptPWD($password_RET[1]['PASSWORD']))!=strtolower($_REQUEST['values']['current']))
				$error = _('Your current password was incorrect.');
			else
			{
				DBQuery("UPDATE staff SET PASSWORD='".EncryptPWD($_REQUEST['values']['new'])."' WHERE STAFF_ID='".$user_staff_id."' AND SYEAR='".UserSyear()."'");
				$note = _('Your new password was saved.');
			}
		}
	}
	else
	{
		$current_RET = DBGet(DBQuery("SELECT TITLE,VALUE,PROGRAM FROM PROGRAM_USER_CONFIG WHERE USER_ID='".$user_staff_id."' AND PROGRAM IN ('Preferences','StudentFieldsSearch','StudentFieldsView','WidgetsSearch','StaffFieldsSearch','StaffFieldsView','StaffWidgetsSearch')"),array(),array('PROGRAM','TITLE'));

		if($_REQUEST['tab']=='student_listing' && $_REQUEST['values']['Preferences']['SEARCH']!='Y')
			$_REQUEST['values']['Preferences']['SEARCH'] = 'N';
		if($_REQUEST['tab']=='student_listing' && $_REQUEST['values']['Preferences']['E_CODEDS']!='Y')
			$_REQUEST['values']['Preferences']['E_CODEDS'] = 'N';
		if($_REQUEST['tab']=='student_listing' && $_REQUEST['values']['Preferences']['E_EXPORTS']!='Y')
			$_REQUEST['values']['Preferences']['E_EXPORTS'] = 'N';
		if($_REQUEST['tab']=='student_listing' && User('PROFILE')=='admin' && $_REQUEST['values']['Preferences']['DEFAULT_FAMILIES']!='Y')
			$_REQUEST['values']['Preferences']['DEFAULT_FAMILIES'] = 'N';
		if($_REQUEST['tab']=='student_listing' && User('PROFILE')=='admin' && $_REQUEST['values']['Preferences']['DEFAULT_ALL_SCHOOLS']!='Y')
			$_REQUEST['values']['Preferences']['DEFAULT_ALL_SCHOOLS'] = 'N';
		if($_REQUEST['tab']=='display_options' && $_REQUEST['values']['Preferences']['HIDE_ALERTS']!='Y')
			$_REQUEST['values']['Preferences']['HIDE_ALERTS'] = 'N';
		if($_REQUEST['tab']=='display_options' && $_REQUEST['values']['Preferences']['HIDDEN']!='Y')
			$_REQUEST['values']['Preferences']['HIDDEN'] = 'N';
		if($_REQUEST['tab']=='display_options' && $_REQUEST['values']['Preferences']['THEME']!=$current_RET['Preferences']['THEME'][1]['VALUE'])
		{
			echo '<script language=JavaScript>';
			echo 'parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;';
			echo "parent.help.location='Bottom.php?modcat=Users&modname=$_REQUEST[modname]';";
			echo '</script>';
		}
		if($_REQUEST['tab']=='student_fields' || $_REQUEST['tab']=='widgets' || $_REQUEST['tab']=='staff_fields' || $_REQUEST['tab']=='staff_widgets')
		{
			DBQuery("DELETE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".$user_staff_id."' AND PROGRAM".($_REQUEST['tab']=='student_fields'?" IN ('StudentFieldsSearch','StudentFieldsView')":($_REQUEST['tab']=='widgets'?"='WidgetsSearch'":($_REQUEST['tab']=='staff_fields'?" IN ('StaffFieldsSearch','StaffFieldsView')":"='StaffWidgetsSearch'"))));

			foreach($_REQUEST['values'] as $program=>$values)
			{
				foreach($values as $name=>$value)
				{
					if(isset($value))
						DBQuery("INSERT INTO PROGRAM_USER_CONFIG (USER_ID,PROGRAM,TITLE,VALUE) values('".$user_staff_id."','$program','$name','$value')");
				}
			}
		}
		else
		{
			foreach($_REQUEST['values'] as $program=>$values)
			{
				foreach($values as $name=>$value)
				{
					if(!$current_RET[$program][$name] && $value!='')
						DBQuery("INSERT INTO PROGRAM_USER_CONFIG (USER_ID,PROGRAM,TITLE,VALUE) values('".$user_staff_id."','$program','$name','$value')");
					elseif($value!='')
						DBQuery("UPDATE PROGRAM_USER_CONFIG SET VALUE='$value' WHERE USER_ID='".$user_staff_id."' AND PROGRAM='$program' AND TITLE='$name'");
					else
						DBQuery("DELETE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".$user_staff_id."' AND PROGRAM='$program' AND TITLE='$name'");
				}
			}
		}

		// So Preferences() will get the new values
		unset($_CENTRE['Preferences']);
	}
	unset($_REQUEST['values']);
	unset($_SESSION['_REQUEST_vars']['values']);
}

unset($_REQUEST['search_modfunc']);
unset($_SESSION['_REQUEST_vars']['search_modfunc']);

if(!$_REQUEST['modfunc'])
{
	$current_RET = DBGet(DBQuery("SELECT TITLE,VALUE,PROGRAM FROM PROGRAM_USER_CONFIG WHERE USER_ID='".$user_staff_id."' AND PROGRAM IN ('Preferences','StudentFieldsSearch','StudentFieldsView','WidgetsSearch','StaffFieldsSearch','StaffFieldsView','StaffWidgetsSearch') "),array(),array('PROGRAM','TITLE'));

	if(!$_REQUEST['tab'])
		$_REQUEST['tab'] = 'display_options';

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&amp;tab=$_REQUEST[tab] method=POST>";
	DrawHeader('','<INPUT type=submit value="'._('Save').'">');
	echo '<BR>';

	if(User('PROFILE')=='admin' || User('PROFILE')=='teacher')
	{
		$tabs = array(array('title'=>_('Display Options'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=display_options"),array('title'=>_('Student Listing'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=student_listing"),array('title'=>_('Password'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=password"),array('title'=>_('Student Fields'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=student_fields"),array('title'=>_('Widgets'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=widgets"));
		if(User('PROFILE')=='admin')
		{
			$tabs[] = array('title'=>_('User Fields'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=staff_fields");
			$tabs[] = array('title'=>_('User Widgets'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=staff_widgets");
		}
	}
	else
		$tabs = array(array('title'=>_('Display Options'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=display_options"),array('title'=>_('Password'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=password"),array('title'=>_('Student Fields'),'link'=>"Modules.php?modname=$_REQUEST[modname]&amp;tab=student_fields"));

	$_CENTRE['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&amp;tab=".$_REQUEST['tab'];
	PopTable('header',$tabs);

	echo '<fieldset>';

	if($_REQUEST['tab']=='student_listing')
	{
		echo '<TABLE>';
		echo '<TR><TD valign=top align=right><font color=gray>'._('Student Name').'</TD><TD><INPUT type=radio name=values[Preferences][NAME] value=Common'.((Preferences('NAME')=='Common')?' CHECKED':'').'>'._('Common Name').'<BR><INPUT type=radio name=values[Preferences][NAME] value=Given'.((Preferences('NAME')=='Given')?' CHECKED':'').'>'._('Given Name').'</TD></TR>';
		echo '<TR><TD valign=top align=right><font color=gray>'._('Student Sorting').'</TD><TD><INPUT type=radio name=values[Preferences][SORT] value=Name'.((Preferences('SORT')=='Name')?' CHECKED':'').'>'._('Name').'<BR><INPUT type=radio name=values[Preferences][SORT] value=Grade'.((Preferences('SORT')=='Grade')?' CHECKED':'').'>'._('Grade, Name').'</TD></TR>';
		echo '<TR><TD valign=top align=right><font color=gray>'._('File Export Type').'</TD><TD><INPUT type=radio name=values[Preferences][DELIMITER] value=Tab'.((Preferences('DELIMITER')=='Tab')?' CHECKED':'').'>'._('Tab-Delimited (Excel)').'<BR><INPUT type=radio name=values[Preferences][DELIMITER] value=CSV'.((Preferences('DELIMITER')=='CSV')?' CHECKED':'').'>CSV (OpenOffice)</TD></TR>';
		echo '<TR><TD valign=top align=right><font color=gray>'._('Date Export Format').'</TD><TD><INPUT type=radio name=values[Preferences][E_DATE] value=""'.((Preferences('E_DATE')=='')?' CHECKED':'').'>'._('Display Options Format').'<BR><INPUT type=radio name=values[Preferences][E_DATE] value=MM/DD/YYYY'.((Preferences('E_DATE')=='MM/DD/YYYY')?' CHECKED':'').'>MM/DD/YYYY</TD></TR>';
		echo '<TR><TD></TD><TD><INPUT type=checkbox name=values[Preferences][E_CODEDS] value=Y'.((Preferences('E_CODEDS')=='Y')?' CHECKED':'').'>'.sprintf(_('Export %s fields as stored value'),'<i>'._('Coded Pull-Down').'</i>').'</TD></TR>';
		echo '<TR><TD></TD><TD><INPUT type=checkbox name=values[Preferences][E_EXPORTS] value=Y'.((Preferences('E_EXPORTS')=='Y')?' CHECKED':'').'>'.sprintf(_('Export %s fields as stored value'),'<i>'._('Export Pull-Down').'</i>').'</TD></TR>';
		echo '<TR><TD><BR></TD><TD><BR></TD>';
		echo '<TR><TD></TD><TD><INPUT type=checkbox name=values[Preferences][SEARCH] value=Y'.((Preferences('SEARCH')=='Y')?' CHECKED':'').'>'._('Display student search screen').'</TD></TR>';
		if(User('PROFILE')=='admin')
		{
			echo '<TR><TD></TD><TD><INPUT type=checkbox name=values[Preferences][DEFAULT_FAMILIES] value=Y'.((Preferences('DEFAULT_FAMILIES')=='Y')?' CHECKED':'').'>'._('Group by family by default').'</TD></TR>';
			echo '<TR><TD></TD><TD><INPUT type=checkbox name=values[Preferences][DEFAULT_ALL_SCHOOLS] value=Y'.((Preferences('DEFAULT_ALL_SCHOOLS')=='Y')?' CHECKED':'').'>'._('Search all schools by default').'</TD></TR>';
		}
		echo '</TABLE>';
	}

	if($_REQUEST['tab']=='display_options')
	{
		echo '<TABLE>';
		echo '<TR><TD valign=top align=right><font color=gray>'._('Theme').'</font></TD><TD><TABLE><TR>';
		if($handle = opendir($CentrePath.'/assets/themes/'))
		{
			while(false !== ($file = readdir($handle)))
			{
				if($file != "." && $file != ".." && !in_array($file,$IgnoreFiles))
				{
					echo '<TD><INPUT type=radio name=values[Preferences][THEME] value='.$file.((Preferences('THEME')==$file)?' CHECKED':'').'>'.$file.'</TD>';
					$count++;
					if($count%3==0)
						echo '</TR><TR>';
				}
			}
			closedir($handle);
		}
		echo '</TR></TABLE></TD></TR>';
		$colors = array('#FFFFFF','#330099','#3366FF','#003333','#FF3300','#660000','#666666','#333366','#336633','purple','teal','firebrick','tan');
		echo '<TR><TD align=right><font color=gray>'._('Header Color').'</font></TD><TD><TABLE><TR>';
		foreach($colors as $color)
			echo '<TD bgcolor='.$color.'><INPUT type=radio name=values[Preferences][HEADER] value='.$color.((Preferences('HEADER')==$color)?' CHECKED':'').'></TD>';
		echo '</TR></TABLE></TD></TR>';

		$colors = array('#FFCCFF','#9999FF','#CCCCFF','#3399FF','#CCFFCC','#33FF66','#FFFFCC','#FFFF66','#FFCCCC','#CC6666','#CCCCCC','#999999');
		echo '<TR><TD align=right><font color=gray>'._('List Color').'</font></TD><TD><TABLE><TR>';
		foreach($colors as $color)
			echo '<TD bgcolor='.$color.'><INPUT type=radio name=values[Preferences][COLOR] value='.$color.((Preferences('COLOR')==$color)?' CHECKED':'').'></TD>';
		echo '</TR></TABLE></TD></TR>';

		$colors = array('#330099','#3366FF','#003333','#FF3300','#660000','#666666');
		echo '<TR><TD align=right><font color=gray>'._('Highlight Color').'</font></TD><TD><TABLE><TR>';
		foreach($colors as $color)
			echo '<TD bgcolor='.$color.'><INPUT type=radio name=values[Preferences][HIGHLIGHT] value='.$color.((Preferences('HIGHLIGHT')==$color)?' CHECKED':'').'></TD>';
		echo '</TR></TABLE></TD></TR>';

		$colors = array('#FFFFCC','gray','black','#333366');
		echo '<TR><TD align=right><font color=gray>'._('Titles Color').'</font></TD><TD><TABLE><TR>';
		foreach($colors as $color)
			echo '<TD bgcolor='.$color.'><INPUT type=radio name=values[Preferences][TITLES] value='.$color.((Preferences('TITLES')==$color)?' CHECKED':'').'></TD>';
		echo '</TR></TABLE></TD></TR>';

		/*echo '<TR><TD align=right><font color=gray>'._('Date Format').'</font></TD><TD><SELECT name=values[Preferences][MONTH]>';
		$values = array('F','M','m','n');
		foreach($values as $value)
			echo '<OPTION value='.$value.((Preferences('MONTH')==$value)?' SELECTED':'').'>'.date($value).'</OPTION>';
		echo '</SELECT>';
		echo '<SELECT name=values[Preferences][DAY]>';
		$values = array('d','j','jS');
		foreach($values as $value)
			echo '<OPTION value='.$value.((Preferences('DAY')==$value)?' SELECTED':'').'>'.date($value=='d'?'[0]j':$value).'</OPTION>';
		echo '</SELECT>';
		echo '<SELECT name=values[Preferences][YEAR]>';
		$values = array('Y','y','');
		foreach($values as $value)
			echo '<OPTION value="'.$value.'"'.((Preferences('YEAR')==$value || (!Preferences('YEAR') && !$value))?' SELECTED':'').'>'.date($value).'</OPTION>';
		echo '</SELECT>';
		echo '</TD></TR>';*/
		echo '<TR><TD></TD><TD><INPUT type=checkbox name=values[Preferences][HIDE_ALERTS] value=Y'.((Preferences('HIDE_ALERTS')=='Y')?' CHECKED':'').'>'._('Disable login alerts').'</TD></TR>';
		echo '<TR><TD></TD><TD><INPUT type=checkbox name=values[Preferences][HIDDEN] value=Y'.((Preferences('HIDDEN')=='Y')?' CHECKED':'').'>'._('Display data using hidden fields').'</TD></TR>';
		echo '</TABLE>';
	}

	if($_REQUEST['tab']=='password')
	{
		if($error)
			echo ErrorMessage(array($error));
		if($note)
			echo ErrorMessage(array($note),'note');
		echo '<TABLE><TR><TD align=right><font color=gray>Current Password</font></TD><TD><INPUT type=password name=values[current]></TD></TR>
						<TR><TD align=right><font color=gray>New Password</font></TD><TD><INPUT type=password name=values[verify]></TD></TR>
						<TR><TD align=right><font color=gray>Verify New Password</font></TD><TD><INPUT type=password name=values[new]></TD></TR></TABLE>';
	}

	if($_REQUEST['tab']=='student_fields')
	{
		if(User('PROFILE_ID'))
			$custom_fields_RET = DBGet(DBQuery("SELECT sfc.TITLE AS CATEGORY,cf.ID,cf.TITLE,'' AS SEARCH,'' AS DISPLAY FROM CUSTOM_FIELDS cf,STUDENT_FIELD_CATEGORIES sfc WHERE sfc.ID=cf.CATEGORY_ID AND (SELECT CAN_USE FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Students/Student.php&category_id='||cf.CATEGORY_ID)='Y' ORDER BY sfc.SORT_ORDER,sfc.TITLE,cf.SORT_ORDER,cf.TITLE"),array('SEARCH'=>'_make','DISPLAY'=>'_make'),array('CATEGORY'));
		else
			$custom_fields_RET = DBGet(DBQuery("SELECT sfc.TITLE AS CATEGORY,cf.ID,cf.TITLE,'' AS SEARCH,'' AS DISPLAY FROM CUSTOM_FIELDS cf,STUDENT_FIELD_CATEGORIES sfc WHERE sfc.ID=cf.CATEGORY_ID AND (SELECT CAN_USE FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Students/Student.php&category_id='||cf.CATEGORY_ID)='Y' ORDER BY sfc.SORT_ORDER,sfc.TITLE,cf.SORT_ORDER,cf.TITLE"),array('SEARCH'=>'_make','DISPLAY'=>'_make'),array('CATEGORY'));
        foreach ($custom_fields_RET as &$category_RET)
            foreach ($category_RET as &$field) {
                $field['CATEGORY'] = '<b>'.ParseMLField($field['CATEGORY']).'</b>';
                $field['TITLE']    = ParseMLField($field['TITLE']); 
            }
		$THIS_RET['ID'] = 'CONTACT_INFO';
		$custom_fields_RET[-1][1] = array('CATEGORY'=>'<B>'._('Contact Information').'</B>','ID'=>'CONTACT_INFO','TITLE'=>'<IMG SRC=assets/down_phone_button.gif width=15> '._('Contact Info Rollover'),'DISPLAY'=>_make('','DISPLAY'));
		$THIS_RET['ID'] = 'HOME_PHONE';
		$custom_fields_RET[-1][] = array('CATEGORY'=>'<B>'._('Contact Information').'</B>','ID'=>'HOME_PHONE','TITLE'=>_('Home Phone Number'),'DISPLAY'=>_make('','DISPLAY'));
		$THIS_RET['ID'] = 'GUARDIANS';
		$custom_fields_RET[-1][] = array('CATEGORY'=>'<B>'._('Contact Information').'</B>','ID'=>'GUARDIANS','TITLE'=>_('Guardians'),'DISPLAY'=>_make('','DISPLAY'));
		$THIS_RET['ID'] = 'ALL_CONTACTS';
		$custom_fields_RET[-1][] = array('CATEGORY'=>'<B>'._('Contact Information').'</B>','ID'=>'ALL_CONTACTS','TITLE'=>_('All Contacts'),'DISPLAY'=>_make('','DISPLAY'));

		$custom_fields_RET[0][1] = array('CATEGORY'=>'<B>'._('Addresses').'</B>','ID'=>'ADDRESS','TITLE'=>'None','DISPLAY'=>_makeAddress(''));
		$custom_fields_RET[0][] = array('CATEGORY'=>'<B>'._('Addresses').'</B>','ID'=>'ADDRESS','TITLE'=>'<IMG SRC=assets/house_button.gif> '._('Residence'),'DISPLAY'=>_makeAddress('RESIDENCE'));
		$custom_fields_RET[0][] = array('CATEGORY'=>'<B>'._('Addresses').'</B>','ID'=>'ADDRESS','TITLE'=>'<IMG SRC=assets/mailbox_button.gif> '._('Mailing'),'DISPLAY'=>_makeAddress('MAILING'));
		$custom_fields_RET[0][] = array('CATEGORY'=>'<B>'._('Addresses').'</B>','ID'=>'ADDRESS','TITLE'=>'<IMG SRC=assets/bus_button.gif> '._('Bus Pickup'),'DISPLAY'=>_makeAddress('BUS_PICKUP'));
		$custom_fields_RET[0][] = array('CATEGORY'=>'<B>'._('Addresses').'</B>','ID'=>'ADDRESS','TITLE'=>'<IMG SRC=assets/bus_button.gif> '._('Bus Dropoff'),'DISPLAY'=>_makeAddress('BUS_DROPOFF'));

		if(User('PROFILE')=='admin' || User('PROFILE')=='teacher')
			$columns = array('CATEGORY'=>'','TITLE'=>_('Field'),'SEARCH'=>_('Search'),'DISPLAY'=>_('Expanded View'));
		else
			$columns = array('CATEGORY'=>'','TITLE'=>_('Field'),'DISPLAY'=>_('Expanded View'));
		ListOutput($custom_fields_RET,$columns,'.','.',array(),array(array('CATEGORY')));
	}

	if($_REQUEST['tab']=='widgets')
	{
		$widgets = array();
		if($CentreModules['Students'])
			$widgets += array('calendar'=>_('Calendar'),'next_year'=>_('Next School Year'));
		if($CentreModules['Scheduling'] && User('PROFILE')=='admin')
			$widgets = array('course'=>_('Course'),'request'=>_('Request'));
		if($CentreModules['Attendance'])
			$widgets += array('absences'=>_('Days Absent'));
		if($CentreModules['Grades'])
			$widgets += array('gpa'=>_('GPA'),'class_rank'=>_('Class Rank'),'letter_grade'=>_('Letter Grade'));
		if($CentreModules['Eligibility'])
			$widgets += array('eligibility'=>_('Eligibility'),'activity'=>_('Activity'));
		if($CentreModules['Food_Service'])
			$widgets += array('fsa_balance'=>_('Food Service Balance'),'fsa_discount'=>_('Food Service Discount'),'fsa_status'=>_('Food Service Status'),'fsa_barcode'=>_('Food Service Barcode'));
		if($CentreModules['Discipline'])
			$widgets += array('discipline'=>_('Discipline'));
		if($CentreModules['Student_Billing'])
			$widgets += array('balance'=>_('Student Billing Balance'));

		$widgets_RET[0] = array();
		foreach($widgets as $widget=>$title)
		{
			$THIS_RET['ID'] = $widget;
			$widgets_RET[] = array('ID'=>$widget,'TITLE'=>$title,'WIDGET'=>_make('','WIDGET'));
		}
		unset($widgets_RET[0]);

		echo '<INPUT type=hidden name=values[WidgetsSearch]>';
		$columns = array('TITLE'=>_('Widget'),'WIDGET'=>_('Search'));
		ListOutput($widgets_RET,$columns,'.','.');
	}

	if($_REQUEST['tab']=='staff_fields' && User('PROFILE')=='admin')
	{
		if(User('PROFILE_ID'))
			$custom_fields_RET = DBGet(DBQuery("SELECT sfc.TITLE AS CATEGORY,cf.ID,cf.TITLE,'' AS STAFF_SEARCH,'' AS STAFF_DISPLAY FROM staff_FIELDS cf,STAFF_FIELD_CATEGORIES sfc WHERE sfc.ID=cf.CATEGORY_ID AND (SELECT CAN_USE FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Users/User.php&category_id='||cf.CATEGORY_ID)='Y' ORDER BY sfc.SORT_ORDER,sfc.TITLE,cf.SORT_ORDER,cf.TITLE"),array('STAFF_SEARCH'=>'_make','STAFF_DISPLAY'=>'_make'),array('CATEGORY'));
		else
			$custom_fields_RET = DBGet(DBQuery("SELECT sfc.TITLE AS CATEGORY,cf.ID,cf.TITLE,'' AS STAFF_SEARCH,'' AS STAFF_DISPLAY FROM staff_FIELDS cf,STAFF_FIELD_CATEGORIES sfc WHERE sfc.ID=cf.CATEGORY_ID AND (SELECT CAN_USE FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Users/User.php&category_id='||cf.CATEGORY_ID)='Y' ORDER BY sfc.SORT_ORDER,sfc.TITLE,cf.SORT_ORDER,cf.TITLE"),array('STAFF_SEARCH'=>'_make','STAFF_DISPLAY'=>'_make'),array('CATEGORY'));

        foreach ($custom_fields_RET as &$category_RET)
            foreach ($category_RET as &$field) {
                $field['CATEGORY'] = '<b>'.ParseMLField($field['CATEGORY']).'</b>';
                $field['TITLE']    = ParseMLField($field['TITLE']); 
            }
		echo '<INPUT type=hidden name=values[StaffFieldsSearch]><INPUT type=hidden name=values[StaffFieldsView]>';
		$columns = array('CATEGORY'=>'','TITLE'=>_('Field'),'STAFF_SEARCH'=>_('Search'),'STAFF_DISPLAY'=>_('Expanded View'));
		ListOutput($custom_fields_RET,$columns,'User Field','User Fields',array(),array(array('CATEGORY')));
	}

	if($_REQUEST['tab']=='staff_widgets' && User('PROFILE')=='admin')
	{
		$widgets = array();
		if($CentreModules['Users'])
			$widgets += array('permissions'=>_('Permissions'));
		if($CentreModules['Food_Service'])
			$widgets += array('fsa_balance'=>_('Food Service Balance'),'fsa_status'=>_('Food Service Status'),'fsa_barcode'=>_('Food Service Barcode'));

		$widgets_RET[0] = array();
		foreach($widgets as $widget=>$title)
		{
			$THIS_RET['ID'] = $widget;
			$widgets_RET[] = array('ID'=>$widget,'TITLE'=>$title,'STAFF_WIDGET'=>_make('','STAFF_WIDGET'));
		}
		unset($widgets_RET[0]);

		echo '<INPUT type=hidden name=values[StaffWidgetsSearch]>';
		$columns = array('TITLE'=>_('Widget'),'STAFF_WIDGET'=>_('Search'));
		ListOutput($widgets_RET,$columns,'.','.');
	}

	echo '</fieldset>';
	PopTable('footer');
	echo '<CENTER><INPUT type=submit value="'._('Save').'"></CENTER>';
	echo '</FORM>';
}

function _make($value,$name)
{	global $THIS_RET,$current_RET;

	switch($name)
	{
		case 'SEARCH':
			if($current_RET['StudentFieldsSearch'][$THIS_RET['ID']])
				$checked = ' checked';
			return '<INPUT type=checkbox name=values[StudentFieldsSearch]['.$THIS_RET['ID'].'] value=Y'.$checked.'>';
		break;

		case 'DISPLAY':
			if($current_RET['StudentFieldsView'][$THIS_RET['ID']])
				$checked = ' checked';
			return '<INPUT type=checkbox name=values[StudentFieldsView]['.$THIS_RET['ID'].'] value=Y'.$checked.'>';
		break;

		case 'WIDGET':
			if($current_RET['WidgetsSearch'][$THIS_RET['ID']])
				$checked = ' checked';
			return '<INPUT type=checkbox name=values[WidgetsSearch]['.$THIS_RET['ID'].'] value=Y'.$checked.'>';
		break;

		case 'STAFF_SEARCH':
			if($current_RET['StaffFieldsSearch'][$THIS_RET['ID']])
				$checked = ' checked';
			return '<INPUT type=checkbox name=values[StaffFieldsSearch]['.$THIS_RET['ID'].'] value=Y'.$checked.'>';
		break;

		case 'STAFF_DISPLAY':
			if($current_RET['StaffFieldsView'][$THIS_RET['ID']])
				$checked = ' checked';
			return '<INPUT type=checkbox name=values[StaffFieldsView]['.$THIS_RET['ID'].'] value=Y'.$checked.'>';
		break;

		case 'STAFF_WIDGET':
			if($current_RET['StaffWidgetsSearch'][$THIS_RET['ID']])
				$checked = ' checked';
			return '<INPUT type=checkbox name=values[StaffWidgetsSearch]['.$THIS_RET['ID'].'] value=Y'.$checked.'>';
		break;
	}
}

function _makeAddress($value)
{	global $current_RET;

	if($current_RET['StudentFieldsView']['ADDRESS'][1]['VALUE']==$value || (!$current_RET['StudentFieldsView']['ADDRESS'][1]['VALUE'] && $value==''))
		$checked = ' CHECKED';
	return '<INPUT type=radio name=values[StudentFieldsView][ADDRESS] value="'.$value.'"'.$checked.'>';
}
?>
