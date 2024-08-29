<?php
include 'modules/Grades/DeletePromptX.fnc.php';
DrawHeader(ProgramTitle());
Search('student_id');

if(UserStudentID())
{
    $student_id = UserStudentID();
    $mp_id = $_REQUEST['mp_id'];
    $tab_id = ($_REQUEST['tab_id']?$_REQUEST['tab_id']:'grades');
    if ($_REQUEST['modfunc']=='update' && $_REQUEST['removemp'] && $mp_id && DeletePromptX('Marking Period')){
            DBQuery("DELETE FROM STUDENT_MP_STATS WHERE student_id = $student_id and marking_period_id = $mp_id");
            unset($mp_id);
    }
    
    if ($_REQUEST['modfunc']=='update' && !$_REQUEST['removemp']){
        
        if ($_REQUEST['new_sms']) {
            
            DBQuery("INSERT INTO STUDENT_MP_STATS (student_id, marking_period_id) VALUES ($student_id, ".$_REQUEST['new_sms'].")");
            $mp_id = $_REQUEST['new_sms'];
            
        }

        if ($_REQUEST['SMS_GRADE_LEVEL'] && $mp_id) {
            $updatestats = "UPDATE student_mp_stats SET grade_level_short = '".$_REQUEST['SMS_GRADE_LEVEL']."'
                            WHERE marking_period_id = $mp_id     
                            AND student_id = $student_id";
            DBQuery($updatestats);
        }    
        foreach($_REQUEST['values'] as $id=>$columns)
        {
            if($id!='new')
            {
                $sql = "UPDATE student_report_card_grades SET ";
                foreach($columns as $column=>$value)
                    $sql .= $column."='".str_replace("\'","''",$value)."',";
                if($_REQUEST['tab_id']!='new')
                    $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                else
                    $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                DBQuery($sql);
            }
            elseif($columns['COURSE_TITLE'])
            {
				if($columns['WEIGHTED_GP']!="") {
					$sql = 'INSERT INTO student_report_card_grades ';
					$fields = 'SCHOOL_ID, STUDENT_ID, MARKING_PERIOD_ID, ';
					$values = UserSchool().", $student_id, $mp_id, ";
					if(!$columns['GP_SCALE']) $columns['GP_SCALE'] = SchoolInfo('REPORTING_GP_SCALE');
					if(!$columns['CREDIT_ATTEMPTED']) $columns['CREDIT_ATTEMPTED'] = 1;
					if(!$columns['CREDIT_EARNED']){
						if($columns['UNWEIGHTED_GP'] > 0 || $columns['WEIGHTED_GP'] > 0) 
							$columns['CREDIT_EARNED'] = 1;
						else
							$columns['CREDIT_EARNED'] = 0;
					}
					if(!$columns['CLASS_RANK']) $columns['CLASS_RANK']='Y'; 
					
					$go = false;
					foreach($columns as $column=>$value)
						if($value)
						{
							$fields .= $column.',';
							$values .= '\''.str_replace("\'","''",$value).'\',';
							$go = true;
						}
					$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
	
					if($go && $mp_id && $student_id) DBQuery($sql);
				}
				else {
					BackPromptMsg(_('Field Required'),_('Enter Grade Points value'), $table);
				}
            }
        }
        unset($_REQUEST['modfunc']); 

    }
    if($_REQUEST['modfunc']=='remove')
    {
        if(DeletePromptX('Student Grade'))
        {
            DBQuery("DELETE FROM student_report_card_grades WHERE ID='$_REQUEST[id]'");
        }
    }    
    if(!$_REQUEST['modfunc']){    
        $stuRET = DBGet(DBQuery("SELECT LAST_NAME, FIRST_NAME, MIDDLE_NAME, NAME_SUFFIX from STUDENTS where STUDENT_ID = $student_id"));
        $stuRET = $stuRET[1];
        $displayname = $stuRET['LAST_NAME'].(($stuRET['NAME_SUFFIX'])?$stuRET['suffix'].' ':'').', '.$stuRET['FIRST_NAME'].' '.$stuRET['MIDDLE_NAME'];
       
       $gquery = "SELECT mp.syear, mp.marking_period_id as mp_id, mp.title as mp_name, mp.post_end_date as posted, sms.grade_level_short as grade_level, 
       CASE WHEN sms.gp_credits > 0 THEN (sms.sum_weighted_factors/sms.gp_credits)*s.reporting_gp_scale ELSE 0 END as weighted_gpa,
        sms.cum_weighted_factor*s.reporting_gp_scale as weighted_cum,
       CASE WHEN sms.gp_credits > 0 THEN (sms.sum_unweighted_factors/sms.gp_credits)*s.reporting_gp_scale ELSE 0 END as unweighted_gpa,
        sms.cum_unweighted_factor*s.reporting_gp_scale as unweighted_cum,
       CASE WHEN sms.cr_credits > 0 THEN (sms.cr_weighted_factors/cr_credits)*s.reporting_gp_scale ELSE 0 END as cr_weighted,
       CASE WHEN sms.cr_credits > 0 THEN (sms.cr_unweighted_factors/cr_credits)*s.reporting_gp_scale ELSE 0 END as cr_unweighted
       FROM MARKING_PERIODS mp, student_mp_stats sms, schools s
       WHERE sms.marking_period_id = mp.marking_period_id and
             s.id = mp.school_id and sms.student_id = $student_id
    AND mp.school_id = ".UserSchool()." GROUP BY mp_id, mp_name order by posted";
            
		//echo $gquery;
        $GRET = DBGet(DBQuery($gquery));
        
        $last_posted = null;
        $gmp = array(); //grade marking_periods
        $grecs = array();  //grade records
        if($GRET){
            foreach($GRET as $rec){
                if ($mp_id == null || $mp_id == $rec['MP_ID']){
                    $mp_id = $rec['MP_ID'];
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
						'cr_weighted'=>$rec['CR_WEIGHTED'],
						'cr_unweighted'=>$rec['CR_UNWEIGHTED'],
                                                'gpa'=>$rec['GPA']);
                }
                if ($mp_id != $rec['MP_ID']){
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
						'cr_weighted'=>$rec['CR_WEIGHTED'],
						'cr_unweighted'=>$rec['CR_UNWEIGHTED'],
                                                'gpa'=>$rec['GPA']);
                }    
            }
        } else {
            $mp_id = "0";
        }
        $mpselect = "<FORM action=Modules.php?modname=$_REQUEST[modname]&tab_id=".$_REQUEST['tab_id']." method=POST>";
        $mpselect .= "<SELECT name=mp_id onchange='document.forms[0].submit();'>";
        foreach ($gmp as $id=>$mparray){
            $mpselect .= "<OPTION value=".$id.(($id==$mp_id)?' SELECTED':'').">".$mparray['schoolyear'].' '.$mparray['mp_name'].', Grade '.$mparray['grade_level']."</OPTION>";
        }
        $mpselect .= "<OPTION value=0 ".(($mp_id=='0')?' SELECTED':'').">"._('Add another marking period')."</OPTION>";   
        $mpselect .= '</SELECT>';
        DrawHeader($mpselect);
        echo '</FORM>';
            
            
            
            
            
            
            //FORM for updates/new records
            echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&tab_id=$_REQUEST[tab_id]&mp_id=$mp_id method=POST>";
            DrawHeader('',SubmitButton(_('Save')));
            echo '<BR>';
            echo "<table cellpadding=5><tr><td colspan=3><b>$displayname</b></td></tr><tr><td colspan=3 align=\"center\">MARKING PERIOD STATISTICS</td></tr><tr><td>"._('GPA')."</td><td>WEIGHTED: ".sprintf('%0.3f',$gmp[$mp_id]['weighted_gpa'])."</td><td>UNWEIGHTED: ".sprintf('%0.3f',$gmp[$mp_id]['unweighted_gpa'])."</td></tr>";
	    echo "<tr><td>CLASS RANK GPA</td><td>WEIGHTED: ".sprintf('%0.3f',$gmp[$mp_id]['cr_weighted'])."</td><td>UNWEIGHTED: ".sprintf('%0.3f',$gmp[$mp_id]['cr_unweighted'])."</td></tr></table><br>";
            
            
            $sms_grade_level = TextInput($gmp[$mp_id]['grade_level'],"SMS_GRADE_LEVEL",_('Grade'),'size=3 maxlength=3');
            
            if ($mp_id=="0"){
                $syear = UserSyear();
                $sql = "SELECT MARKING_PERIOD_ID, SYEAR, TITLE, POST_END_DATE FROM MARKING_PERIODS WHERE SCHOOL_ID = ".UserSchool().
                        " AND SYEAR BETWEEN ".sprintf('%d',$syear-5)." AND $syear ORDER BY POST_END_DATE";
                $MPRET = DBGet(DBQuery($sql));
                if ($MPRET){
                    //$mpselect = "<SELECT name=new_sms>";
                    $mpoptions = array();
                    foreach ($MPRET as $id=>$mp){
                        //$mpselect .= "<OPTION value=".$mp['MARKING_PERIOD_ID'].">".formatSyear($mp['SYEAR']).' '.$mp['TITLE']."</OPTION>";
                        $mpoptions[$mp['MARKING_PERIOD_ID']] = formatSyear($mp['SYEAR']).' '.$mp['TITLE'];
                    } 
                    //$mpselect .= '</SELECT>';
                    //echo $mpselect;
                    echo "<TABLE><TR><TD>";
                    echo SelectInput(null,'new_sms',_('New Marking Period'),$mpoptions,false,null);
                    echo "</TD><TD>";
                    echo $sms_grade_level;
                    echo "</TD></TR></TABLE>";
                } 
                
            } else {
                echo $sms_grade_level;
                $tabs = array();
                $tabs[] = array('title'=>'Grades','link'=>"Modules.php?modname=$_REQUEST[modname]&tab_id=grades&mp_id=$mp_id");
                $tabs[] = array('title'=>'Credits','link'=>"Modules.php?modname=$_REQUEST[modname]&tab_id=credits&mp_id=$mp_id");
                echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&tab_id=$tab_id&mp_id=$mp_id").'</CENTER>';
                
                $sql = 'SELECT * FROM student_report_card_grades WHERE STUDENT_ID = '.$student_id.' AND MARKING_PERIOD_ID = '.$mp_id.' ORDER BY ID';
            
                //build forms based on tab selected
                if ($_REQUEST['tab_id']=='grades' || $_REQUEST['tab_id'] == ''){
                    $functions = array( 'COURSE_TITLE'=>'makeTextInput',
                                        'GRADE_PERCENT'=>'makeTextInput',
                                        'GRADE_LETTER'=>'makeTextInput',
                                        'WEIGHTED_GP'=>'makeTextInput',                  
                                        'UNWEIGHTED_GP'=>'makeTextInput',
                                        'GP_SCALE'=>'makeTextInput',
                                        );
                    $LO_columns = array('COURSE_TITLE'=>_('Course Name'),
                                        'GRADE_PERCENT'=>_('Percentage'),
                                        'GRADE_LETTER'=>_('Letter Grade'),
                                        'WEIGHTED_GP'=>_('Grade Points'),
                                        'UNWEIGHTED_GP'=>_('Unweighted Grade Points'),
                                        'GP_SCALE'=>_('Grade Scale'),
                                        );
                    $link['add']['html'] = array('COURSE_TITLE'=>makeTextInput('','COURSE_TITLE'),
                                        'GRADE_PERCENT'=>makeTextInput('','GRADE_PERCENT'),
                                        'GRADE_LETTER'=>makeTextInput('','GRADE_LETTER'),
                                        'WEIGHTED_GP'=>makeTextInput('','WEIGHTED_GP'),
                                        'UNWEIGHTED_GP'=>makeTextInput('','UNWEIGHTED_GP'),
                                        'GP_SCALE'=>makeTextInput('','GP_SCALE'),
                                        );
                } else {
                    $functions = array( 'COURSE_TITLE'=>'makeTextInput',
                                        'CREDIT_ATTEMPTED'=>'makeTextInput',
                                        'CREDIT_EARNED'=>'makeTextInput',
                                        'CREDIT_CATEGORY'=>'makeTextInput',
					'CLASS_RANK'=>'makeCheckBoxInput'
                                        );
                    $LO_columns = array('COURSE_TITLE'=>_('Course Name'),
                                        'CREDIT_ATTEMPTED'=>_('Credit Attempted'),
                                        'CREDIT_EARNED'=>_('Credit Earned'),
                                        'CREDIT_CATEGORY'=>_('Credit Category'),
					'CLASS_RANK'=>_('Affects Class Rank')
                                        );
                    $link['add']['html'] = array('COURSE_TITLE'=>makeTextInput('','COURSE_TITLE'),
                                        'CREDIT_ATTEMPTED'=>makeTextInput('','CREDIT_ATTEMPTED'),
                                        'CREDIT_EARNED'=>makeTextInput('','CREDIT_EARNED'),
                                        'CREDIT_CATEGORY'=>makeTextInput('','CREDIT_CATEGORY'),
					'CLASS_RANK'=>makeTextInput('','CLASS_RANK')
                                        );
                                        
                }
                //$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&table=student_report_card_grades";
                //$link['remove']['variables'] = array('id'=>'ID');
                $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&mp_id=$mp_id";
                $link['remove']['variables'] = array('id'=>'ID');
                $link['add']['html']['remove'] = button('add');
                $LO_ret = DBGet(DBQuery($sql),$functions);
                ListOutput($LO_ret,$LO_columns,'.','.',$link,array(),array('count'=>false,'download'=>false,'search'=>false));
            }
            echo '<CENTER>';
            if (!$LO_ret){
                echo SubmitButton(_('Remove Marking Period'), 'removemp');
            }
            echo SubmitButton(_('Save')).'</CENTER>';
            echo '</FORM>';
    }
}
function makeTextInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else
        $id = 'new';
//    //bjj adding 'GP_SCALE'
    if($name=='COURSE_TITLE')
        $extra = 'size=25 maxlength=25';
    elseif($name=='GRADE_PERCENT')
        $extra = 'size=6 maxlength=6';
    elseif($name=='GRADE_LETTER' || $name=='WEIGHTED_GP' || $name=='UNWEIGHTED_GP')
        $extra = 'size=5 maxlength=5';
    elseif($name=='CLASS_RANK')
	$extra = 'size=1 maxlength=1';
    //elseif($name=='GP_VALUE')
    //    $extra = 'size=5 maxlength=5';
    //elseif($name=='UNWEIGHTED_GP_VALUE')
        
    else
    $extra = 'size=10 maxlength=10';

    return TextInput($value,"values[$id][$name]",'',$extra);
}

function makeCheckBoxInput($value, $name){
    global $THIS_RET;
    
    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else
        $id = 'new';
    
    return CheckBoxInput($value, "values[$id][$name]",'','');
    
}

function formatSyear($value){
    return substr($value,2).'-'.substr($value+1,2);
}
?>
