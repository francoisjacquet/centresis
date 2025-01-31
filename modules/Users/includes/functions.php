<?php
// OTHER INFO
function _makeTextInput($column,$name,$size,$request='staff')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && $value[$column]=='' ? array('<FONT color=red>','</FONT>') : array('','');
	}

	if($field['TYPE']=='numeric')
		$value[$column] = str_replace('.00','',$value[$column]);

	return TextInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$size,$div);
}

function _makeDateInput($column,$name,$request='staff')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && $value[$column]=='' ? array('<FONT color=red>','</FONT>') : array('','');
	}

	return DateInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$div);
}

function _makeSelectInput($column,$name,$request='staff')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && $value[$column]=='' ? array('<FONT color=red>','</FONT>') : array('','');
	}

	$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
	$select_options = explode("\r",$field['SELECT_OPTIONS']);
	if(count($select_options))
	{
		foreach($select_options as $option)
			if($field['TYPE']=='codeds')
			{
				$option = explode('|',$option);
				if($option[0]!='' && $option[1]!='')
					$options[$option[0]] = $option[1];
			}
			elseif($field['TYPE']=='exports')
			{
				$option = explode('|',$option);
				if($option[0]!='')
					$options[$option[0]] = $option[0];
			}
			else
				$options[$option] = $option;
	}

	$extra = 'style="max-width:250;"';
	return SelectInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$options,'N/A',$extra,$div);
}

function _makeAutoSelectInput($column,$name,$request='staff')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && ($value[$column]=='' || $value[$column]=='---') ? array('<FONT color=red>','</FONT>') : array('','');
	}

	// build the select list...
	// get the standard selects
	if($field['SELECT_OPTIONS'])
	{
		$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
		$select_options = explode("\r",$field['SELECT_OPTIONS']);
	}
	else
		$select_options = array();
	if(count($select_options))
	{
		foreach($select_options as $option)
			if($option!='')
				$options[$option] = $option;
	}
	// add the 'new' option, is also the separator
	$options['---'] = '---';

	if($field['TYPE']=='autos' && AllowEdit()) // we don't really need the select list if we can't edit anyway
	{
		// add values found in current and previous year
		$options_RET = DBGet(DBQuery("SELECT DISTINCT s.CUSTOM_$field[ID],upper(s.CUSTOM_$field[ID]) AS KEY FROM staff s WHERE (s.SYEAR='".UserSyear()."' OR s.SYEAR='".(UserSyear()-1)."') AND s.CUSTOM_$field[ID] IS NOT NULL ORDER BY KEY"));
		if(count($options_RET))
		{
			foreach($options_RET as $option)
				if($option['CUSTOM_'.$field['ID']]!='' && !$options[$option['CUSTOM_'.$field['ID']]])
					$options[$option['CUSTOM_'.$field['ID']]] = array($option['CUSTOM_'.$field['ID']],'<FONT color=blue>'.$option['CUSTOM_'.$field['ID']].'</FONT>');
		}
	}
	// make sure the current value is in the list
	if($value[$column]!='' && !$options[$value[$column]])
		$options[$value[$column]] = array($value[$column],'<FONT color='.($field['TYPE']=='autos'?'blue':'green').'>'.$value[$column].'</FONT>');

	if($value[$column]!='---' && count($options)>1)
	{
		$extra = 'style="max-width:250;"';
		return SelectInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$options,'N/A',$extra,$div);
	}
	else
		return TextInput($value[$column]=='---'?array('---','<FONT color=red>---</FONT>'):''.$value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$size,$div);
}

function _makeCheckboxInput($column,$name,$request='staff')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
	}
	else
		$div = true;

	return CheckboxInput($value[$column],$request.'['.$column.']',$name,'',($_REQUEST['student_id']=='new'));
}

function _makeTextareaInput($column,$name,$request='staff')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
	}
	else
		$div = true;

	return TextAreaInput($value[$column],$request.'['.$column.']',$name,'',$div);
}

function _makeMultipleInput($column,$name,$request='staff')
{	global $value,$field,$_CENTRE;

	if(AllowEdit() && !$_REQUEST['_CENTRE_PDF'])
	{
		$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
		$select_options = explode("\r",$field['SELECT_OPTIONS']);
		if(count($select_options))
		{
			foreach($select_options as $option)
				$options[$option] = $option;
		}

		if($value[$column]!='')
			echo '<DIV id=\'div'.$request."[".$column.']\'><div onclick=\'javascript:addHTML("';
		echo '<TABLE border=0 cellpadding=3>';
		if(count($options)>12)
		{
			echo '<TR><TD colspan=2>';
			echo '<small><FONT color='.Preferences('TITLES').'>'.($value[$column]!=''?str_replace(array("'",'"'),array('&#39;','\"'),$name):$name).'</FONT></small>';
			if($value[$column]!='')
				echo '<TABLE width=100% height=7 style=\"border:1;border-style: solid solid none solid;\"><TR><TD></TD></TR></TABLE>';
			else
				echo '<TABLE width=100% height=7 style="border:1;border-style: solid solid none solid;"><TR><TD></TD></TR></TABLE>';

			echo '</TD></TR>';
		}
		echo '<TR>';
		$i = 0;
		foreach($options as $option)
		{
			if($i%2==0)
				echo '</TR><TR>';
			if($value[$column]!='')
				echo '<TD><INPUT type=checkbox name='.$request.'['.$column.'][] value=\"'.str_replace(array("'",'"'),array('&#39;','&rdquo;'),$option).'\"'.(strpos($value[$column],'||'.$option.'||')!==false?' CHECKED':'').'><small>'.str_replace(array("'",'"'),array('&#39;','\"'),$option).'</small></TD>';
			else
				echo '<TD><INPUT type=checkbox name='.$request.'['.$column.'][] value="'.str_replace('"','&quot;',$option).'"><small>'.$option.'</small></TD>';
			$i++;
		}
		echo '</TR><TR><TD colspan=2>';
		if($value[$column]!='')
			echo '<TABLE width=100% height=7 style=\"border:1;border-style: none solid solid solid;\"><TR><TD></TD></TR></TABLE>';
		else
			echo '<TABLE width=100% height=7 style="border:1;border-style: none solid solid solid;"><TR><TD></TD></TR></TABLE>';

		echo '</TD></TR></TABLE>';
		if($value[$column]!='')
		{
			echo '","div'.$request.'['.$column.']",true);\' >';
			echo '<span style=\'border-bottom-style:dotted;border-bottom-width:1px;border-bottom-color:'.Preferences('TITLES').';\'>'.($value[$column]!=''?str_replace('||',', ',substr($value[$column],2,-2)):'-').'</span>';
			echo '</div></DIV>';
		}
	}
	else
		echo ($value[$column]!=''?str_replace('||',', ',substr($value[$column],2,-2)):'-').'<BR>';

	echo '<small><FONT color='.Preferences('TITLES').'>'.$name.'</FONT></small>';
}
?>