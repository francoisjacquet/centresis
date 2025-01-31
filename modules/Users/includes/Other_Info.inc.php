<?php
include_once('modules/Users/includes/functions.php');
$category_RET = DBGet(DBQuery("SELECT COLUMNS FROM staff_FIELD_CATEGORIES WHERE ID='$_REQUEST[category_id]'"));
$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED FROM staff_FIELDS WHERE CATEGORY_ID='$_REQUEST[category_id]' ORDER BY SORT_ORDER,TITLE"));

if(UserStaffID())
{
	$custom_RET = DBGet(DBQuery("SELECT * FROM staff WHERE STAFF_ID='".UserStaffID()."'"));
	$value = $custom_RET[1];
}

//echo '<pre>'; var_dump($fields_RET); echo '</pre>';
if(count($fields_RET))
	echo $separator;
echo '<TABLE cellpadding=5>';
$i = 1;
$per_row = $category_RET[1]['COLUMNS']?$category_RET[1]['COLUMNS']:'3';
foreach($fields_RET as $field)
{
    $field['TITLE'] = ParseMLField($field['TITLE']);
	//echo '<pre>'; var_dump($field); echo '</pre>';
	switch($field['TYPE'])
	{
		case 'text':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],$field['TITLE'],'');
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'autos':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'edits':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'numeric':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],$field['TITLE'],'size=5 maxlength=10');
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'date':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeDateInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'exports':
		case 'codeds':
		case 'select':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeSelectInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'multiple':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeMultipleInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'radio':
			if(($i-1)%$per_row==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeCheckboxInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%$per_row==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;
	}
}
if(($i-1)%$per_row!=0)
	echo '</TR>';
echo '</TABLE><BR>';

echo '<TABLE cellpadding=5>';
$i = 1;
foreach($fields_RET as $field)
{
    $field['TITLE'] = ParseMLField($field['TITLE']);
	if($field['TYPE']=='textarea')
	{
		if(($i-1)%2==0)
			echo '<TR>';
		echo '<TD>';
		echo _makeTextareaInput('CUSTOM_'.$field['ID'],$field['TITLE']);
		echo '</TD>';
		if($i%2==0)
			echo '</TR>';
		else
			echo '<TD width=50></TD>';
		$i++;
	}
}
if(($i-1)%2!=0)
	echo '</TR>';
echo '</TABLE>';

?>