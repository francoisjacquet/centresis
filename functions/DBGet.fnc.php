<?php
/*
	Send it an SQL Select Query Identifier and an optional functions array where the key is the
	column in the database that the function (contained in the value of the array) is applied.

	Use the second parameter (an array of functions indexed by the column to apply them to)
	if you need to do complicated formatting and don't want to loop through the
	array before sending it to ListOutput.  Use especially when expecting a large result.

	$THIS_RET is a useful variable for the functions in the second parameter.  It is the current row of the
	query result.

	Furthermore, the third parameter can be used to change the array index to a column in the
	result.  For instance, if you selected student_id from students, and chose to index by student_id,
	you would get a result similar to this :
	$array[1031806][1] = array('STUDENT_ID'=>'1031806');

	The third parameter should be an array -- ordered by the importance of the index.  So, if you select
	COURSE_ID,COURSE_PERIOD_ID from COURSE_PERIODS, and choose to index by
	array('COURSE_ID','COURSE_PERIOD_ID') then you will be returned an array formatted like this:
	$array[10101][402345][1] = array('COURSE_ID'=>'10101','COURSE_PERIOD_ID'=>'402345')
*/

function DBGet($QI,$functions=array(),$index=array())
{	global $THIS_RET;

	$index_count = count($index);
	$tmp_THIS_RET = $THIS_RET;

	$results = array();
	while($RET=db_fetch_row($QI))
	{
		$THIS_RET = $RET;

		if($index_count)
		{
			$ind = '';
			foreach($index as $col)
				$ind .= "['".str_replace("'","\'",$THIS_RET[$col])."']";
			eval('$s'.$ind.'++;$this_ind=$s'.$ind.';');
		}
		else
			$s++; // 1-based if no index specified
		foreach($RET as $key=>$value)
		{
			if($functions[$key] && function_exists($functions[$key]))
			{
				if($index_count)
					eval('$results'.$ind.'[$this_ind][$key] = $functions[$key]($value,$key);');
				else
					$results[$s][$key] = $functions[$key]($value,$key);
			}
			else
			{
				if($index_count)
					eval('$results'.$ind.'[$this_ind][$key] = $value;');
				else
					$results[$s][$key] = $value;
			}
		}
	}

	$THIS_RET = $tmp_THIS_RET;

	return $results;
}

function money_formatt($format, $number) { 
	$regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'. 
			  '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/'; 
	if (setlocale(LC_MONETARY, 0) == 'C') { 
		setlocale(LC_MONETARY, ''); 
	} 
	$locale = localeconv(); 
	preg_match_all($regex, $format, $matches, PREG_SET_ORDER); 
	foreach ($matches as $fmatch) { 
		$value = floatval($number); 
		$flags = array( 
			'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? 
						   $match[1] : ' ', 
			'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0, 
			'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? 
						   $match[0] : '+', 
			'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0, 
			'isleft'    => preg_match('/\-/', $fmatch[1]) > 0 
		); 
		$width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0; 
		$left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0; 
		$right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits']; 
		$conversion = $fmatch[5]; 
 
		$positive = true; 
		if ($value < 0) { 
			$positive = false; 
			$value  *= -1; 
		} 
		$letter = $positive ? 'p' : 'n'; 
 
		$prefix = $suffix = $cprefix = $csuffix = $signal = ''; 
 
		$signal = $positive ? $locale['positive_sign'] : $locale['negative_sign']; 
		switch (true) { 
			case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+': 
				$prefix = $signal; 
				break; 
			case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+': 
				$suffix = $signal; 
				break; 
			case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+': 
				$cprefix = $signal; 
				break; 
			case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+': 
				$csuffix = $signal; 
				break; 
			case $flags['usesignal'] == '(': 
			case $locale["{$letter}_sign_posn"] == 0: 
				$prefix = '('; 
				$suffix = ')'; 
				break; 
		} 
		if (!$flags['nosimbol']) { 
			$currency = $cprefix . 
						($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . 
						$csuffix; 
		} else { 
			$currency = ''; 
		} 
		$space  = $locale["{$letter}_sep_by_space"] ? ' ' : ''; 
 
		$value = number_format($value, $right, $locale['mon_decimal_point'], 
				 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']); 
		$value = @explode($locale['mon_decimal_point'], $value); 
 
		$n = strlen($prefix) + strlen($currency) + strlen($value[0]); 
		if ($left > 0 && $left > $n) { 
			$value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0]; 
		} 
		$value = implode($locale['mon_decimal_point'], $value); 
		if ($locale["{$letter}_cs_precedes"]) { 
			$value = $prefix . $currency . $space . $value . $suffix; 
		} else { 
			$value = $prefix . $value . $space . $currency . $suffix; 
		} 
		if ($width > 0) { 
			$value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? 
					 STR_PAD_RIGHT : STR_PAD_LEFT); 
		} 
 
		$format = str_replace($fmatch[0], $value, $format); 
	} 
	return $format; 
}


?>