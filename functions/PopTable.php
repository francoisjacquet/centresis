<?php

// DRAWS A TABLE WITH A BLUE TAB, SURROUNDING SHADOW
// REQUIRES A TITLE

function PopTable($action,$title='Search',$table_att='',$cell_padding='5')
{	global $_CENTRE;

	if($action=='header')
	{
		echo "<CENTER>
			<TABLE cellpadding=0 cellspacing=0 $table_att>";

			echo "<TR><TD align=center colspan=3>";
			if(is_array($title))
				echo WrapTabs($title,$_CENTRE['selected_tab']);
			else
				echo DrawTab($title);
			echo "</TD></TR>
			<TR></TR><TR><TD bgcolor=white>";

		// Start content table.
		echo "<TABLE cellpadding=".$cell_padding." cellspacing=0 width=100%><tr><td class=inside_cont bgcolor=white>";
	}
	elseif($action=='footer')
	{
		// Close embeded table.
		echo "</td></tr></TABLE>";

		// 2nd cell is for shadow.....
		echo "</TD>
		</TR>
		<TR>
			
		</TR></TABLE></CENTER>";
	}
	elseif($action=='none')
	{
		echo "<CENTER><TABLE cellpadding=0 cellspacing=0 $table_att><TR></TR><TR><TD class=wrapper bgcolor=white>";
		echo "<TABLE cellpadding=".$cell_padding." cellspacing=0 width=100%><tr><td bgcolor=white>";
	}
}
?>