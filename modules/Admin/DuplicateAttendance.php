<?php

if(count($_REQUEST['mp_arr']))
{
        foreach($_REQUEST['mp_arr'] as $mp)
                $mp_list .= ",'$mp'";
        $mp_list = substr($mp_list,1);
        $last_mp = $mp;
}

$delete_message = " ";

//Widgets('course');
//Widgets('gpa');
//Widgets('class_rank');
//Widgets('letter_grade');

if($_REQUEST['modfunc']!='gradelist')
        //$extra['action'] .= "&_CENTRE_PDF=false";
        $x = "x";
else
        $extra['action'] .= '&modfunc=gradelist';

$extra['force_search'] = true;

if($_REQUEST['delete']=='true')
{
        //DeletePrompt('Duplicate Attendance Record');
        if(DeletePrompt('Duplicate Attendance Record'))
        {
                $i = 0;
                $ii = 0;
                $iii = 0;
                $sid = $_REQUEST['studentidx'];
                $cnt = $_REQUEST['deletecheck'];
                $pid = $_REQUEST['periodidx'];
                $sdt = $_REQUEST['schooldatex'];

                foreach($cnt as $a => $val_dchck){
                        $val1 = $val_dchck;
                        if($val1 >= 0){
                              //echo "$val1 |";
                              foreach($sid as $b => $val_sid){
                                      $val2 = $val_sid;
                                      if($val1 == $i){
                                              //echo "$val2 - $i||| ";
                                              foreach($pid as $c => $val_pid){
                                                    $val3 = $val_pid;
                                                    if($val1 == $ii){
                                                            //echo "$val1 - $val2 - $val3 ||| ";
                                                            foreach($sdt as $d => $val_sdt){
                                                                    $val4 = $val_sdt;
                                                                    if($val1 == $iii){
                                                                                //echo "$val1 - $val2 - $val3 - $val4 ||| ";
                                                                                DBQuery("DELETE FROM attendance_period WHERE STUDENT_ID='".$val2."' AND SCHOOL_DATE='".$val4."' AND COURSE_PERIOD_ID='".$val3."'");
                                                                    }
                                                                    $iii++;
                                                            }
                                                            $iii = 0;
                                                    }
                                                    $ii++;
                                              }
                                              $ii = 0;
                                      }
                                      $i++;
                              }
                              $i = 0;
                        }
                }

                //foreach($sid as $b => $val_sid){
                //        $val2 = $val_sid;
                //        echo "$val2| ";
                //}

                DrawHeader(ProgramTitle());
                echo "<TABLE width=100% border=0 cellpadding=0 cellspacing=0><TR>";
                echo "<TD bgcolor=#FFFFFF style=border-bottom:1px dotted #767676; align=left> &nbsp;";
                echo "<font color=#000000><FONT size=-1><IMG SRC=assets/check.gif>";
                echo _('The duplicate records have been deleted.');
                echo "</FONT></font></TD></TR></TABLE><BR>";

        }
}

