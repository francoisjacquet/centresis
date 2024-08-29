<?php
DrawHeader(ProgramTitle());
//$_CENTRE['allow_edit'] = true;

if($_REQUEST['tables'] && $_POST['tables'])
{
	$table = $_REQUEST['table'];
	foreach($_REQUEST['tables'] as $id=>$columns)
	{
		if($id!='new')
		{
			if($columns['CATEGORY_ID'] && $columns['CATEGORY_ID']!=$_REQUEST['category_id'])
				$_REQUEST['category_id'] = $columns['CATEGORY_ID'];

			$sql = "UPDATE $table SET ";

			foreach($columns as $column=>$value)
				$sql .= $column."='".str_replace("\'","''",$value)."',";
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			$go = true;
		}
		else
		{
			$sql = "INSERT INTO $table ";

			if($table=='CUSTOM_FIELDS')
			{
				if($columns['CATEGORY_ID'])
				{
					$_REQUEST['category_id'] = $columns['CATEGORY_ID'];
					unset($columns['CATEGORY_ID']);
				}

				//$id = DBGet(DBQuery("SELECT ".db_nextval('CUSTOM').' AS ID '.FROM_DUAL));
				$id = db_nextval('custom_fields');
				$fields = "CATEGORY_ID,";
				$values = "'".$_REQUEST['category_id']."',";
				$_REQUEST['id'] = $id;

				switch($columns['TYPE'])
				{
					case 'radio':
						DBQuery("ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(1)");
					break;

					case 'text':
					case 'exports':
					case 'select':
					case 'autos':
					case 'edits':
						DBQuery("ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(255)");
					break;

					case 'codeds':
						DBQuery("ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(15)");
					break;

					case 'multiple':
						DBQuery("ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(1000)");
					break;

					case 'numeric':
						DBQuery("ALTER TABLE STUDENTS ADD CUSTOM_$id NUMERIC(10,2)");
					break;

					case 'date':
						DBQuery("ALTER TABLE STUDENTS ADD CUSTOM_$id DATE");
					break;

					case 'textarea':
						DBQuery("ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(5000)");
					break;
				}
				DBQuery("CREATE INDEX CUSTOM_IND$id ON STUDENTS (CUSTOM_$id)");
				DBQuery("INSERT INTO CUSTOM (student_id) VALUES (NULL)");
			}
			elseif($table=='STUDENT_FIELD_CATEGORIES')
			{
				//$id = DBGet(DBQuery("SELECT ".db_nextval('STUDENT_FIELD_CATEGORIES').' AS ID '.FROM_DUAL));
				//$id = $id[1]['ID'];
				$id = db_nextval('STUDENT_FIELD_CATEGORIES');
//				$fields = "ID,";
//				$values = $id.",";
				$fields = "";
				$values = "";
				$_REQUEST['category_id'] = $id;
				// add to profile or permissions of user creating it
				if(User('PROFILE_ID'))
					DBQuery("INSERT INTO PROFILE_EXCEPTIONS (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('".User('PROFILE_ID')."','Students/Student.php&category_id=$id','Y','Y')");
				else
					DBQuery("INSERT INTO STAFF_EXCEPTIONS (USER_ID,MODNAME,CAN_USE,CAN_EDIT) values('".User('STAFF_ID')."','Students/Student.php&category_id=$id','Y','Y')");
			}

			$go = false;

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
		}

		if($go)
			DBQuery($sql);
	}
	unset($_REQUEST['tables']);
}

if($_REQUEST['modfunc']=='delete')
{
	if($_REQUEST['id'])
	{
		if(DeletePrompt('student field'))
		{
			$id = $_REQUEST['id'];
			DBQuery("DELETE FROM CUSTOM_FIELDS WHERE ID='$id'");
			DBQuery("ALTER TABLE STUDENTS DROP COLUMN CUSTOM_$id");
			$_REQUEST['modfunc'] = '';
			unset($_REQUEST['id']);
		}
	}
	elseif($_REQUEST['category_id'])
	{
		if(DeletePrompt('student field category and all fields in the category'))
		{
			$fields = DBGet(DBQuery("SELECT ID FROM CUSTOM_FIELDS WHERE CATEGORY_ID='$_REQUEST[category_id]'"));
			foreach($fields as $field)
			{
				DBQuery("DELETE FROM CUSTOM_FIELDS WHERE ID='$field[ID]'");
				DBQuery("ALTER TABLE STUDENTS DROP COLUMN CUSTOM_$field[ID]");
			}
			DBQuery("DELETE FROM STUDENT_FIELD_CATEGORIES WHERE ID='$_REQUEST[category_id]'");
			// remove from profiles and permissions
			DBQuery("DELETE FROM PROFILE_EXCEPTIONS WHERE MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]'");
			DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]'");
			$_REQUEST['modfunc'] = '';
			unset($_REQUEST['category_id']);
		}
	}
}

