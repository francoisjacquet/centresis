<?php
if($_REQUEST['month_values'] && $_POST['month_values'])
{
	foreach($_REQUEST['month_values'] as $id=>$columns)
	{
		foreach($columns as $column=>$value)
		{
			$_REQUEST['values'][$id][$column] = date("Y-m-d", strtotime($_REQUEST['day_values'][$id][$column].'-'.$value.'-'.$_REQUEST['year_values'][$id][$column]));
			if($_REQUEST['values'][$id][$column]=='--')
				$_REQUEST['values'][$id][$column] = '';
		}
	}
	$_POST['values'] = $_REQUEST['values'];
}

if($_REQUEST['values'] && $_POST['values'])
{
	foreach($_REQUEST['values'] as $id=>$columns)
	{	
		if($id!='new')
		{
			$sql = "UPDATE ELIGIBILITY_ACTIVITIES SET ";
							
			foreach($columns as $column=>$value)
			{
				$sql .= $column."='".str_replace("\'","''",$value)."',";
			}
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			DBQuery($sql);
		}
		else
		{
			$sql = "INSERT INTO ELIGIBILITY_ACTIVITIES ";

			$fields = 'ID,SCHOOL_ID,SYEAR,';
			$values = db_nextval('ELIGIBILITY_ACTIVITIES').",'".UserSchool()."','".UserSyear()."',";

			$go = 0;
			foreach($columns as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",$value)."',";
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			if($go)
				DBQuery($sql);
		}
	}
}

DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='remove')
{
	if(DeletePrompt('activity'))
	{
		DBQuery("DELETE FROM ELIGIBILITY_ACTIVITIES WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['modfunc']!='remove')
{
	$sql = "SELECT ID,TITLE,START_DATE,END_DATE FROM ELIGIBILITY_ACTIVITIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY TITLE";
	$QI = DBQuery($sql);
	$activities_RET = DBGet($QI,array('TITLE'=>'makeTextInput','START_DATE'=>'makeDateInput','END_DATE'=>'makeDateInput'));
	
	$columns = array('TITLE'=>_('Title'),'START_DATE'=>_('Begins'),'END_DATE'=>_('Ends'));
	$link['add']['html'] = array('TITLE'=>makeTextInput('','TITLE'),'START_DATE'=>makeDateInput('','START_DATE'),'END_DATE'=>makeDateInput('','END_DATE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'ID');
	
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	DrawHeader('',SubmitButton('Save'));
	ListOutput($activities_RET,$columns,'Activity','Activities',$link);
	echo '<CENTER>'.SubmitButton('Save').'</CENTER>';
	echo '</FORM>';
}

function makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	return TextInput($value,'values['.$id.']['.$name.']');
}

function makeDateInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	return DateInput($value,'values['.$id.']['.$name.']');
}

?>