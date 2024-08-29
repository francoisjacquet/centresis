<?php

$max_cols = 3;
$max_rows = 10;
$to_family = Localize('colon',_('To the parents of'));

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
		$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

		$_REQUEST['mailing_labels']='Y';
		if($_REQUEST['to_address'])
			$_REQUEST['residence']='Y';
		Widgets('mailing_labels');
		$extra['SELECT'] .= ",coalesce(s.CUSTOM_200000002,s.FIRST_NAME) AS NICK_NAME";
		$extra['group'] = array('ADDRESS_ID');
		$RET = GetStuList($extra);

		if(count($RET))
		{
			$handle = PDFstart();
			echo '<!-- MEDIA SIZE 8.5x11in -->';
			echo '<!-- MEDIA TOP 0.5in -->';
			echo '<!-- MEDIA BOTTOM 0.25in -->';
			echo '<!-- MEDIA LEFT 0.25in -->';
			echo '<!-- MEDIA RIGHT 0.25in -->';
			echo '<!-- FOOTER RIGHT "" -->';
			echo '<!-- FOOTER LEFT "" -->';
			echo '<!-- FOOTER CENTER "" -->';
			echo '<!-- HEADER RIGHT "" -->';
			echo '<!-- HEADER LEFT "" -->';
			echo '<!-- HEADER CENTER "" -->';
			echo '<table width="100%" height="860" border="0" cellspacing="0" cellpadding="0">';

			$cols = 0;
			$rows = 0;
			for($i=-(($_REQUEST['start_row']-1)*$max_cols+$_REQUEST['start_col']-1);$i<count($RET);$i++)
			{
				if($i>=0)
				{
					$addresses = current($RET);
					next($RET);
					if($_REQUEST['to_address']=='student')
					{
						foreach($addresses as $key=>$address)
						{
							if($_REQUEST['student_name']=='common')
								$name = $address['LAST_NAME'].', '.$address['NICK_NAME'];
							elseif($_REQUEST['student_name']=='given')
								$name = $address['LAST_NAME'].', '.$address['FIRST_NAME'].' '.$address['MIDDLE_NAME'];
							elseif($_REQUEST['student_name']=='common_natural')
								$name = $address['NICK_NAME'].' '.$address['LAST_NAME'];
							elseif($_REQUEST['student_name']=='given_natural')
								$name = $address['FIRST_NAME'].' '.$address['LAST_NAME'];
							else
								$name = $address['FULL_NAME'];
							$addresses[$key]['MAILING_LABEL'] = $name.'<BR>'.substr($address['MAILING_LABEL'],strpos($address['MAILING_LABEL'],'<!-- -->'));
						}
					}
					elseif($_REQUEST['to_address']=='family')
					{
						// if grouping by address, replace people list in mailing labels with students list
						$lasts = array();
						foreach($addresses as $address)
							$lasts[$address['LAST_NAME']][] = $_REQUEST['family_name']=='common'?$address['NICK_NAME']:($_REQUEST['family_name']=='given'?$address['FIRST_NAME']:(Preferences('NAME')=='Common'?$address['NICK_NAME']:$address['FIRST_NAME']));
						$students = '';
						foreach($lasts as $last=>$firsts)
						{
							$student = '';
							$previous = '';
							foreach($firsts as $first)
							{
								if($student && $previous)
									$student .= ', '.$previous;
								elseif($previous)
									$student = $previous;
								$previous = $first;
							}
							if($student)
								$student .= ' & '.$previous.' '.$last;
							else
								$student = $previous.' '.$last;
							$students .= $student.', ';
						}
						$addresses = array(1=>array('MAILING_LABEL'=>'<SMALL>'.$to_family.'<BR></SMALL>'.substr($students,0,-2).'<BR>'.substr($addresses[1]['MAILING_LABEL'],strpos($addresses[1]['MAILING_LABEL'],'<!-- -->'))));
					}
				}
				else
					$addresses = array(1=>array('MAILING_LABEL'=>' '));

				foreach($addresses as $address)
				{
					if(!$address['MAILING_LABEL'])
						continue;

					if($cols<1)
						echo '<tr>';
					echo '<td width="33.3%" height="86" align="center" valign="middle">';
					echo $address['MAILING_LABEL'];
					echo '</td>';

					$cols++;

					if($cols==$max_cols)
					{
						echo '</tr>';
						$rows++;
						$cols = 0;
					}

					if($rows==$max_rows)
					{
						echo '</table><!--NEW PAGE -->';
						echo '<table width="100%" height="860" border="0" cellspacing="0" cellpadding="0">';
						$rows = 0;
					}
				}
			}

			if ($cols==0 && $rows==0)
			{}
			else
			{
				while ($cols!=0 && $cols<$max_cols)
				{
					echo '<td width="33.3%" height="86" align="center" valign="middle">&nbsp;</td>';
					$cols++;
				}
				if ($cols==$max_cols)
					echo '</tr>';
				echo '</table>';
			}
			//echo '</body></html>';
			echo '</body>';
			PDFstop($handle);
		}
		else
			BackPrompt(_('No Students were found.'));
	}
}

