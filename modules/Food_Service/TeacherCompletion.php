<?php

if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
else
{
	$_REQUEST['day_date'] = date('d');
	$_REQUEST['month_date'] = strtoupper(date('M'));
	$_REQUEST['year_date'] = date('y');
	$date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date'];
}

DrawHeader(ProgramTitle());

$day = date('D',strtotime($date));
switch($day)
{
	case 'Sun':
		$day = 'U';
	break;
	case 'Thu':
		$day = 'H';
	break;
	default:
		$day = substr($day,0,1);
	break;
}

$QI = DBQuery("SELECT sp.PERIOD_ID,sp.TITLE FROM SCHOOL_PERIODS sp WHERE sp.SCHOOL_ID='".UserSchool()."' AND sp.SYEAR='".UserSyear()."' AND EXISTS (SELECT '' FROM COURSE_PERIODS WHERE SYEAR=sp.SYEAR AND PERIOD_ID=sp.PERIOD_ID AND DOES_FS_COUNTS='Y') ORDER BY sp.SORT_ORDER");
$periods_RET = DBGet($QI);

$period_select =  "<SELECT name=period><OPTION value=''>All</OPTION>";
foreach($periods_RET as $period)
	$period_select .= "<OPTION value=$period[PERIOD_ID]".(($_REQUEST['period']==$period['PERIOD_ID'])?' SELECTED':'').">".$period['TITLE']."</OPTION>";
$period_select .= "</SELECT>";

$sql = "SELECT CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,sp.TITLE,cp.PERIOD_ID,s.STAFF_ID
		FROM staff s,COURSE_PERIODS cp,SCHOOL_PERIODS sp
		WHERE
			sp.PERIOD_ID = cp.PERIOD_ID
			AND cp.TEACHER_ID=s.STAFF_ID AND cp.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).")
			AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND s.PROFILE='teacher'
			AND cp.DOES_FS_COUNTS='Y' ".(($_REQUEST['period'])?" AND cp.PERIOD_ID='$_REQUEST[period]'":'')."
			AND position('$day' in cp.DAYS)>0";

$RET = DBGet(DBQuery($sql),array(),array('STAFF_ID','PERIOD_ID'));

$menus_RET = DBGet(DBQuery('SELECT MENU_ID,TITLE FROM FOOD_SERVICE_MENUS WHERE SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'),array(),array('MENU_ID'));
if(!$_REQUEST['menu_id'])
{
	if(!$_SESSION['FSA_menu_id'])
		if(count($menus_RET))
			$_REQUEST['menu_id'] = $_SESSION['FSA_menu_id'] = key($menus_RET);
		else
			ErrorMessage(array('There are no menus yet setup.'),'fatal');
	else
		$_REQUEST['menu_id'] = $_SESSION['FSA_menu_id'];
	unset($_SESSION['FSA_sale']);
}
else
	$_SESSION['FSA_menu_id'] = $_REQUEST['menu_id'];

$totals = array(array());
if(count($RET))
{
	foreach($RET as $staff_id=>$periods)
	{
		$i++;
		$staff_RET[$i]['FULL_NAME'] = $periods[key($periods)][1]['FULL_NAME'];
		foreach($periods as $period_id=>$period)
		{
			//$sql = 'SELECT (SELECT DESCRIPTION FROM FOOD_SERVICE_LUNCH_ITEMS WHERE ITEM_ID=ac.ITEM_ID) AS DESCRIPTION,(SELECT SORT_ORDER FROM FOOD_SERVICE_MENU_ITEMS WHERE ITEM_ID=ac.ITEM_ID AND MENU_ID=\''.$_REQUEST['menu_id'].'\') AS SORT_ORDER,ac.SHORT_NAME,ac.COUNT FROM FOOD_SERVICE_COMPLETED ac WHERE ac.STAFF_ID=\''.$staff_id.'\' AND ac.SCHOOL_DATE=\''.$date.'\' AND ac.PERIOD_ID=\''.$period_id.'\' ORDER BY SORT_ORDER';
			$sql = 'SELECT fsi.DESCRIPTION,fsi.SHORT_NAME,ac.COUNT FROM FOOD_SERVICE_COMPLETED ac,FOOD_SERVICE_ITEMS fsi WHERE ac.STAFF_ID=\''.$staff_id.'\' AND ac.SCHOOL_DATE=\''.$date.'\' AND ac.PERIOD_ID=\''.$period_id.'\' AND ac.MENU_ID=\''.$_REQUEST['menu_id'].'\' AND fsi.ITEM_ID=ac.ITEM_ID ORDER BY fsi.SORT_ORDER';
			$items_RET = DBGet(DBQuery($sql));
			if ($items_RET) {
				$color = 'FFFFFF';

				$staff_RET[$i][$period_id] = '<TABLE bgcolor=#'.$color.'><TR>';
				foreach($items_RET as $item) {
					$staff_RET[$i][$period_id] .= '<TD bgcolor=#'.$color.'><font class=LO_field>'.($item['COUNT'] ? $item['COUNT'] : '0').'<BR><small>'.$item['DESCRIPTION'].'</small></font></TD>';
					if($color=='FFFFFF')
						$color = 'F0F0F0';
					else
						$color = 'FFFFFF';
					if ($totals[$item['SHORT_NAME']])
						$totals[$item['SHORT_NAME']]['COUNT'] += $item['COUNT'];
					else
						$totals+= array($item['SHORT_NAME']=>array('DESCRIPTION'=>$item['DESCRIPTION'],'COUNT'=>$item['COUNT']));
				}
				$staff_RET[$i][$period_id] .= '</TR></TABLE>';
			}
			else
				$staff_RET[$i][$period_id] = '<IMG SRC=assets/x.gif>';
		}
	}
}

$columns = array('FULL_NAME'=>'Teacher');
if(!$_REQUEST['period'])
{
	foreach($periods_RET as $period)
		$columns[$period['PERIOD_ID']] = $period['TITLE'];
}

echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeader(PrepareDate(strtoupper(date("Y-m-d",strtotime($date))),'_date').' : '.$period_select.' : <INPUT type=submit value=Go>');
echo '</FORM>';

echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=add&menu_id=$_REQUEST[menu_id] method=POST>";
if(count($menus_RET)>1)
{
	$tabs = array();
	foreach($menus_RET as $id=>$menu)
		$tabs[] = array('title'=>$menu[1]['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&menu_id=$id");

	echo '<BR>';
	echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&menu_id=$_REQUEST[menu_id]").'</CENTER>';
}

echo '<TABLE width=100%><TR><TD>';
$singular = sprintf(_('Teacher who takes %s counts'),$menus_RET[$_REQUEST['menu_id']][1]['TITLE']);
$plural = sprintf(_('Teachers who take %s counts'),$menus_RET[$_REQUEST['menu_id']][1]['TITLE']);
ListOutput($staff_RET,$columns,$singular,$plural);
echo '</TD></TR>';

$totals = array_values($totals);
unset($totals[0]);
echo '<TR><TD>';
ListOutput($totals,array('DESCRIPTION'=>_('Item'),'COUNT'=>_('Total Count')),'Item Total','Item Totals');
echo '</TD></TR></TABLE>';

echo '</FORM>';

?>
