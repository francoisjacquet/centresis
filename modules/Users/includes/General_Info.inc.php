<?php
echo '<TABLE width=100% border=0 cellpadding=6>';
echo '<TR>';
// IMAGE
if($_REQUEST['staff_id']!='new' && $UserPicturesPath && (($file = @fopen($picture_path=$UserPicturesPath.$staff['SYEAR'].'/'.UserStaffID().'.JPG','r')) || $staff['ROLLOVER_ID'] && ($file = @fopen($picture_path=$UserPicturesPath.($staff['SYEAR']-1).'/'.$staff['ROLLOVER_ID'].'.JPG','r'))))
{
	fclose($file);
	echo '<TD width=150><IMG SRC="'.$picture_path.'" width=150></TD><TD valign=top>';
}
else
	echo '<TD colspan=2>';

echo '<TABLE width=100% cellpadding=5><TR>';

echo '<TD>';
if(AllowEdit() && !$_REQUEST['_CENTRE_PDF'])
	if($_REQUEST['staff_id']=='new' || Preferences('HIDDEN')!='Y')
		echo '<TABLE><TR><TD>'.SelectInput($staff['TITLE'],'staff[TITLE]','Title',array('Mr'=>'Mr','Mrs'=>'Mrs','Ms'=>'Ms','Miss'=>'Miss','Dr'=>'Dr'),'').'</TD><TD>'.TextInput($staff['FIRST_NAME'],'staff[FIRST_NAME]',($staff['FIRST_NAME']==''?'<FONT color=red>':'').'First'.($staff['FIRST_NAME']==''?'</FONT>':''),'maxlength=50').'</TD><TD>'.TextInput($staff['MIDDLE_NAME'],'staff[MIDDLE_NAME]','Middle','maxlength=50').'</TD><TD>'.TextInput($staff['LAST_NAME'],'staff[LAST_NAME]',($staff['LAST_NAME']==''?'<FONT color=red>':'').'Last'.($staff['LAST_NAME']==''?'</FONT>':''),'maxlength=50').'</TD><TD>'.SelectInput($staff['NAME_SUFFIX'],'staff[NAME_SUFFIX]','Suffix',array('Jr'=>'Jr','Sr'=>'Sr','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'').'</TD></TR></TABLE>';
	else
		echo '<DIV id=user_name><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($staff['TITLE'],'staff[TITLE]','Title',array('Mr'=>'Mr','Mrs'=>'Mrs','Ms'=>'Ms','Miss'=>'Miss','Dr'=>'Dr'),'','',false)).'</TD><TD>'.str_replace('"','\"',TextInput($staff['FIRST_NAME'],'staff[FIRST_NAME]','First','maxlength=50',false)).'</TD><TD>'.str_replace('"','\"',TextInput($staff['MIDDLE_NAME'],'staff[MIDDLE_NAME]','Middle','size=3 maxlength=50',false)).'</TD><TD>'.str_replace('"','\"',TextInput($staff['LAST_NAME'],'staff[LAST_NAME]','Last','maxlength=50',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($staff['NAME_SUFFIX'],'staff[NAME_SUFFIX]','Suffix',array('Jr'=>'Jr','Sr'=>'Sr','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'','',false)).'</TD></TR></TABLE>","user_name",true);\'><span style=\'border-bottom-style:dotted;border-bottom-width:1px;border-bottom-color:'.Preferences('TITLES').';\'>'.$staff['TITLE'].' '.$staff['FIRST_NAME'].' '.$staff['MIDDLE_NAME'].' '.$staff['LAST_NAME'].' '.$staff['NAME_SUFFIX'].'</span></div></DIV><small><FONT color='.Preferences('TITLES').'>Name</FONT></small>';
else
	echo ($staff['TITLE']!=''||$staff['FIRST_NAME']!=''||$staff['MIDDLE_NAME']!=''||$staff['LAST_NAME']!=''||$staff['NAME_SUFFIX']!=''?$staff['TITLE'].' '.$staff['FIRST_NAME'].' '.$staff['MIDDLE_NAME'].' '.$staff['LAST_NAME'].' '.$staff['NAME_SUFFIX']:'-').'<BR><small><FONT color='.Preferences('TITLES').'>Name</FONT></small>';
echo '</TD>';

echo '<TD colspan=1>';
echo NoInput($staff['STAFF_ID'],'Centre ID');
echo '</TD>';

echo '<TD colspan=1>';
echo NoInput($staff['ROLLOVER_ID'],'Last Year Centre ID');
echo '</TD>';

echo '</TR><TR>';

echo '<TD>';
echo TextInput($staff['USERNAME'],'staff[USERNAME]','Username','size=12 maxlength=100');
echo '</TD>';

echo '<TD>';
//echo TextInput($staff['PASSWORD'],'staff[PASSWORD]','Password','size=12 maxlength=100');
echo TextInput(array($staff['PASSWORD'],str_repeat('*',strlen($staff['PASSWORD']))),'staff[PASSWORD]',($staff['USERNAME']&&!$staff['PASSWORD']?'<FONT color=red>':'').'Password'.($staff['USERNAME']&&!$staff['PASSWORD']?'</FONT>':''),'size=12 maxlength=100');
echo '</TD>';

echo '<TD>';
echo NoInput(makeLogin($staff['LAST_LOGIN']),'Last Login');
echo '</TD>';

echo '</TR></TABLE>';
echo '</TD></TR></TABLE>';

echo '<HR>';

echo '<TABLE border=0 cellpadding=6 width=100%>';
if(basename($_SERVER['PHP_SELF'])!='index.php')
{
	echo '<TR>';

	echo '<TD>';
	echo '<TABLE><TR><TD>';
	unset($options);
	$options = array('admin'=>'Administrator','teacher'=>'Teacher','parent'=>'Parent','none'=>'No Access');
	echo SelectInput($staff['PROFILE'],'staff[PROFILE]',(!$staff['PROFILE']?'<FONT color=red>':'').'User Profile'.(!$staff['PROFILE']?'</FONT>':''),$options);

	echo '</TD></TR><TR><TD>';

	unset($profiles);
	if($_REQUEST['staff_id']!='new')
	{
		$profiles_RET = DBGet(DBQuery("SELECT ID,TITLE FROM user_profiles WHERE PROFILE='$staff[PROFILE]' ORDER BY ID"));
		foreach($profiles_RET as $profile)
			$profiles[$profile['ID']] = $profile['TITLE'];
		$na = 'Custom';
	}
	else
		$na = 'Default';
	echo SelectInput($staff['PROFILE_ID'],'staff[PROFILE_ID]','Permissions',$profiles,$na);
	echo '</TD></TR></TABLE>';
	echo '</TD>';

	echo '<TD>';
	$sql = "SELECT ID,TITLE FROM SCHOOLS WHERE SYEAR='".UserSyear()."'";
	$QI = DBQuery($sql);
	$schools_RET = DBGet($QI);
	unset($options);
	if(count($schools_RET))
	{
		$i = 0;
		echo '<TABLE><TR>';
		foreach($schools_RET as $value)
		{
			if($i%3==0)
				echo '</TR><TR>';
			echo '<TD>'.CheckboxInput(((strpos($staff['SCHOOLS'],','.$value['ID'].',')!==false)?'Y':''),'staff[SCHOOLS]['.$value['ID'].']','','',true,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').$value['TITLE'].'</TD>';
			$i++;
		}
		echo '</TR></TABLE>';
		echo '<small><FONT color='.Preferences('TITLES').'>Schools</FONT></small>';
	}
	//echo SelectInput($staff['SCHOOL_ID'],'staff[SCHOOL_ID]','School',$options,'All Schools');
	echo '</TD><TD>';
	echo '</TD>';
	echo '</TR>';
}
echo '<TR>';
echo '<TD>';
echo TextInput($staff['EMAIL'],'staff[EMAIL]','Email Address','size=12 maxlength=100');
echo '</TD>';
echo '<TD>';
echo TextInput($staff['PHONE'],'staff[PHONE]','Phone Number','size=12 maxlength=100');
echo '</TD>';
echo '</TR>';
echo '</TABLE>';

$_REQUEST['category_id'] = '1';
include('modules/Users/includes/Other_Info.inc.php');
?>
