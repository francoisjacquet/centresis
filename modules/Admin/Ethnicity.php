<?php
unset($_SESSION['_REQUEST_vars']['values']);unset($_SESSION['_REQUEST_vars']['modfunc']);

if($_REQUEST['modfunc']=='save')
{
	$S_RET = DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
	$SCHOOLNAME = strtoupper($S_RET[1]['TITLE']);

	$extra['SELECT'] .= ",custom_200000000 AS Gender, sgl.short_name AS G_LEVEL, custom_200000001 AS Ethnic, sgl.sort_order";
	$extra['FROM'] .= ",SCHOOL_GRADELEVELS sgl";
	$extra['WHERE'] .= " AND ssm.GRADE_ID=sgl.id";
	$extra['ORDER_BY'] .= "sgl.sort_order ASC";
	$RET = GetStuList($extra);

	#var_dump($RET);

	if(count($RET))
	{
		$handle = PDFStart();
		echo '<!-- MEDIA SIZE 8.5x11in -->';

		$gender_arr = array();
		$ethnic_arr = array();
		$grade_arr = array();

		foreach($RET as $student_id=>$grade_lvls) {
			$i=0;
			foreach($grade_lvls as $var_arr=>$ethnic)	{
				$i++;
				if($var_arr=="GENDER") :
					$gender_arr['GENDER'][] = $ethnic; 
					$last_gender = $ethnic;

				elseif($var_arr=="G_LEVEL") :
					$grade_arr['GRADE'][] = $ethnic;
					$last_grade = $ethnic;

				elseif($var_arr=="ETHNIC") :
					$ethnic_arr['ETHNICCODE'][] = substr($ethnic,0,1); 
					$ethnic_arr['ETHNIC_TITLE'][] = $ethnic; 

					switch($last_gender) {
						case "Male":
							$ethnic_arr['MALE'][$last_grade][] = substr($ethnic,0,1); 
							break;
						case "Female":
							$ethnic_arr['FEMALE'][$last_grade][] = substr($ethnic,0,1); 
							break;
					}

				endif;
			}
		}

		$ALL_GRADES = array_count_values($grade_arr['GRADE']);
		$ALL_GENDER = array_count_values($gender_arr['GENDER']);
		$ALL_ETHNICCODE = array_count_values($ethnic_arr['ETHNICCODE']);
		$ALL_ETHNIC_TITLE = array_count_values($ethnic_arr['ETHNIC_TITLE']);
			array_reorder_keys($ALL_GRADES, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15');
			array_reorder_keys($ALL_GENDER, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15');
			array_reorder_keys($ALL_ETHNICCODE, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15');
			array_reorder_keys($ALL_ETHNIC_TITLE, '1--American Indian,2--Asian,3--Black,4--Pacific Islander,5--White,6--Hispanic,7--Multi-Racial,8--,9,10,11,12,13,14,15');
			$Ethnics_Num = count($ALL_ETHNICCODE);
			$td_percent_w = (int)100/$Ethnics_Num.'%';
			#var_dump($ALL_ETHNICCODE);

			#echo '<style type="text/css">table { font-family:"Source Sans Pro",sans-serif; font-size:12px; }</style>';
			echo '<font face="\'lucida sans unicode\'" size=2><TABLE bgcolor=#e3eef9 border=2 bordercolor=#cbd6e5 cellpadding=3><TR>';
			echo '<TD width="100%"><TABLE width=100%>';
				echo '<TR><TD border=0 width=120 align=left valign=top>'.date('h:i:s A').'</TD><TD width=450 align=center valign=top><b>'.$SCHOOLNAME.'</b> <br>Race Code Distribution</TD><TD width=120 align=right valign=top>'.date('m/d/Y').'</TD></TR>';
			echo '</TABLE></TD>';
			echo '</TR></TABLE>';
			echo '<br><br>';
			echo '<TABLE width="100%">';
				echo '<TR>';
				echo '<TD width=70%><TABLE cellpadding=0>';
					echo '<TR>';
						echo '<TD width=13%><b>Grade</b></TD>';
						echo '<TD colspan=3 width=87% align=center><b>Race Codes</b></TD>';
					echo '</TR>';
					echo '<TR>';
						echo '<TD width=13%><b>Level</b></TD>';
						echo '<TD width=10% align=left><b>Gender</b></TD>';
						echo '<TD width=65% align=right>';
							echo '<TABLE width=92%><TR>';
								foreach($ALL_ETHNICCODE as $key=>$value) {
									echo '<TD align=center width="'.$td_percent_w.'"><b>'.trim($key).'</b></TD>';
								}
							echo '</TR></TABLE>';
						echo '</TD>';
						echo '<TD width=12% align=right><b>Total</b></TD>';
					echo '</TR>';
					unset($key); unset($value);

					$A_TOTAL = 0;
					$TM_TOTAL = array();
					$TF_TOTAL = array();

					foreach($ALL_GRADES as $key=>$value) {
					echo '<TR><TD colspan=4><TABLE cellpadding=0>';
						// Males
						echo '<TR><TD width=13%>'.$key.'</TD>';
						echo '<TD width=10% align=left>Males</TD>';
						echo '<TD width=65% align=right>';
							echo '<TABLE width=92%><TR>';
								$M_TOTAL = 0;
								$ethnic_arr['MR'] = array_count_values($ethnic_arr['MALE'][$key]);
								foreach($ALL_ETHNICCODE as $e=>$v) {
									$M_TOTAL += $ethnic_arr['MR'][$e];
									$TM_TOTAL['M'][$e] += $ethnic_arr['MR'][$e];
									echo ($ethnic_arr['MR'][$e]!="")?'<TD align=center width="'.$td_percent_w.'">'.trim($ethnic_arr['MR'][$e]):'<TD align=center width="'.$td_percent_w.'">0';
									echo '</TD>';
								}

							echo '</TR></TABLE>';
						echo '</TD>';
						echo '<TD width=12% align=right>'.$M_TOTAL.'</TD></TR>';

						// Females
						echo '<TR><TD width=13%>&nbsp;</TD>';
						echo '<TD width=10% align=left>Females</TD>';
						echo '<TD width=65% align=right>';
							echo '<TABLE width=92%><TR>';
								$FEM_TOTAL = 0;
								$ethnic_arr['MS'] = array_count_values($ethnic_arr['FEMALE'][$key]);
								foreach($ALL_ETHNICCODE as $e=>$v) {
									$FEM_TOTAL += $ethnic_arr['MS'][$e];
									$TF_TOTAL['F'][$e] += $ethnic_arr['MS'][$e];
									echo ($ethnic_arr['MS'][$e]!="")?'<TD align=center width="'.$td_percent_w.'">'.trim($ethnic_arr['MS'][$e]):'<TD align=center width="'.$td_percent_w.'">0';
									echo '</TD>';
									$TF_TOTAL[$e][] = $ethnic_arr['MS'][$e];
								}
							echo '</TR></TABLE>';
						echo '</TD>';
						echo '<TD width=12% align=right>'.$FEM_TOTAL.'</TD></TR>';

						// Totals
						echo '<TR><TD width=13%>&nbsp;</TD>';
						echo '<TD width=10% align=left>Totals</TD>';
						echo '<TD width=65% align=right>';
							echo '<TABLE width=92%><TR>';
								$T_TOTAL = 0;
								foreach($ALL_ETHNICCODE as $e=>$v) {
									$T_TOTAL += ($ethnic_arr['MS'][$e] + $ethnic_arr['MR'][$e]);
									echo '<TD align=center width="'.$td_percent_w.'">'.trim(($ethnic_arr['MS'][$e] + $ethnic_arr['MR'][$e])).'</TD>';
								}
							echo '</TR></TABLE>';
						echo '</TD>';
						echo '<TD width=12% align=right>'.$T_TOTAL.'</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
					}


						// Total/All
						echo '<TR><TD colspan=4><TABLE cellpadding=0>';
						echo '<TR><TD width=13%>Total</TD>';
						echo '<TD width=10% align=left>Males</TD>';
						echo '<TD width=65% align=right>';
							$A_TOTAL = 0;
							echo '<TABLE width=92%><TR>';
								foreach($ALL_ETHNICCODE as $e=>$v) {
									$A_TOTAL += $TM_TOTAL['M'][$e];
									echo '<TD align=center width="'.$td_percent_w.'">'.trim($TM_TOTAL['M'][$e]).'</TD>';
								}
							echo '</TR></TABLE>';
						echo '</TD>';
						echo '<TD width=12% align=right>'.$A_TOTAL.'</TD></TR>';
						echo '</TABLE></TD></TR>';

						echo '<TR><TD colspan=4><TABLE cellpadding=0>';
						echo '<TR><TD width=13%></TD>';
						echo '<TD width=10% align=left>Females</TD>';
						echo '<TD width=65% align=right>';

							echo '<TABLE width=92%><TR>';
								$A_TOTAL = 0;
								foreach($ALL_ETHNICCODE as $e=>$v) {
									$A_TOTAL += $TF_TOTAL['F'][$e];
									echo '<TD align=center width="'.$td_percent_w.'">'.trim($TF_TOTAL['F'][$e]).'</TD>';
								}
							echo '</TR></TABLE>';
						echo '</TD>';
						echo '<TD width=12% align=right>'.$A_TOTAL.'</TD></TR>';
						echo '</TABLE></TD></TR>';

						echo '<TR><TD colspan=4><TABLE cellpadding=0>';
						echo '<TR><TD width=13%></TD>';
						echo '<TD width=10% align=left>Totals</TD>';
						echo '<TD width=65% align=right>';

							echo '<TABLE width=92%><TR>';
								$A_TOTAL = 0;
								foreach($ALL_ETHNICCODE as $e=>$v) {
									$A_TOTAL += ($v);
									echo '<TD align=center width="'.$td_percent_w.'">'.trim($v).'</TD>';
								}
							echo '</TR></TABLE>';
						echo '</TD>';
						echo '<TD width=12% align=right>'.$A_TOTAL.'</TD></TR>';
						echo '</TABLE></TD></TR>';

						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';
						echo '<TR><TD colspan=4>&nbsp;</TD></TR>';

						// Legends
						echo '<TR><TD colspan=4><TABLE cellpadding=0>';
						echo '<TR><TD width=100% colspan=4><b>Race Codes Legends</b></TD></TR>';
						echo '<TR><TD width=100% colspan=4><TABLE width=690><TR>';
							$max_col = 3; $i=0;
							foreach($ALL_ETHNIC_TITLE as $key=>$value) {
								$i++;
								if($i>$max_col) { echo '</TR><TR>'; $i=1; }
								echo '<TD align=left width=230>'.trim(str_replace("--", " ", $key)).'</b></TD>';
							}
						echo '</TR></TABLE></TD></TR>';
						echo '</TR></TABLE></TD></TR>';


					echo '</TABLE></TD></TR>';
				echo '</TABLE></TD>';
				echo '</TR>';
			echo '</TABLE></font>';
		
		#echo '<!-- NEW PAGE -->';
		PDFStop($handle);
	}
	else
		BackPrompt(_('No Students were found.'));
}

if(!$_REQUEST['modfunc'] && User('PROFILE')=='admin')
{
	DrawHeader(ProgramTitle());
	DrawHeader('',SubmitButton(_('Generate PDF')));
	
	$page_name = _('Ethnicity Report');
	echo '<BR>';
	PopTable('header',$page_name);

	echo "<FORM ACTION=Modules.php?modname=$_REQUEST[modname]&modfunc=save&_CENTRE_PDF=true  METHOD=POST>";
	echo "<FIELDSET><TABLE>";
	echo "<h3 style='font-size:16px;margin:4px 0;text-align:center;'>Race Code Distribution</h3><p style='margin:5px 14px;'>This module will generate Race Codes consists of Grade Levels, Genders with their Total numbers.</p>";
	echo '<CENTER>'.SubmitButton(_('Generate PDF')).'</CENTER>';
	
	echo "</TABLE></FIELDSET>";
	
	echo '</FORM>';
	PopTable('footer');
}

/*
	$S_RET = DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
	$SCHOOLNAME = $S_RET[1]['TITLE'];

	$extra['SELECT'] .= ",custom_200000000 AS Gender, custom_200000001 AS Ethnic, sgl.short_name AS G_LEVEL, sgl.sort_order";
	$extra['FROM'] .= ",SCHOOL_GRADELEVELS sgl";
	$extra['WHERE'] .= " AND ssm.GRADE_ID=sgl.id";
	$extra['ORDER_BY'] .= "sgl.sort_order ASC";
	$RET = GetStuList($extra);
	#echo count($RET);
	#print_r($RET);

///*
	if(count($RET))
	{
		$handle = PDFStart();
		echo '<!-- MEDIA SIZE 8.5x11in -->';
			echo '<TABLE><TR><TD width=50> &nbsp; </TD><TD width=300>'.$SCHOOLNAME.'</TD></TR></TABLE>';
		
		#echo '<!-- NEW PAGE -->';
		PDFStop($handle);
	}
	else
		BackPrompt(_('No Students were found.'));
	}*/
//*/

function array_reorder_keys(&$array, $keynames){
    if(empty($array) || !is_array($array) || empty($keynames)) return;
    if(!is_array($keynames)) $keynames = explode(',',$keynames);
    if(!empty($keynames)) $keynames = array_reverse($keynames);
    foreach($keynames as $n){
        if(array_key_exists($n, $array)){
            $newarray = array($n=>$array[$n]); //copy the node before unsetting
            unset($array[$n]); //remove the node
            $array = $newarray + array_filter($array); //combine copy with filtered array
        }
    }
}
?>