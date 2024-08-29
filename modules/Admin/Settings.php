<?php
unset($_SESSION['_REQUEST_vars']['values']);unset($_SESSION['_REQUEST_vars']['modfunc']);
DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='update' && ($_REQUEST['button']==_('Save') || $_REQUEST['button']==''))
{
	if($_REQUEST['sitedesc'] && $_POST['sitedesc'] && User('PROFILE')=='admin')
	{
		DBQuery("DELETE FROM CONFIG WHERE TITLE = 'sitedesc'");
		$sitedesc = "INSERT INTO CONFIG (TITLE,DESCRIPTION) values('sitedesc','".$_REQUEST['sitedesc']."')";
		DBQuery($sitedesc);
		unset($_REQUEST['new_logo']);
	}
	$_REQUEST['modfunc'] = '';
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(!$_REQUEST['modfunc'] && User('PROFILE')=='admin')
{
	DrawHeader('',SubmitButton(_('Save')));
	if(!$_REQUEST['new_logo']) :
		$logodata = DBGet(DBQuery("SELECT TITLE, DESCRIPTION FROM config WHERE title = 'sitelogo'"));
		$logodata = $logodata[1];
		$page_name = _('Logo URL Settings');
	else:
		$page_name = _('Set a Logo');
	endif;

	echo '<script type="text/javascript" src="modules/Admin/includes/uploadify/jquery-1.7.2.min.js"></script>';
	echo '<script type="text/javascript" src="modules/Admin/includes/uploadify/jquery.uploadify.min.js"></script>';
	echo '<link rel="stylesheet" type="text/css" href="modules/Admin/includes/uploadify/uploadify.css" />';
	echo "<FORM METHOD='POST' ACTION='Modules.php?modname=".$_REQUEST['modname']."&modfunc=update&new_school=$_REQUEST[new_school]'>";
	echo '<BR>';
	PopTable('header',$page_name);

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	echo "<FIELDSET><TABLE>";

	echo "<TR ALIGN=LEFT><TD colspan=3>".UploadInput($logodata['TITLE'],'sitelogo',(!$logodata['TITLE']?'<FONT color=red>':'')._('Logo URL').(!$logodata['TITLE']?'</FONT>':''),'maxlength=150')."</TD></TR>";

	if($logodata["DESCRIPTION"]!="") 
		echo "<TR ALIGN=LEFT><TD colspan=3>".IMGInput($logodata['DESCRIPTION'],'',_('Current Logo'),' readonly="readonly"')."<br /><br /></TD></TR>";

	$sitedata = DBGet(DBQuery("SELECT TITLE, DESCRIPTION FROM config WHERE title = 'sitedesc'"));
	$sitedata = $sitedata[1];

	echo "<TR ALIGN=LEFT><TD colspan=3>".MakeTextInput($sitedata['DESCRIPTION'],'sitedesc',_('Site Description'),' size="150" style="padding:4px;width:300px;"')."</TD></TR>";

	echo "</TABLE></FIELDSET>";
	echo '<CENTER>'.SubmitButton(_('Save')).'</CENTER>';
	echo '</FORM>';
	PopTable('footer');
}

function makeTextInput($value,$name,$desc,$extra)
{	global $THIS_RET;
	return TextInput($value,$name,$desc,$extra);
}
?>