if(!$_REQUEST['modfunc'])
{
	// CATEGORIES
	$sql = "SELECT ID,TITLE,SORT_ORDER FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE";
	$QI = DBQuery($sql);
	$categories_RET = DBGet($QI);

	if(AllowEdit() && $_REQUEST['id']!='new' && $_REQUEST['category_id']!='new' && ($_REQUEST['id'] || $_REQUEST['category_id']>4))
		$delete_button = "<INPUT type=button value='"._('Delete')."' onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&category_id=$_REQUEST[category_id]&id=$_REQUEST[id]\"'>";

	// ADDING & EDITING FORM
	if($_REQUEST['id'] && $_REQUEST['id']!='new')
	{
		$sql = "SELECT CATEGORY_ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,SORT_ORDER,REQUIRED,REQUIRED,(SELECT TITLE FROM STUDENT_FIELD_CATEGORIES WHERE ID=CATEGORY_ID) AS CATEGORY_TITLE FROM CUSTOM_FIELDS WHERE ID='$_REQUEST[id]'";
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = ParseMLField($RET['CATEGORY_TITLE']).' - '.ParseMLField($RET['TITLE']);
	}
	elseif($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && $_REQUEST['id']!='new')
	{
		$sql = "SELECT TITLE,SORT_ORDER,INCLUDE,COLUMNS
				FROM STUDENT_FIELD_CATEGORIES
				WHERE ID='$_REQUEST[category_id]'";
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = ParseMLField($RET['TITLE']);
	}
	elseif($_REQUEST['id']=='new')
		$title = _('New Student Field');
	elseif($_REQUEST['category_id']=='new')
		$title = _('New Student Field Category');

	if($_REQUEST['id'])
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
		if($_REQUEST['id']!='new')
			echo "&id=$_REQUEST[id]";
		echo "&table=CUSTOM_FIELDS method=POST>";

		DrawHeader($title,$delete_button.SubmitButton(_('Save')));
		$header .= '<TABLE cellpadding=3 width=100%>';
		$header .= '<TR>';
 
		$header .= '<TD>' . MLTextInput($RET['TITLE'],'tables['.$_REQUEST['id'].'][TITLE]',_('Field Name')) . '</TD>';

		// You can't change a student field type after it has been created
		// mab - allow changing between select and autos and edits and text and exports
		if($_REQUEST['id']!='new')
		{
			if($RET['TYPE']!='select' && $RET['TYPE']!='autos' && $RET['TYPE']!='edits' && $RET['TYPE']!='text' && $RET['TYPE']!='exports')
			{
				$allow_edit = $_CENTRE['allow_edit'];
				$AllowEdit = $_CENTRE['AllowEdit'][$modname];
				$_CENTRE['allow_edit'] = false;
				$_CENTRE['AllowEdit'][$modname] = array();
				$type_options = array('select'=>_('Pull-Down'),'autos'=>_('Auto Pull-Down'),'edits'=>_('Edit Pull-Down'),'text'=>_('Text'),'radio'=>_('Checkbox'),'codeds'=>_('Coded Pull-Down'),'exports'=>_('Export Pull-Down'),'numeric'=>_('Number'),'multiple'=>_('Select Multiple from Options'),'date'=>_('Date'),'textarea'=>_('Long Text'));
			}
			else
				$type_options = array('select'=>_('Pull-Down'),'autos'=>_('Auto Pull-down'),'edits'=>_('Edit Pull-Down'),'exports'=>_('Export Pull-Down'),'text'=>_('Text'));
		}
		else
			$type_options = array('select'=>_('Pull-Down'),'autos'=>_('Auto Pull-down'),'edits'=>_('Edit Pull-Down'),'text'=>_('Text'),'radio'=>_('Checkbox'),'codeds'=>_('Coded Pull-Down'),'exports'=>_('Export Pull-Down'),'numeric'=>_('Number'),'multiple'=>_('Select Multiple from Options'),'date'=>_('Date'),'textarea'=>_('Long Text'));

		$header .= '<TD>' . SelectInput($RET['TYPE'],'tables['.$_REQUEST['id'].'][TYPE]',_('Data Type'),$type_options,false) . '</TD>';
		if($_REQUEST['id']!='new' && $RET['TYPE']!='select' && $RET['TYPE']!='autos' && $RET['TYPE']!='edits' && $RET['TYPE']!='text' && $RET['TYPE']!='exports')
		{
			$_CENTRE['allow_edit'] = $allow_edit;
			$_CENTRE['AllowEdit'][$modname] = $AllowEdit;
		}
		foreach($categories_RET as $type)
			$categories_options[$type['ID']] = ParseMLField($type['TITLE']);

		$header .= '<TD>' . MLSelectInput($RET['CATEGORY_ID']?$RET['CATEGORY_ID']:$_REQUEST['category_id'],'tables['.$_REQUEST['id'].'][CATEGORY_ID]',_('Student Field Category'),$categories_options,false) . '</TD>';

		$header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['id'].'][SORT_ORDER]',_('Sort Order'),'size=5') . '</TD>';

		$header .= '</TR><TR>';
		$colspan = 2;
		if($RET['TYPE']=='autos' || $RET['TYPE']=='edits' || $RET['TYPE']=='select' || $RET['TYPE']=='codeds' || $RET['TYPE']=='multiple' || $RET['TYPE']=='exports' || $_REQUEST['id']=='new')
		{
			$header .= '<TD colspan=2>'.TextAreaInput($RET['SELECT_OPTIONS'],'tables['.$_REQUEST['id'].'][SELECT_OPTIONS]',_('Pull-Down').'/'._('Auto Pull-Down').'/'._('Coded Pull-Down').'/'._('Select Multiple Choices').'<BR>'._('* one per line'),'rows=7 cols=40') . '</TD>';
			$colspan = 1;
		}
		$header .= '<TD valign=bottom colspan='.$colspan.'>'.TextInput($RET['DEFAULT_SELECTION'],'tables['.$_REQUEST['id'].'][DEFAULT_SELECTION]','Default').'<small><BR>'._('* for dates: YYYY-MM-DD').',<BR>&nbsp;'._('for checkboxes: Y').'</small></TD>';

		$new = ($_REQUEST['id']=='new');
		$header .= '<TD>' . CheckboxInput($RET['REQUIRED'],'tables['.$_REQUEST['id'].'][REQUIRED]',_('Required'),'',$new) . '</TD>';

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	elseif($_REQUEST['category_id'])
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&table=STUDENT_FIELD_CATEGORIES";
		if($_REQUEST['category_id']!='new')
			echo "&category_id=$_REQUEST[category_id]";
		echo " method=POST>";
		DrawHeader($title,$delete_button.SubmitButton(_('Save')));
		$header .= '<TABLE cellpadding=3 width=100%>';
		$header .= '<TR>';

		$header .= '<TD>' . MLTextInput($RET['TITLE'],'tables['.$_REQUEST['category_id'].'][TITLE]',_('Title')) . '</TD>';
		$header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['category_id'].'][SORT_ORDER]',_('Sort Order'),'size=5') . '</TD>';
		$header .= '<TD>' . TextInput($RET['COLUMNS'],'tables['.$_REQUEST['category_id'].'][COLUMNS]',_('Display Columns'),'size=5') . '</TD>';

		$new = ($_REQUEST['category_id']=='new');
		if($_REQUEST['category_id']>4 || $new)
			$header .= '<TD>' . TextInput($RET['INCLUDE'],'tables['.$_REQUEST['category_id'].'][INCLUDE]',_('Include (should be left blank for most categories)')) . '</TD>';

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	else
		$header = false;

	if($header)
	{
		DrawHeader($header);
		echo '</FORM>';
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false); //,'add'=>true);

	echo '<TABLE><TR>';

	if(count($categories_RET))
	{
		if($_REQUEST['category_id'])
		{
			foreach($categories_RET as $key=>$value)
			{
				if($value['ID']==$_REQUEST['category_id'])
					$categories_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>_('Category'),'SORT_ORDER'=>_('Sort Order'));
	$link = array();
	$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
	$link['TITLE']['variables'] = array('category_id'=>'ID');
	$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=new";

    $categories_RET = ParseMLArray($categories_RET,'TITLE');
	ListOutput($categories_RET,$columns,'Student Field Category','Student Field Categories',$link,array(),$LO_options);
	echo '</TD>';

	// FIELDS
	if($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && count($categories_RET))
	{
		$sql = "SELECT ID,TITLE,TYPE,SORT_ORDER FROM CUSTOM_FIELDS WHERE CATEGORY_ID='".$_REQUEST['category_id']."' ORDER BY SORT_ORDER,TITLE";
		$fields_RET = DBGet(DBQuery($sql),array('TYPE'=>'_makeType'));

		if(count($fields_RET))
		{
			if($_REQUEST['id'] && $_REQUEST['id']!='new')
			{
				foreach($fields_RET as $key=>$value)
				{
					if($value['ID']==$_REQUEST['id'])
						$fields_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('TITLE'=>_('Student Field'),'SORT_ORDER'=>_('Order'),'TYPE'=>_('Data Type'));
		$link = array();
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
		$link['TITLE']['variables'] = array('id'=>'ID');
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new";

        $fields_RET = ParseMLArray($fields_RET,'TITLE');
		ListOutput($fields_RET,$columns,'Student Field','Student Fields',$link,array(),$LO_options);

		echo '</TD>';
	}

	echo '</TR></TABLE>';
}

function _makeType($value,$name)
{
	$options = array('radio'=>_('Checkbox'),'text'=>_('Text'),'autos'=>_('Auto Pull-Down'),'edits'=>_('Edit Pull-Down'),'select'=>_('Pull-Down'),'codeds'=>_('Coded Pull-Down'),'exports'=>_('Export Pull-Down'),'date'=>_('Date'),'numeric'=>_('Number'),'textarea'=>_('Long Text'),'multiple'=>_('Select Multiple'));
	return $options[$value];
}
?>