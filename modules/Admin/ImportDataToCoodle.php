<?php
DrawHeader(ProgramTitle());

if($_REQUEST['values'] && $_POST['values'])
{
	foreach($_REQUEST['values'] as $id=>$columns)
	{
		$go = 0;
		if($id!='new')
		{
			$sql = "UPDATE DISCIPLINE_CATEGORIES SET ";

			foreach($columns as $column=>$value) {
				if($column=='ENTRY_DATE'):
					$sql .= $column."='".str_replace("\'","''",date("Y-m-d", strtotime($value)))."',";
				else:
					$sql .= $column."='".str_replace("\'","''",$value)."',";
				endif;
			}
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			$go = true;
			$sql1 = $sql2 = '';
		}
		else
		{
			//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('DISCIPLINE_CATEGORIES_SEQ').' AS ID'.FROM_DUAL));
			$id = db_nextval('DISCIPLINE_CATEGORIES');
			$sql = "INSERT INTO DISCIPLINE_CATEGORIES ";

			$fields = "ID,COLUMN_ID,SYEAR,SCHOOL_ID,";
			$values = "'".$id."','".$id."','".UserSyear()."','".UserSchool()."',";

			if($columns['TITLE'])
			{
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

				switch($columns['TYPE'])
				{
					case 'checkbox':
						$sql1 = "ALTER TABLE DISCIPLINE_REFERRALS ADD CATEGORY_$id VARCHAR(1)";
					break;

					case 'text':
					case 'multiple_radio':
					case 'multiple_checkbox':
					case 'select':
						$sql1 = "ALTER TABLE DISCIPLINE_REFERRALS ADD CATEGORY_$id VARCHAR(1000)";
					break;

					case 'numeric':
						$sql1 = "ALTER TABLE DISCIPLINE_REFERRALS ADD CATEGORY_$id NUMERIC(10,2)";
					break;

					case 'date':
						$sql1 = "ALTER TABLE DISCIPLINE_REFERRALS ADD CATEGORY_$id DATE";
					break;

					case 'textarea':
						$sql1 = "ALTER TABLE DISCIPLINE_REFERRALS ADD CATEGORY_$id VARCHAR(5000)";
					break;
				}
				$sql2 = "CREATE INDEX DISCIPLINE_REFERRALS_IND$id ON DISCIPLINE_REFERRALS (CATEGORY_$id)";
			}
		}

		if($go)
		{
			DBQuery($sql);
			if($sql1)
			{
				DBQuery($sql1);
				DBQuery($sql2);
			}
		}
	}
	unset($_REQUEST['values']);
	unset($_SESSION['_REQUEST_vars']['values']);
}

if($_REQUEST['modfunc']=='delete')
{
	if(DeletePrompt('category'))
	{
		$id = $_REQUEST['id'];
		$column_id = DBGet(DBQuery("SELECT COLUMN_ID FROM DISCIPLINE_CATEGORIES WHERE ID='$id'"));
		$column_id = $column_id[1]['COLUMN_ID'];
		DBQuery("DELETE FROM DISCIPLINE_CATEGORIES WHERE ID='$id'");
		$count = DBGet(DBQuery("SELECT count(*) AS COUNT FROM DISCIPLINE_CATEGORIES WHERE COLUMN_ID='$column_id'"));
		$count = $count[1]['COUNT'];
		if($count==0)
			DBQuery("ALTER TABLE DISCIPLINE_REFERRALS DROP COLUMN CATEGORY_$column_id");
		unset($_REQUEST['modfunc']);
		unset($_REQUEST['id']);
	}
}

if(!$_REQUEST['modfunc'])
{
	$sql = "SELECT ID,TITLE,SORT_ORDER,TYPE,OPTIONS FROM DISCIPLINE_CATEGORIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER";
	$QI = DBQuery($sql);
	$referrals_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInput','TYPE'=>'_makeType','OPTIONS'=>'_makeTextAreaInput'));

	$columns = array('TITLE'=>'Title','SORT_ORDER'=>'Sort Order','TYPE'=>'Data Type','OPTIONS'=>'Options');
	$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SORT_ORDER'=>_makeTextInput('','SORT_ORDER'),'OPTIONS'=>_makeTextAreaInput('','OPTIONS'),'TYPE'=>_makeType('','TYPE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=delete";
	$link['remove']['variables'] = array('id'=>'ID');

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
	DrawHeader('','<INPUT type=submit value=Save>');
	ListOutput($referrals_RET,$columns,'Referral Form Category','Referral Form Categories',$link);
	echo '<CENTER><INPUT type=submit value=Save></CENTER>';
	echo '</FORM>';
}

function _makeType($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	$new_options = array('checkbox'=>'Checkbox','text'=>'Text','multiple_checkbox'=>'Select Multiple from Options','multiple_radio'=>'Select One from Options','select'=>'Pull-Down','date'=>'Date','numeric'=>'Number','textarea'=>'Long Text');
	$options = array('text'=>'Text','multiple_checkbox'=>'Select Multiple from Options','multiple_radio'=>'Select One from Options','select'=>'Pull-Down');

	if($value=='date' || $value=='numeric' || $value=='checkbox' || $value=='textarea')
		return $new_options[$value];
	else
		return SelectInput($value,'values['.$id.']['.$name.']','',(($id=='new')?$new_options:$options),false);
}

function _makeTextInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name!='TITLE')
		$extra = 'size=5 maxlength=2';
	if($name=='SORT_ORDER')
		$comment = '<!-- '.$value.' -->';

	return $comment.TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeTextAreaInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($id=='new' || $THIS_RET['TYPE']=='multiple_checkbox' || $THIS_RET['TYPE']=='multiple_radio' || $THIS_RET['TYPE']=='select')
		return TextAreaInput(str_replace('"','\"',$value),'values['.$id.']['.$name.']');
	else
		return 'N/A';
}
?>
