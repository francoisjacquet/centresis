<?php
// example:
//
//	if(($dp=DeletePrompt('Title')))
//		// OK
//		DBQuery("DELETE FROM BOK WHERE id='$_REQUEST[benchmark_id]'");
//	elseif($dp==false)
//		// Cancel
//

function DeletePromptX($title,$action='Delete')
{
	$PHP_tmp_SELF = PreparePHP_SELF($_REQUEST,array('delete_ok','delete_cancel'));

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Confirm'.(!substr(' ',' '.$action)?$action:''));
		echo "<CENTER><h4>Are You Sure You Want to $action that $title?</h4><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=submit name=delete_ok value=OK><INPUT type=submit name=delete_cancel value=Cancel></FORM></CENTER>";
		PopTable('footer');
		return '';
	}
	if($_REQUEST['delete_ok'])
	{
		unset($_REQUEST['delete_ok']);
		unset($_REQUEST['modfunc']);
		return true;
	}
	unset($_REQUEST['delete_cancel']);
	unset($_REQUEST['modfunc']);
	return false;
}
?>