<?php

function DrawHeader($left='',$right='',$center='')
{	global $_CENTRE;

	if(!isset($_CENTRE['DrawHeader']))
	{
		$_CENTRE['DrawHeader'] = 'bgcolor='.Preferences('HEADER');
		if($_CENTRE['HeaderIcon'])
			$left = '<IMG src=assets/icons/'.$_CENTRE['HeaderIcon'].' height=20 border=0 align=absmiddle> '.$left;
	}

	if($_CENTRE['DrawHeader'] == 'bgcolor='.Preferences('HEADER'))
	{
		$attribute = 'B';
		$font_color = '#FFFFFF';
		$font_custom_css = (Preferences('HEADER')!="#FFFFFF") ? '<style>.sub-header tbody tr td font b { color:#FFF;text-shadow:0 1px 0 #333; }.sub-header table tbody tr td b { color:#464646; text-shadow: 0 1px 0 #FFFFFF; }</style>' : '';
	}
	else
	{
		$attribute = 'FONT size=-1';
		$font_color = '#000000';
	}

	echo $font_custom_css.'<TABLE class="sub-header" width=100% border=0 cellpadding=0 cellspacing=0><TR>';
	if($left)
		echo '<TD '.$_CENTRE['DrawHeader'].' align=left>&nbsp;<font color='.$font_color.'><'.$attribute.'>'.$left.'</'.substr($attribute,0,4).'></font></TD>';
	if($center)
		echo '<TD '.$_CENTRE['DrawHeader'].' align=center><font color='.$font_color.'><'.$attribute.'>'.$center.'</'.$attribute.'></font></TD>';
	if($right)
		echo '<TD '.$_CENTRE['DrawHeader'].' align=right><font color='.$font_color.'><'.$attribute.'>'.$right.'</'.substr($attribute,0,4).'></font></TD>';
	echo '</TR></TABLE>';

	if($_CENTRE['DrawHeader'] == 'bgcolor='.Preferences('HEADER'))
		$_CENTRE['DrawHeader'] = 'bgcolor=#FFFFFF style="border-bottom:1px dotted #767676;"';
	else
		$_CENTRE['DrawHeader'] = 'bgcolor=#F0F0F1 style="border-bottom:1px dotted #767676;"';
}
?>
