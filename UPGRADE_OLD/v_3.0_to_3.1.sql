-- School period start and end times
ALTER TABLE school_periods
    ALTER COLUMN start_time TYPE character varying(10);
ALTER TABLE school_periods
    ALTER COLUMN end_time TYPE character varying(10);

-- People name fields to length 50
ALTER TABLE people
    ALTER COLUMN first_name TYPE character varying(50);
ALTER TABLE people
    ALTER COLUMN middle_name TYPE character varying(50);
ALTER TABLE people
    ALTER COLUMN last_name TYPE character varying(50);

-- Short name for grade levels to length 5
DROP VIEW enroll_grade;
ALTER TABLE school_gradelevels
    ALTER COLUMN short_name TYPE character varying(5);
CREATE OR REPLACE VIEW enroll_grade AS 
 SELECT e.id, e.syear, e.school_id, e.student_id, e.start_date, e.end_date, sg.short_name, sg.title
 FROM student_enrollment e, school_gradelevels sg
 WHERE e.grade_id = sg.id;