if(!$_REQUEST['modfunc'])
{
	DrawHeader(ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools]&_CENTRE_PDF=true method=POST>";
		$extra['header_right'] = '<INPUT type=submit value="'._('Create Labels for Selected Students').'">';

		$extra['extra_header_left'] = '<TABLE>';

		$extra['extra_header_left'] .= '<TR><TD colspan=5><b>Address Labels:</b></TD></TR>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=to_address value="" checked>'._('To Contacts').'</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=radio name=residence value="" checked><small>'._('Mailing').'</small></TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=radio name=residence value="Y"><small>'._('Residence').'</small></TD>';
		$extra['extra_header_left'] .= '<TD colspan=2></TD></TR>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=to_address value=student>'._('To Student').'</TD>';
		if(Preferences('NAME')=='Common')
		{
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=student_name value="" checked><small>'._('Last, Common').'</small></TD>';
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=student_name value=given><small>'._('Last, Given Middle').'</small></TD>';
		}
		else
		{
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=student_name value="" checked><small>'._('Last, Given Middle').'</small></TD>';
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=student_name value=common><small>'._('Last, Common').'</small></TD>';
		}
		$extra['extra_header_left'] .= '<TD><INPUT type=radio name=student_name value=given_natural><small>'._('Given Last').'</small></TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=radio name=student_name value=common_natural><small>'._('Common Last').'</small></TD></TR>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=to_address value=family>'._('To Family').'</TD>';
		if(Preferences('NAME')=='Common')
		{
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=family_name value="" checked><small>'._('Common Last').'</small></TD>';
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=family_name value=given><small>'._('Given Last').'</small></TD>';
		}
		else
		{
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=family_name value="" checked><small>'._('Given Last').'</small></TD>';
			$extra['extra_header_left'] .= '<TD><INPUT type=radio name=family_name value=common><small>'._('Common Last').'</small></TD>';
		}

		$extra['extra_header_left'] .= '<TD colspan=2></TD></TR>';
		$extra['extra_header_left'] .= '</TABLE>';
		$extra['extra_header_right'] = '<TABLE>';

		$extra['extra_header_right'] .= '<TR><TD align=right>'._('Starting row').'</TD><TD><SELECT name=start_row>';
		for($row=1; $row<=$max_rows; $row++)
			$extra['extra_header_right'] .=  '<OPTION value="'.$row.'">'.$row;
		$extra['extra_header_right'] .=  '</SELECT></TD></TR>';
		$extra['extra_header_right'] .= '<TR><TD align=right>Starting column</TD><TD><SELECT name=start_col>';
		for($col=1; $col<=$max_cols; $col++)
			$extra['extra_header_right'] .=  '<OPTION value="'.$col.'">'.$col;
		$extra['extra_header_right'] .= '</SELECT></TD></TR>';

		$extra['extra_header_right'] .= '</TABLE>';
	}

	//Widgets('course');
	//Widgets('request');
	//Widgets('activity');
	//Widgets('absences');
	//Widgets('gpa');
	//Widgets('class_rank');
	//Widgets('letter_grade');
	//Widgets('eligibility');
	//$extra['force_search'] = true;

	$extra['SELECT'] .= ",s.STUDENT_ID AS CHECKBOX";
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit value="'._('Create Labels for Selected Students').'"></CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
?>
