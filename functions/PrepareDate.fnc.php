<?php

// SEND PrepareDate a name prefix, and a date in oracle format 'd-M-y' as the selected date to have returned a date selection series
// of pull-down menus
// For the default to be Not Specified, send a date of 00-000-00 or send nothing
// The date pull-downs will create three variables, monthtitle, daytitle, yeartitle
// The third parameter (booleen) specifies whether Not Specified should be allowed as an option

function PrepareDate($date,$title='',$allow_na=true,$options='')
{	global $_CENTRE;

	if($options=='')
		$options = array();
	if(!$options['Y'] && !$options['M'] && !$options['D'] && !$options['C'])
		$options += array('Y'=>true,'M'=>true,'D'=>true,'C'=>true);

	if($options['short']==true)
		$extraM = "style='width:65;' ";
	if($options['submit']==true)
	{
		if($options['C'])
		{
		$e = "onchange='document.location.href=\"".PreparePHP_SELF($_REQUEST,array('month'.$title,'day'.$title,'year'.$title))."&amp;month$title=\"+this.form.month$title.value+\"&amp;day$title=\"+this.form.day$title.value+\"&amp;year$title=\"+this.form.year$title.value;'";
		$extraM .= $e;
		$extraD .= $e;
		$extraY .= $e;
		}
		else
		{
		$extraM .= "onchange='document.location.href=\"".PreparePHP_SELF($_REQUEST,array('month'.$title))."&amp;month$title=\"+this.form.month$title.value;'";
		$extraD .= "onchange='document.location.href=\"".PreparePHP_SELF($_REQUEST,array('day'.$title))."&amp;day$title=\"+this.form.day$title.value;'";
		$extraY .= "onchange='document.location.href=\"".PreparePHP_SELF($_REQUEST,array('year'.$title))."&amp;year$title=\"+this.form.year$title.value;'";
		}
	}

	if($options['C'])
		$_CENTRE['PrepareDate']++;

	if(strlen($date)==9) // ORACLE
	{
		$day = substr($date,8,2)+0;
		//$month = substr($date,5,2)+0;
		$month = MonthNWSwitch(substr($date,3,3),'tonum')+0;
		$year = substr($date,0,4);
		$return .= '<!-- '.MonthNWSwitch($month,'tonum').$day.$year.' -->';
		//$return .= date("Y-m-d", strtotime($month,$day,$year));
	}
	elseif(strlen($date)==10) // POSTGRES
	{
		$day = substr($date,8,2);
		$month = substr($date,5,2);
		$year = substr($date,0,4);

		$return .= '<!-- '.$year.$month.$day.' -->';
	}
	else //strlen($date)==11 ORACLE with 4-digit year
	{
		$day = substr($date,0,2);
		$month = substr($date,3,3);
		$year = substr($date,7,4);
		$return .= '<!-- '.$year.MonthNWSwitch($month,'tonum').$day.' -->';
	}

	// MONTH  ---------------
	if($options['M'])
	{
		$return .= '<SELECT NAME=month'.$title.' id=monthSelect'.$_CENTRE['PrepareDate'].' SIZE=1 '.$extraM.'>';
		if($allow_na)
		{
			if($month=='000')
				$return .= '<OPTION value="" SELECTED>N/A';
			else
				$return .= '<OPTION value="">N/A';
		}

		foreach(array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December') as $key=>$name)
			$return .= '<OPTION VALUE='.$key.($month==$key?' SELECTED':'').'>'.$name;
		$return .= '</SELECT>';
	}

	// DAY  ---------------
	if($options['D'])
	{
		$return .= '<SELECT NAME=day'.$title.' id=daySelect'.$_CENTRE['PrepareDate'].' SIZE=1 '.$extraD.'>';
		if($allow_na)
		{
			if($day=='00')
				$return .= '<OPTION value="" SELECTED>N/A';
			else
				$return .= '<OPTION value="">N/A';
		}

		for($i=1;$i<=31;$i++)
		{
			if(strlen($i)==1)
				$print = '0'.$i;
			else
				$print = $i;

			$return .= '<OPTION VALUE='.$print.($day==$print?' SELECTED':'').'>'.$i;
		}
		$return .= '</SELECT>';
	}

	// YEAR  ---------------
	if($options['Y'])
	{
		if(!$year || $year=='0000')
		{
			$begin = date('Y') - 20;
			$end = date('Y') + 5;
		}
		else
		{
			$begin = $year - 5;
			$end = $year + 5;
		}

		$return .= '<SELECT NAME=year'.$title.' id=yearSelect'.$_CENTRE['PrepareDate'].' SIZE=1 '.$extraY.'>';
		if($allow_na)
		{
			if($year=='0000')
				$return .= '<OPTION value="" SELECTED>N/A';
			else
				$return .= '<OPTION value="">N/A';
		}

		for($i=$begin;$i<=$end;$i++)
			$return .= '<OPTION VALUE='.$i.($year==$i?' SELECTED':'').'>'.$i;
		$return .= '</SELECT>';
	}

	if($options['C'])
		$return .= '<img src="assets/jscalendar/img.gif" id="trigger'.$_CENTRE['PrepareDate'].'" style="cursor: pointer; cursor:hand; border: 1px solid red;" onmouseover=this.style.background="red"; onmouseout=this.style.background=""; />';

	if($_REQUEST['_CENTRE_PDF'])
		$return = ProperDate($date);
	return $return;
}
?>