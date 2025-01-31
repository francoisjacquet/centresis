<?php
if($_REQUEST['modfunc']=='save')
{
	$date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
	if(count($_REQUEST['month_values']))
	{
		foreach($_REQUEST['month_values'] as $field_name=>$month)
		{
			$_REQUEST['values'][$field_name] = $_REQUEST['day_values'][$field_name].'-'.$month.'-'.$_REQUEST['year_values'][$field_name];
			if(!VerifyDate($_REQUEST['values'][$field_name]))
			{
				if($_REQUEST['values'][$field_name]!='--')
					$note = '<IMG SRC=assets/warning_button.gif>'._('The date you specified is not valid, so was not used. The other data was saved.');
				unset($_REQUEST['values'][$field_name]);
			}
		}
	}

	if(count($_REQUEST['values']) && count($_REQUEST['student']))
	{
		if($_REQUEST['values']['NEXT_SCHOOL']!='')
		{
			$next_school = $_REQUEST['values']['NEXT_SCHOOL'];
			unset($_REQUEST['values']['NEXT_SCHOOL']);
		}
		if($_REQUEST['values']['CALENDAR_ID'])
		{
			$calendar = $_REQUEST['values']['CALENDAR_ID'];
			unset($_REQUEST['values']['CALENDAR_ID']);
		}

		foreach($_REQUEST['values'] as $field=>$value)
		{
			if(isset($value) && $value!='')
			{
				$update .= ','.$field."='$value'";
				$values_count++;
			}
		}

		foreach($_REQUEST['student'] as $student_id=>$yes)
		{
			if($yes=='Y')
			{
				$students .= ",'$student_id'";
				$students_count++;
			}
		}

		if($values_count && $students_count)
			DBQuery('UPDATE STUDENTS SET '.substr($update,1).' WHERE STUDENT_ID IN ('.substr($students,1).')');
		elseif($note)
			$note = substr($note,0,strpos($note,'. '));
		elseif($next_school=='' && !$calendar)
			$note = '<IMG SRC=assets/warning_button.gif>'._('No data was entered.');

		if($next_school!='')
			DBQuery("UPDATE STUDENT_ENROLLMENT SET NEXT_SCHOOL='".$next_school."' WHERE SYEAR='".UserSyear()."' AND STUDENT_ID IN (".substr($students,1).") ");
		if($calendar)
			DBQuery("UPDATE STUDENT_ENROLLMENT SET CALENDAR_ID='".$calendar."' WHERE SYEAR='".UserSyear()."' AND STUDENT_ID IN (".substr($students,1).") ");

		if(!$note)
			$note = '<IMG SRC=assets/check.gif>'._('The specified information was applied to the selected students.');
		unset($_REQUEST['modfunc']);
		unset($_REQUEST['values']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
		unset($_SESSION['_REQUEST_vars']['values']);
	}
	else
		BackPrompt(_('You must choose at least one field and one student'));
}

DrawHeader(ProgramTitle());

if(!$_REQUEST['modfunc'])
{
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",CAST (NULL AS CHAR(1)) AS CHECKBOX";

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save METHOD=POST>";
		DrawHeader('',SubmitButton(_('Save')));
		echo '<BR>';

		if($_REQUEST['category_id'])
			$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS FROM CUSTOM_FIELDS WHERE CATEGORY_ID='$_REQUEST[category_id]'"),array(),array('TYPE'));
		else
			$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS FROM CUSTOM_FIELDS"),array(),array('TYPE'));

		$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STUDENT_FIELD_CATEGORIES"));
		echo '<CENTER><TABLE bgcolor=#FFFFCC><TR><TD>';
		echo '<CENTER><SELECT name=category_id onchange="document.location.href=\''.PreparePHP_SELF($_REQUEST,array('category_id')).'&amp;category_id=\'+this.form.category_id.value;"><OPTION value="">'._('All Categories').'</OPTION>';
		foreach($categories_RET as $category)
			echo '<OPTION value='.$category['ID'].($_REQUEST['category_id']==$category['ID']?' SELECTED':'').'>'.ParseMLField($category['TITLE']).'</OPTION>';
		echo '</SELECT>';
		echo '<TABLE></CENTER><HR>';
		if(count($fields_RET['text']))
		{
			foreach($fields_RET['text'] as $field)
				echo '<TR><TD align=right><small><b>'.ParseMLField($field['TITLE']).'</b></small></TD><TD>'._makeTextInput('CUSTOM_'.$field['ID']).'</TD></TR>';
		}
		if(count($fields_RET['numeric']))
		{
			foreach($fields_RET['numeric'] as $field)
				echo '<TR><TD align=right><small><b>'.ParseMLField($field['TITLE']).'</b></small></TD><TD>'._makeTextInput('CUSTOM_'.$field['ID'],true).'</TD></TR>';
		}
		if(count($fields_RET['date']))
		{
			foreach($fields_RET['date'] as $field)
				echo '<TR><TD align=right><small><b>'.ParseMLField($field['TITLE']).'</b></small></TD><TD>'._makeDateInput('CUSTOM_'.$field['ID']).'</TD></TR>';
		}
		if(count($fields_RET['select']))
		{
			foreach($fields_RET['select'] as $field)
			{
				$select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
						if($option!='')
							$select_options[$option] = $option;
				}

				echo "<TR><TD align=right><small><b>".ParseMLField($field[TITLE])."</b></small></TD><TD>"._makeSelectInput('CUSTOM_'.$field['ID'],$select_options).'</TD></TR>';
				echo "</TD></TR>";
			}
		}
		if(count($fields_RET['codeds']))
		{
			foreach($fields_RET['codeds'] as $field)
			{
				$select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							$select_options[$option[0]] = $option[1];
					}
				}
				echo "<TR><TD align=right><small><b>".ParseMLField($field[TITLE])."</b></small></TD><TD>"._makeSelectInput('CUSTOM_'.$field['ID'],$select_options).'</TD></TR>';
				echo "</TD></TR>";
			}
		}
		if(count($fields_RET['autos']))
		{
			foreach($fields_RET['autos'] as $field)	
			{
				$select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
						if($option!='')
							$select_options[$option] = $option;
				}
				// add the 'new' option, is also the separator
				$select_options['---'] = '---';

				// add values found in current and previous year
				$options_RET = DBGet(DBQuery("SELECT DISTINCT s.CUSTOM_$field[ID],upper(s.CUSTOM_$field[ID]) AS KEY FROM STUDENTS s,STUDENT_ENROLLMENT sse WHERE sse.STUDENT_ID=s.STUDENT_ID AND (sse.SYEAR='".UserSyear()."' OR sse.SYEAR='".(UserSyear()-1)."') AND s.CUSTOM_$field[ID] IS NOT NULL ORDER BY KEY"));
				if(count($options_RET))
				{
					foreach($options_RET as $option)
						if($option['CUSTOM_'.$field['ID']]!='' && !$options[$option['CUSTOM_'.$field['ID']]])
							$select_options[$option['CUSTOM_'.$field['ID']]] = array($option['CUSTOM_'.$field['ID']],'<FONT color=blue>'.$option['CUSTOM_'.$field['ID']].'</FONT>');
				}

				echo "<TR><TD align=right><small><b>".ParseMLField($field[TITLE])."</b></small></TD><TD>"._makeSelectInput('CUSTOM_'.$field['ID'],$select_options).'</TD></TR>';
				echo "</TD></TR>";
			}
		}
		if(count($fields_RET['edits']))
			{
			foreach($fields_RET['edits'] as $field)
			{
				$select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
						if($option!='')
							$select_options[$option] = $option;
				}
				// add the 'new' option
				$select_options['---'] = '---';

				echo "<TR><TD align=right><small><b>".ParseMLField($field[TITLE])."</b></small></TD><TD>"._makeSelectInput('CUSTOM_'.$field['ID'],$select_options).'</TD></TR>';
				echo "</TD></TR>";
			}
		}
		if(count($fields_RET['exports']))
		{
			foreach($fields_RET['exports'] as $field)
			{
				$select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
					{
						$option = explode('|',$option);
						if($option[0]!='')
							$select_options[$option[0]] = $option[0];
					}
				}
				echo "<TR><TD align=right><small><b>".ParseMLField($field[TITLE])."</b></small></TD><TD>"._makeSelectInput('CUSTOM_'.$field['ID'],$select_options).'</TD></TR>';
				echo "</TD></TR>";
			}
		}
		if(count($fields_RET['textarea']))
		{
			foreach($fields_RET['textarea'] as $field)
			{
				echo '<TR><TD align=right valign=top><small><b>'.ParseMLField($field['TITLE']).'</b></small></TD>';
				echo '<TD>';
				echo _makeTextareaInput('CUSTOM_'.$field['ID']);
				echo '</TD>';
				echo '</TR>';
			}
		}
		if(!$_REQUEST['category_id'] || $_REQUEST['category_id']=='1')
		{
			echo '<TR><TD align=right valign=top><small><b>'._('Rolling Retention / Options').'</b></small></TD>';
			echo '<TD>';
			$schools_RET = DBGet(DBQuery("SELECT ID,TITLE FROM SCHOOLS WHERE ID!='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
			$options = array(UserSchool()=>_('Next grade at current school'),'0'=>_('Retain'),'-1'=>_('Do not enroll after this school year'));
			if(count($schools_RET))
			{
				foreach($schools_RET as $school)
					$options[$school['ID']] = $school['TITLE'];
			}
			echo _makeSelectInput('NEXT_SCHOOL',$options);
			echo '</TD>';
			echo '</TR>';

			echo '<TR><TD align=right valign=top><small><b>'._('Calendar').'</b></small></TD>';
			echo '<TD>';
			$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID,DEFAULT_CALENDAR,TITLE FROM attendance_calendars WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY DEFAULT_CALENDAR ASC"));
			$options = array();
			if(count($calendars_RET))
			{
				foreach($calendars_RET as $calendar)
					$options[$calendar['CALENDAR_ID']] = $calendar['TITLE'];
			}
			echo _makeSelectInput('CALENDAR_ID',$options);
			echo '</TD>';
			echo '</TR>';
		}
		echo '</TABLE>';
		echo '<BR>';

		$radio_count = count($fields_RET['radio']);
		if($radio_count)
		{
			echo '<TABLE cellpadding=5>';
			echo '<TR>';
			for($i=1;$i<=$radio_count;$i++)
			{
				echo '<TD>'._makeCheckboxInput('CUSTOM_'.$fields_RET['radio'][$i]['ID'],'<b>'.ParseMLField($fields_RET['radio'][$i]['TITLE']).'</b>').'</TD>';
				if($i%5==0 && $i!=$radio_count)
					echo '</TR><TR>';
			}
			echo '</TD></TR>';
			echo '</TABLE>';
		}
		echo '</TD></TR>';
		echo '</TABLE><BR>';
	}
	elseif($note)
		DrawHeader($note);

	//Widgets('activity');
	//Widgets('course');
	//Widgets('absences');

	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
		echo "<BR><CENTER>".SubmitButton(_('Save'))."</CENTER></FORM>";
}

function _makeChooseCheckbox($value,$title='')
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

function _makeTextInput($column,$numeric=false)
{
	if($numeric===true)
		$options = 'size=3 maxlength=11';
	else
		$options = 'size=25';

	return TextInput('','values['.$column.']','',$options);
}

function _makeTextareaInput($column,$numeric=false)
{
	return TextAreaInput('','values['.$column.']');
}

function _makeDateInput($column)
{
	return DateInput('','values['.$column.']','');
}

function _makeSelectInput($column,$options)
{
	return SelectInput('','values['.$column.']','',$options,_('N/A'),"style='max-width:250;'");
}

function _makeCheckboxInput($column,$name)
{
	return CheckboxInput('','values['.$column.']',$name,'',true);
}
?>
