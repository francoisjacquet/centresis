<?php
error_reporting(1);
include "./Warehouse.php";
  
  //$q = 'SELECT \'248162\' AS "SchoolCodeNumber", s.student_id AS "SchoolStudentID", s.first_name AS "FirstName", s.last_name AS "LastName", sg.short_name AS "GradeLevelCode", (SELECT PHONE_1 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1) AS "PHONE_1", (SELECT PHONE_2 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1) AS "PHONE_2", (SELECT PHONE_3 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1) AS "PHONE_3", (SELECT PHONE_4 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1) AS "PHONE_4", (SELECT PHONE_1 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1 OFFSET 1) AS "PHONE_5", (SELECT PHONE_2 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1 OFFSET 1) AS "PHONE_6", (SELECT EMAIL_1 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1) AS "EMAIL_1", (SELECT EMAIL_2 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1) AS "EMAIL_2", (SELECT EMAIL_3 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1) AS "EMAIL_3", (SELECT EMAIL_1 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1 OFFSET 1) AS "EMAIL_4", (SELECT EMAIL_2 FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE sjp.student_id=s.student_id AND sjp.custody=\'Y\' AND p.PERSON_ID=sjp.PERSON_ID LIMIT 1 OFFSET 1) AS "EMAIL_5", s.custom_100000019 AS "EnrollmentTypeCode" FROM students s, school_gradelevels sg, student_enrollment e, schools sc WHERE sc.SYEAR=2012 AND sc.ID=12 AND e.SYEAR=sc.SYEAR AND e.SCHOOL_ID=sc.ID AND s.STUDENT_ID=e.STUDENT_ID AND sg.id = e.grade_id AND e.START_DATE<=CURRENT_DATE AND (e.END_DATE IS NULL OR e.END_DATE>=CURRENT_DATE)';
  $q = "SELECT * FROM STAFF LIMIT 20";
  $result = DBQuery($q);

$fp = fopen('php://output', 'w');
if ($fp && $result) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export.csv"');
	$n=0;

	// fetch a row and write the column names out to the file
	$row = mysql_fetch_assoc($result);
	$line = "";
	$comma = "";
	foreach($row as $name => $value) {
		$line .= $comma . '"' . str_replace('"', '""', ucwords(str_replace("_", " ", $name))) . '"';
		$comma = ",";
	}
	echo $line .= "\n";
	fputcsv($fp, $line);
	
	// remove the result pointer back to the start
	mysql_data_seek($result, 0);
	
	while ($row = mysql_fetch_array($result, MYSQLI_NUM)) {
		$total = count($row);
		$line = "";
		$comma = "";	
		while($total > $n) {
			//echo $row[$n].', <br>';
			$line .= $comma . '"' . str_replace('"', '""', $row[$n]) . '"';
			$comma = ",";			
		$n++;
		}
		echo $line .= "\n";
		fputcsv($fp, $line);
	$n=0;
    }

    die;
}
?>