<?php
if($_REQUEST['table']=='')
	$_REQUEST['table'] = '0';

if($_REQUEST['values'] && $_POST['values'])
{
	foreach($_REQUEST['values'] as $id=>$columns)
	{
		if($columns['DEFAULT_CODE']=='Y')
			DBQuery("UPDATE ATTENDANCE_CODES SET DEFAULT_CODE=NULL WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='".$_REQUEST['table']."'");

		if($id!='new')
		{
			if($_REQUEST['table']!='new')
				$sql = "UPDATE ATTENDANCE_CODES SET ";
			else
				$sql = "UPDATE ATTENDANCE_CODE_CATEGORIES SET ";

			foreach($columns as $column=>$value)
				$sql .= $column."='".str_replace("\'","''",$value)."',";

			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			DBQuery($sql);
		}
		else
		{
			if($_REQUEST['table']!='new')
			{
				$sql = "INSERT INTO ATTENDANCE_CODES ";
				$fields = 'ID,SCHOOL_ID,SYEAR,TABLE_NAME,';
				$values = db_nextval('ATTENDANCE_CODES').",'".UserSchool()."','".UserSyear()."','".$_REQUEST['table']."',";
			}
			else
			{
				$sql = "INSERT INTO ATTENDANCE_CODE_CATEGORIES ";
				$fields = 'ID,SCHOOL_ID,SYEAR,';
				$values = db_nextval('ATTENDANCE_CODE_CATEGORIES').",'".UserSchool()."','".UserSyear()."',";
			}

			$go = false;
			foreach($columns as $column=>$value)
			{
				if(isset($value) && $value!='')
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
	unset($_REQUEST['modfunc']);
}

DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='remove')
{
	if($_REQUEST['table']!='new')
	{
		if(DeletePrompt('attendance code'))
		{
			DBQuery("DELETE FROM attendance_codes WHERE ID='$_REQUEST[id]'");
			unset($_REQUEST['modfunc']);
		}
	}
	else
	{
		if(DeletePrompt('category'))
		{
			DBQuery("DELETE FROM attendance_code_categories WHERE ID='$_REQUEST[id]'");
			DBQuery("DELETE FROM attendance_codes WHERE TABLE_NAME='$_REQUEST[id]'");
			DBQuery("UPDATE COURSE_PERIODS SET DOES_ATTENDANCE=replace(DOES_ATTENDANCE,',$_REQUEST[id],',',') WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("UPDATE COURSE_PERIODS SET DOES_ATTENDANCE=NULL WHERE DOES_ATTENDANCE=',' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			unset($_REQUEST['modfunc']);
		}
	}
}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['table']!=='new')
	{
		$sql = "SELECT ID,TITLE,SHORT_NAME,TYPE,DEFAULT_CODE,STATE_CODE,SORT_ORDER FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='".$_REQUEST['table']."' ORDER BY SORT_ORDER,TITLE";
		$QI = DBQuery($sql);
		$attendance_codes_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInput','TYPE'=>'_makeSelectInput','STATE_CODE'=>'_makeSelectInput','DEFAULT_CODE'=>'_makeCheckBoxInput'));
	}

	$tabs = array(array('title'=>_('Attendance'),'link'=>"Modules.php?modname=$_REQUEST[modname]&table=0"));
	$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM attendance_code_categories WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	foreach($categories_RET as $category)
		$tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&table=".$category['ID']);

	if($_REQUEST['table']!='new')
	{
		$sql = "SELECT ID,TITLE,SHORT_NAME,TYPE,DEFAULT_CODE,STATE_CODE,SORT_ORDER FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='".$_REQUEST['table']."' ORDER BY SORT_ORDER,TITLE";
		$functions = array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInput','TYPE'=>'_makeSelectInput','DEFAULT_CODE'=>'_makeCheckBoxInput');
		$LO_columns = array('TITLE'=>_('Title'),'SHORT_NAME'=>_('Short Name'),'SORT_ORDER'=>_('Sort Order'),'TYPE'=>_('Type'),'DEFAULT_CODE'=>_('Default for Teacher'));
		if($_REQUEST['table']=='0')
		{
			$functions['STATE_CODE'] = '_makeSelectInput';
			$LO_columns['STATE_CODE'] = _('State Code');
		}

		$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>_makeTextInput('','SORT_ORDER'),'TYPE'=>_makeSelectInput('','TYPE'),'DEFAULT_CODE'=>_makeCheckBoxInput('','DEFAULT_CODE'));
		if($_REQUEST['table']=='0')
			$link['add']['html']['STATE_CODE'] = _makeSelectInput('','STATE_CODE');
		$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&table=$_REQUEST[table]";
		$link['remove']['variables'] = array('id'=>'ID');

		$tabs[] = array('title'=>button('add'),'link'=>"Modules.php?modname=$_REQUEST[modname]&table=new");
	}
	else
	{
		$sql = "SELECT ID,TITLE,SORT_ORDER FROM attendance_code_categories WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER,TITLE";
		$functions = array('TITLE'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInput');
		$LO_columns = array('TITLE'=>'Title','SORT_ORDER'=>'Sort Order');

		$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SORT_ORDER'=>_makeTextInput('','SORT_ORDER'));
		$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&table=new";
		$link['remove']['variables'] = array('id'=>'ID');

		$tabs[] = array('title'=>button('white_add'),'link'=>"Modules.php?modname=$_REQUEST[modname]&table=new");
	}
	$LO_ret = DBGet(DBQuery($sql),$functions);

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&table=$_REQUEST[table] method=POST>";
	DrawHeader('',SubmitButton(_('Save')));
	echo '<BR>';

	echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table]").'</CENTER>';
	ListOutput($LO_ret,$LO_columns,'.','.',$link,array(),array('count'=>false,'download'=>false,'search'=>false));

	echo '<CENTER>'.SubmitButton(_('Save')).'</CENTER>';
	echo '</FORM>';
}

function _makeTextInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name=='SHORT_NAME' || $name=='SORT_ORDER')
		$extra = 'size=5 maxlength=10';

	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeSelectInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name=='TYPE')
		$options = array('teacher'=>_('Teacher & Office'),'official'=>_('Office Only'));
	elseif($name='STATE_CODE')
		$options = array('P'=>_('Present'),'A'=>_('Absent'),'H'=>_('Half'));

	return SelectInput($value,'values['.$id.']['.$name.']','',$options);
}

function _makeCheckBoxInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
	{
		$id = 'new';
		$new = true;
	}

	return CheckBoxInput($value,'values['.$id.']['.$name.']','','',$new);
}
?>