if((!$_REQUEST['search_modfunc'] || $_CENTRE['modules_search']) && $_REQUEST['delete']!='true')
{
        DrawHeader(ProgramTitle());

        $extra['new'] = true;
        Search('student_id',$extra);
}
elseif($_REQUEST['delete']!='true')
{
        $RET = GetStuList($extra);

	if (isset($_REQUEST['page'])){
		$urlpage = $_REQUEST['page'];
	}else{
		$urlpage = 1;
	}

	$firstrow = 1;
	$rows_per_page = 25;
	$endrow = $urlpage * $rows_per_page;
	$startrow = $endrow - $rows_per_page;

	//echo "Startrow: $startrow  Endrow: $endrow <br>";

        if(count($RET))
        {

               unset($extra);
               $extra['SELECT_ONLY'] .= "ap.COURSE_PERIOD_ID, s.STUDENT_ID, s.FIRST_NAME, s.LAST_NAME, ap.SCHOOL_DATE, cp.TITLE, ap.PERIOD_ID, sc.START_DATE, sc.END_DATE ";
               $extra['FROM'] .= " ,ATTENDANCE_PERIOD ap, COURSE_PERIODS cp, SCHEDULE sc ";
               //$extra['WHERE'] .= " AND ssm.student_id=s.student_id AND ap.STUDENT_ID=s.STUDENT_ID AND ap.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR ssm.END_DATE IS NULL) ";
               //$extra['WHERE'] .= " AND ssm.student_id=s.student_id AND ap.STUDENT_ID=s.STUDENT_ID AND ap.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID ";
               $extra['WHERE'] .= " AND ap.STUDENT_ID=s.STUDENT_ID AND sc.STUDENT_ID=s.STUDENT_ID AND ap.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND ap.COURSE_PERIOD_ID = sc.COURSE_PERIOD_ID AND sc.END_DATE > '1999-01-01' ";
               $extra['ORDER_BY'] = ' STUDENT_ID, COURSE_PERIOD_ID, SCHOOL_DATE';
               Widgets('course');
               Widgets('gpa');
               Widgets('class_rank');
               Widgets('letter_grade');
               $pageresult1 = GetStuList($extra);

               $totalrows = 0;
               foreach($pageresult1 as $rr){
	                $afterr = "N";

	                $studentidr = $rr['STUDENT_ID'];
	                $courseidr = $rr['COURSE_PERIOD_ID'];
	                $periodidr = $rr['PERIOD_ID'];
	                $firstr = $rr['FIRST_NAME'];
	                $lastr = $rr['LAST_NAME'];
	                $schooldater = $rr['SCHOOL_DATE'];
	                $titler = $rr['TITLE'];
	                $startr = $rr['START_DATE'];
	                $endr = $rr['END_DATE'];

	                if($schooldater > $endr){
	                        $afterr = "Y";
	                }

	                    if(($studentidr == $studentid2) && ($courseidr == $courseid2) && ($schooldater == $schooldate2) && ($startr == $start2)){
	                    		$totalrows++;
	                    }else if(($schooldater > $endr) && ($endr != NULL) && ($startr == $start2)){
	                    		$totalrows++;
	                    }else{
	                         //Do nothing
	                    }

	                    $studentid2 = $studentidr;
	                    $courseid2 = $courseidr;
	                    $periodid2 = $periodidr;
	                    $schooldate2 = $schooldater;
	                    $first2 = $firstr;
	                    $last2 = $lastr;
	                    $title2 = $titler;
	                    $start2 = $startr;
	                    $end2 = $endr;
	       }
	       //echo "$totalrows";

               unset($extra);
               $extra['SELECT_ONLY'] .= "ap.COURSE_PERIOD_ID, s.STUDENT_ID, s.FIRST_NAME, s.LAST_NAME, ap.SCHOOL_DATE, cp.TITLE, cp.SHORT_NAME, ap.PERIOD_ID, sc.START_DATE, sc.END_DATE ";
               $extra['FROM'] .= " ,ATTENDANCE_PERIOD ap, COURSE_PERIODS cp, SCHEDULE sc ";
               //$extra['WHERE'] .= " AND ssm.student_id=s.student_id AND ap.STUDENT_ID=s.STUDENT_ID AND ap.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR ssm.END_DATE IS NULL) ";
               //$extra['WHERE'] .= " AND ssm.student_id=s.student_id AND ap.STUDENT_ID=s.STUDENT_ID AND ap.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID ";
               $extra['WHERE'] .= " AND ap.STUDENT_ID=s.STUDENT_ID AND sc.STUDENT_ID=s.STUDENT_ID AND ap.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND ap.COURSE_PERIOD_ID = sc.COURSE_PERIOD_ID AND sc.END_DATE > '1999-01-01' ";
               $extra['ORDER_BY'] = ' STUDENT_ID, COURSE_PERIOD_ID, SCHOOL_DATE';
               Widgets('course');
               Widgets('gpa');
               Widgets('class_rank');
               Widgets('letter_grade');
               $result1 = GetStuList($extra);

               DrawHeader(ProgramTitle());
               echo "$delete_message";

               //echo "<form action=Modules.php?modname=Attendance/DuplicateAttendance.php&modfunc=&search_modfunc=list&next_modname=Attendance/DuplicateAttendance.php&_CENTRE_PDF=true method=POST>";
               echo "<form action=Modules.php?modname=Attendance/DuplicateAttendance.php&modfunc=&search_modfunc=list&next_modname=Attendance/DuplicateAttendance.php&delete=true method=POST>";
               DrawHeader('',SubmitButton(Delete));

	       $num_rows = $totalrows;

			if($num_rows > $rows_per_page){

				$totalpages = $num_rows/$rows_per_page;
				$totalpages = ceil($totalpages);

				echo "<center><small>Page:</small> ";
				$first = 0;
				$ii = 1;
				for($i=0;$i<$totalpages;$i++){

					if($urlpage == $ii){
						echo "<b>$ii</b> &nbsp;";
					}else{
						echo "<a href=Modules.php?modname=Attendance/DuplicateAttendance.php&modfunc=&search_modfunc=list&next_modname=Attendance/DuplicateAttendance.php&delete=false&page=$ii>$ii</a> &nbsp;";
					}

					$first = $first + $rows_per_page;
					$ii++;
				}
				echo "<small>of $totalpages pages</small>";
			}


               echo '<BR>';
               echo "<br>&nbsp;<br><center><table border=0 cellpadding=6>";
               echo "<tr><TD bgcolor=#333366 class=column_heading><font color=white><INPUT type=checkbox value=Y name=controller onclick=checkAll(this.form,this.form.controller.checked,'deletecheck');> &nbsp</td>";
               echo "<TD bgcolor=#333366 class=column_heading><font color=white><b>Student (Centre ID)</td>";
               echo "<TD bgcolor=#333366 class=column_heading><font color=white><b>Course (Course Period ID)</td>";
               echo "<TD bgcolor=#333366 class=column_heading><font color=white><b>Course Start Date</td>";
               echo "<TD bgcolor=#333366 class=column_heading><font color=white><b>Course End Date</td>";
               echo "<TD bgcolor=#333366 class=column_heading><font color=white><b>Attendance Date</td></tr>";

               $URIcount = 0;
               $count = 0;
               $yellow = 1;
               $after = "N";

               foreach($result1 as $r){
                $after = "N";

                $studentid = $r['STUDENT_ID'];
                $courseid = $r['COURSE_PERIOD_ID'];
                $periodid = $r['PERIOD_ID'];
                $first = $r['FIRST_NAME'];
                $last = $r['LAST_NAME'];
                $schooldate = $r['SCHOOL_DATE'];
                $title = $r['TITLE'];
                $short_name = $r['SHORT_NAME'];
                $start = $r['START_DATE'];
                $end = $r['END_DATE'];

                if($schooldate > $end){
                        $after = "Y";
                }

                    if(($studentid == $studentid2) && ($courseid == $courseid2) && ($schooldate == $schooldate2) && ($start == $start2)){

			$URIcount++;
			//echo "$URIcount | ";
			if($URIcount > $startrow && $URIcount < $endrow){

                                echo "<input type=hidden name=delete value=true>";
                                echo "<input type=hidden name=studentidx[$count] value=$studentid>";
                                echo "<input type=hidden name=periodidx[$count] value=$courseid>";
                                echo "<input type=hidden name=schooldatex[$count] value=$schooldate>";

                                if($yellow == 0){
                                       $color = 'F8F8F9';
                                       $yellow++;
                                }else{
                                       $color = Preferences('COLOR');
                                       $yellow = 0;
                                }
                                echo "<tr><td bgcolor=#$color><input type=checkbox name=deletecheck[$count] value=$count></td><td bgcolor=#$color><font color=#000000><FONT size=-1>$first $last ($studentid)</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$short_name ($courseid)</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$start &nbsp</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$end &nbsp</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$schooldate</td></tr>";

                                $count++;
                        }

                    }else if(($schooldate > $end) && ($end != NULL) && ($start == $start2)){

			$URIcount++;
			//echo "$URIcount | ";
			if($URIcount > $startrow && $URIcount < $endrow){

                                echo "<input type=hidden name=delete value=true>";
                                echo "<input type=hidden name=studentidx[$count] value=$studentid>";
                                echo "<input type=hidden name=periodidx[$count] value=$courseid>";
                                echo "<input type=hidden name=schooldatex[$count] value=$schooldate>";

                                if($yellow == 0){
                                       $color = 'F8F8F9';
                                       $yellow++;
                                }else{
                                       $color = Preferences('COLOR');
                                       $yellow = 0;
                                }
                                echo "<tr><td bgcolor=#$color><input type=checkbox name=deletecheck[$count] value=$count></td><td bgcolor=#$color><font color=#000000><FONT size=-1>$first $last ($studentid)</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$short_name ($courseid)</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$start &nbsp</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$end &nbsp</td><td bgcolor=#$color><font color=#000000><FONT size=-1>$schooldate</td></tr>";

                                $count++;

                         }

                    }else{
                         //echo "<tr><td>$studentid</td><td>$courseid</td></tr>";
                         $duplicate = 0;
                    }

                    $studentid2 = $studentid;
                    $courseid2 = $courseid;
                    $periodid2 = $periodid;
                    $schooldate2 = $schooldate;
                    $first2 = $first;
                    $last2 = $last;
                    $title2 = $title;
                    $start2 = $start;
                    $end2 = $end;
                    //echo "<tr><td>$studentid</td><td>$courseid</td></tr>";
                    //echo "$studentid | $courseid";
               }
               if($count == 0){
                  echo "<tr><td bgcolor=#".Preferences('COLOR')." colspan=6><b>No Duplicates Found</td></tr>";
                  echo "</table>";
                }else{
                  echo "</table>";
                  echo "<br><input type=submit name=submit value=Delete>";
                }

               echo "</form>";
               $RET = " ";

        }
        else
                BackPrompt(_('No Students were found.'));
                //echo "No Students were found";
}

function _makeTeacher($teacher,$column)
{
        return substr($teacher,strrpos(str_replace(' - ',' ^ ',$teacher),'^')+2);
}
?>