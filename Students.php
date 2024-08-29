<?php
error_reporting(1);
include "./Warehouse.php";
$_REQUEST['_CENTRE_PDF'] = true;

$schoolid = '1';
$semnumber = substr(GetMP(GetCurrentMP('SEM',date("Y-m-d")),'SHORT_NAME'), 1, 1);
    
$reports = array();
$reports['students'] = array('filename' => 'students', 'sql' => "SELECT
CONCAT(substr(s.custom_200000003,1,3),' ',substr(s.custom_200000003,5,2),' ',substr(s.custom_200000003,8,4)) AS SSN
,s.student_id AS SID
,s.first_name AS FIRST_NAME
,s.last_name AS LAST_NAME
,substr(s.middle_name,1, 1) AS MI
,s.name_suffix AS SUFFIX
,a.address AS ADDRESS_LINE_1
,'' AS ADDRESS_LINE_2
,a.city AS CITY
,a.state AS STATE
,a.zipcode AS ZIP
,a.plus4 AS ZIP4
,DATE_FORMAT(s.custom_200000004, '%d/%e/%Y') AS DOB
,substr(a.phone,1,12) AS TELEPHONE
,s.custom_11 AS EMAIL
,substr(s.custom_200000000, 1, 1) AS GENDER
,gl.short_name AS GRADUATION_YEAR
,'S' AS MEMBER_TYPE
,'' AS HOMEROOM
,p.first_name||' '||p.last_name AS PARENT_GUARDIAN
,'".$schoolid."' AS SCHOOLNUM
,'' AS SPECIAL_CARD_VALUE
,'' AS ACADEMY_CODE
,'' AS GROUPz
,'' AS LUNCH_CODE
,'' AS VENDOR_ID
,'' AS FOOD_ALLERGY
,'' AS SPECIAL_CARD_VALUE2
,'' AS LOCATION
FROM STUDENT_ENROLLMENT en join SCHOOL_GRADELEVELS gl on (en.grade_id = gl.id) join STUDENTS s on (en.student_id = s.student_id)
  join STUDENTS_JOIN_ADDRESS sja on (en.student_id = sja.student_id and sja.residence = 'Y')
  join ADDRESS a on (sja.address_id = a.address_id)
  join 
    (SELECT student_id, address_id, min(person_id) as person_id from students_join_people group by student_id, address_id ) as sjp
    on (sjp.student_id = en.student_id and sjp.address_id = sja.address_id)
  join people p on p.person_id = sjp.person_id
WHERE en.START_DATE <= NOW() AND (en.END_DATE >= NOW() OR en.END_DATE IS NULL)
AND en.SYEAR = 2011 ORDER BY s.student_id;"); echo $reports['students']['sql'];

$reports['student_schedules'] = array('filename' => 'student_schedules', 'sql' => "select sch.student_id as SID, cp.short_name as COURSEID
from schedule sch join course_periods cp on sch.course_period_id = cp.course_period_id join courses c on c.course_id = cp.course_id join course_subjects cs on cs.subject_id = c.subject_id
join student_enrollment en on (en.student_id = sch.student_id and en.syear = sch.syear)
where cs.title = 'Lunch' and
sch.syear = '2011' and
sch.start_date <= now() and (sch.end_date >= now() or sch.end_date is null)
and en.START_DATE <= NOW() AND (en.END_DATE >= NOW() OR en.END_DATE IS NULL);");

$reports['teachers'] = array('filename' => 'teachers', 'sql' => "select distinct '".$schoolid."' as SCHOOLID, cp.teacher_id as CODE, s.last_name || ', ' || s.first_name as NAME from course_subjects cs
join courses c on c.subject_id = cs.subject_id
join course_periods cp on cp.course_id = c.course_id
join staff s on s.staff_id = cp.teacher_id
where cs.syear = 2011 and cs.title = 'Lunch';");

$reports['class schedules'] = array('filename'=>'class_schedules', 'sql'=>"select '".$schoolid."' as SCHOOL_ID
,'".$semnumber."' as SEMESTER
,cp.room AS ROOM
,sp.short_name as PERIOD
,CASE WHEN d.day = 'H' THEN 'R'
     ELSE d.day
END as day
,c.short_name as NAME
,cp.short_name as COURSEID
,teacher_id AS TEACHER_CODE
 from course_subjects cs
join courses c on c.subject_id = cs.subject_id
join course_periods cp on cp.course_id = c.course_id
join school_periods sp on sp.period_id = cp.period_id
join (select 7 as id, 'S' as day 
union select 6, 'F'
union select 5, 'H'
union select 4, 'W'
union select 3, 'T'
union select 2, 'M'
union select 1, 'U') d on (cp.days LIKE '%'||d.day||'%')
where cs.syear = 2011 and cs.title = 'Lunch' ;");


DrawHeader(ProgramTitle());
foreach($reports as $key=>$report){
  $query = $report['sql'];
  $RET = DBGet(DBQuery($query));

  echo sizeof($RET).' result(s) found.<br />';
  //$fields = array();

  $fields = array();
  foreach(array_keys($RET[1]) as $key)
    $fields[]= array('Name'=>$key);  

  // List of available reporting formats
  $ExportFormats = array('TSV' => array('type'=>'TXT', 'quotes'=> false, 'delim'=> "\t", 'header' => true, 'errchar' => '!' ));
  $format = 'TSV';


  $fileurl = ExportData( $report['filename'], $RET, $fields, $ExportFormats[$format] );
  // Display the link to the exported file
  echo '</p>Done. <a href="'.$fileurl.'">'.substr(strrchr($fileurl,'/'),1).'</a></p>';
}
/*function XMLClosingTag( $tag ) {
    // Get the inside of the tag
    $inside = substr($tag,1,-1);
    // Get the first word of the inside
    if (strpos($inside,' ') !== false)
        $inside = substr($inside,0,strpos($inside,' '));
    // Return closing tag
    return '</'.$inside.'>';
}  */

function ExportData( $filename, $data, $fields, $format ) {
    $filename = 'Output/'.$filename.'.'.strtolower($format['type']);
    // Open file for writing
    if (($fp = fopen(dirname(__FILE__).'/'.$filename,'w')) == NULL)
        die('Unable to create file '.$filename);

    switch ($format['type']) {
        case 'CSV':
            // Default options
            if (!isset($format['EOL'])) $format['EOL'] = PHP_EOL;
            // Write header
            if ($format['header']) {
                $line = '';
                foreach ($fields as $field)
                    $line .= ($field['Field Name']?$field['Field Name']:$field['Name']).$format['delim'];
                $line = substr($line,0,-1).PHP_EOL;
                fwrite( $fp, $line );        
            }

            // Write one row after another
            $firstline = true;
            if (sizeof($data)> 0) foreach ($data as $key => $values) {
                $line = '';
                foreach ($fields as $field)
                    $line .= ($format['quotes']?'"':'').$values[$field['Name']].($format['quotes']?'"':'').$format['delim'];
                $line = ($firstline?'':$format['EOL']).substr($line,0,-1*strlen($format['delim']));
                $firstline = false;
                fwrite( $fp, $line );
            }
            break;
        case 'TXT':
            // Default options
            if (!isset($format['EOL'])) $format['EOL'] = PHP_EOL;
            // Write header
            if ($format['header']) {
                $line = '';
                foreach ($fields as $field)
                    $line .= ($field['Field Name']?$field['Field Name']:$field['Name']).$format['delim'];
                $line = substr($line,0,-1).PHP_EOL;
                fwrite( $fp, $line );        
            }

            // Write one row after another
            $firstline = true;
            if (sizeof($data)> 0) foreach ($data as $key => $values) {
                $line = '';
                foreach ($fields as $field)
                    $line .= ($format['quotes']?'"':'').$values[$field['Name']].($format['quotes']?'"':'').$format['delim'];
                $line = ($firstline?'':$format['EOL']).substr($line,0,-1*strlen($format['delim']));
                $firstline = false;
                fwrite( $fp, $line );
            }
            break;
        case 'XML':
            fwrite( $fp, '<?xml version="1.0"'.($format['encoding']?' encoding="'.$format['encoding'].'"':'').'?>'.PHP_EOL );
            $tabs = '';
            // Write header
            if (!empty($format['header'])) {
                fwrite( $fp, $format['header'].PHP_EOL );
                $tabs .= '  ';
            }

            // Write one row after another
            if (sizeof($data)> 0) foreach ($data as $key => $values) {
                if (!empty($format['record'])) {
                    fwrite($fp, $tabs.$format['record'].PHP_EOL);
                    $tabs .= '  ';
                }
                $section = '';
                foreach ($fields as $field) {
                    // If field is not present in result set (which is NOT has a NULL value)
                    if (!isset($values[$field['Name']])) continue;
                    if ((!empty($field['Section'])) && ($section!=$field['Section'])) {
                        if (!empty($section)) {
                            $tabs = substr($tabs,0,-2);
                            fwrite($fp, $tabs.'</'.$section.'>'.PHP_EOL);
                        }
                        $section = $field['Section'];
                        fwrite($fp, $tabs.'<'.$section.'>'.PHP_EOL);
                        $tabs .= '  ';
                    }
                    if($values[$field['Name']] != '') { // do NOT use empty() since numeric 0 needs to appear in XML
                        if (is_array($values[$field['Name']])) {  // Multi-valued fields (MSDS)
                            foreach ($values[$field['Name']] as $value)
                                fwrite($fp, $tabs.'<'.$field['Name'].'>'.$value.'</'.$field['Name'].'>'.PHP_EOL);
                        } else
                            fwrite($fp, $tabs.'<'.$field['Name'].'>'.$values[$field['Name']].'</'.$field['Name'].'>'.PHP_EOL);
                    } else if ($format['empties'])
                        fwrite($fp, $tabs.'<'.$field['Name'].' />'.PHP_EOL);                        
                }
                if (!empty($section)) {
                    $tabs = substr($tabs,0,-2);
                    fwrite($fp, $tabs.'</'.$section.'>'.PHP_EOL);
                }
                if (!empty($format['record'])) {
                    $tabs = substr($tabs,0,-2);
                    fwrite( $fp, $tabs.XMLClosingTag($format['record']).PHP_EOL);
                }
            }

            if (!empty($format['header'])) {
                $tabs = substr($tabs,0,-2);
                fwrite( $fp, $tabs.XMLClosingTag($format['header']).PHP_EOL );
            }
            break;
        default:
            die('Unknown file export format: '.$format['type']);
    }
    fclose($fp);

    // Build the URL to the generated file    
    $fileurl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://');
    $fileurl .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/modules/Attendance/' . $filename;

    return $fileurl;
}
?>
