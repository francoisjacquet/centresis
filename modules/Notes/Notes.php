<?php
unset($_SESSION['_REQUEST_vars']['values']);unset($_SESSION['_REQUEST_vars']['modfunc']);
DrawHeader(ProgramTitle());

if(!$_REQUEST['modfunc'])
{
	$page_name = _('Admin Secured Notes');
	echo '<BR>';

	PopTable('header',$page_name);

	echo "<FIELDSET><TABLE>";

	$ret = DBGet(DBQuery("SELECT NOTE, URL FROM admin_notes"));
	for($i=1; $i<=10; $i++) {
	 if($ret[$i]['NOTE']!="") {
	   echo "<TR ALIGN=LEFT><TD colspan=3><a target='_blank' href='".$ret[$i]['URL']."' border='0'>".$ret[$i]['NOTE']."</a></TD></TR>";
	 }
	}

	echo "</TABLE></FIELDSET>";

	PopTable('footer');
}

?>