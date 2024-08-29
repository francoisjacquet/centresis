<?php
unset($_SESSION['_REQUEST_vars']['values']);unset($_SESSION['_REQUEST_vars']['modfunc']);
DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='update' && ($_REQUEST['button']==_('Save') || $_REQUEST['button']==''))
{
	if(($_REQUEST['note'] && $_POST['note']) && User('PROFILE')=='admin')
	{
		DBQuery("TRUNCATE admin_notes");
		  foreach($_REQUEST['note'] as $key=>$value):
		  	if($value['text']!="" && $value['url']!="") :
			  $note_text = "INSERT INTO admin_notes (NOTE, URL, STAFF_ID, USERNAME, DATETIME) values('".$value['text']."', '".$value['url']."', '".User('STAFF_ID')."', '".User('USERNAME')."', CURRENT_TIMESTAMP)";
			  DBQuery($note_text);
			  unset($note_text);
			endif;
		  endforeach;
			
		unset($_REQUEST['note']);
	}
	$_REQUEST['modfunc'] = '';
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(!$_REQUEST['modfunc'] && User('PROFILE')=='admin')
{
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	DrawHeader('',SubmitButton(_('Save')));
	echo '<BR>';

	$page_name = _('Admin Notes');

	PopTable('header',$page_name);

	echo "<FIELDSET><TABLE>";

	$ret = DBGet(DBQuery("SELECT NOTE, URL FROM admin_notes")); 
	for($i=1; $i<=10; $i++) {
	 if($ret[$i]['NOTE']!="") {
	  echo "<input type='hidden' name='note[".$i."][text]' value=\"".$ret[$i]['NOTE']."\">";
	  echo "<TR ALIGN=LEFT style='display:block;'><TD colspan=3>".MakeTextInput($ret[$i]['NOTE'],'note['.$i.'][text]',_('Anchor Text'),' size=150 style=padding:4px;width:300px;')."</TD></TR>";
	  echo "<input type='hidden' name='note[".$i."][url]' value=\"".$ret[$i]['URL']."\">";
	  echo "<TR ALIGN=LEFT style='margin-bottom: 14px; display: block; border-bottom: 1px dotted #CCC; padding-bottom: 5px;'><TD colspan=3>".MakeTextInput($ret[$i]['URL'],'note['.$i.'][url]',_('URL'),' size=150 style=padding:4px;width:300px;')."</TD></TR>";
	 }
	 else {
	  echo "<TR ALIGN=LEFT style='display:block;'><TD colspan=3>".MakeTextInput($notes['NOTE'],'note['.$i.'][text]',_('Anchor Text'),' size=150 style=padding:4px;width:300px;')."</TD></TR>";
	  echo "<TR ALIGN=LEFT style='margin-bottom: 14px; display: block; border-bottom: 1px dotted #CCC; padding-bottom: 5px;'><TD colspan=3>".MakeTextInput($notes['URL'],'note['.$i.'][url]',_('URL'),' size=150 style=padding:4px;width:300px;')."</TD></TR>";
	 }
	}


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