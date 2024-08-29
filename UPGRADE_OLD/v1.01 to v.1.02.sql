alter table staff alter column staff_id set NOT NULL;
alter table staff add primary key (staff_id);
alter table students add primary key (student_id);
alter table student_enrollment alter column id set not null;
alter table student_enrollment add primary key (id);
alter table students_join_address add primary key (id);
alter table custom alter column student_id set not null;
alter table custom add primary key (student_id);
alter table address add primary key (address_id);
alter table students_join_people add primary key (id);
create index relations_meets_6 on STUDENTS_JOIN_PEOPLE (CUSTODY,EMERGENCY);
alter table people add primary key (person_id);
alter table courses alter column course_id set not null;
alter table courses alter column syear set not null;
alter table courses alter column subject_id set not null;
alter table courses alter column school_id set not null;
alter table courses add primary key (course_id);
create index courses_ind2 on courses (subject_id);
alter table course_periods alter column syear set not null;
alter table course_periods alter column school_id set not null;
alter table course_periods alter column course_period_id set not null;
alter table course_periods alter column course_id set not null;
alter table course_periods alter column course_weight set not null;
alter table course_periods add primary key (course_period_id);
alter table schedule alter column syear set not null;
alter table schedule alter column student_id set not null;
alter table schedule alter column course_id set not null;
alter table schedule alter column course_weight set not null;
alter table schedule alter column course_period_id set not null;
alter table schedule add primary key (syear,student_id,course_id,course_weight,course_period_id);
create index schedule_ind4 on schedule (syear,school_id);
drop index schedule_ind1;
create index schedule_ind1 on schedule (course_id,course_weight);
create unique index staff_ind4 on staff (username,syear);
alter table schedule_requests alter column request_id set NOT NULL;
alter table schedule_requests add primary key (request_id);
alter table course_subjects alter column subject_id set not null;
alter table course_subjects add primary key (subject_id);
create index course_subjects_ind1 on course_subjects (syear,school_id,subject_id);
alter table course_weights alter column course_weight set not null;
alter table course_weights alter column course_id set not null;
alter table course_weights add primary key (course_id,course_weight);
create index attendance_period_ind4 on attendance_period (school_date);
drop index attendance_period_ind1;
create index attendance_period_ind1 on attendance_period (student_id);
alter table attendance_period alter column student_id set not null;
alter table attendance_period alter column school_date set not null;
alter table attendance_period alter column period_id set not null;
alter table attendance_period add primary key (student_id,school_date,period_id);
create index attendance_period_ind5 on attendance_period (attendance_code);	
alter table attendance_codes ALTER column id set not null;
alter table attendance_codes add primary key (id);
alter table attendance_calendar alter column syear set not null;
alter table attendance_calendar alter column school_id set not null;
alter table attendance_calendar alter column school_date set not null;
alter table attendance_calendar add primary key (school_id,school_date);
alter table attendance_day alter column student_id set not null;
alter table attendance_day alter column school_date set not null;
alter table attendance_day add primary key (student_id,school_date);
drop INDEX attendance_day_ind1;
DROP INDEX address_1;
drop INDEX stu_addr_meets_5;
drop index people_2;
drop INDEX attendance_codes_ind1;
alter table school_gradelevels alter column id set not null;
alter table school_gradelevels add primary key (id);        
create index school_gradelevels_ind1 on school_gradelevels (school_id);
alter table schools alter column id set not null;
alter table schools add primary key (id);
create index schools_ind1 on schools (syear);
alter table school_periods alter column period_id set not null;
alter table school_periods add primary key (period_id);
alter table attendance_completed ALTER column staff_id set not null;
alter table attendance_completed ALTER column school_date set not null;
alter table attendance_completed ALTER column period_id set not null;
alter table attendance_completed add primary key (staff_id,school_date,period_id);
alter table eligibility_completed ALTER column staff_id set not null;
alter table eligibility_completed ALTER column school_date set not null;
alter table eligibility_completed ALTER column period_id set not null;
alter table eligibility_completed add primary key (staff_id,school_date,period_id);
alter table grades_completed ALTER column staff_id set not null;
alter table grades_completed ALTER column marking_period_id set not null;
alter table grades_completed ALTER column period_id set not null;
alter table grades_completed add primary key (staff_id,marking_period_id,period_id);
alter table students_join_users alter column student_id set not null;
alter table students_join_users alter column staff_id set not null;
alter table students_join_users add primary key (student_id,staff_id);
alter table custom_fields alter column id set not null;
alter table custom_fields add primary key (id);
alter table school_semesters alter column marking_period_id set not null;
alter table school_quarters alter column marking_period_id set not null;
alter table school_progress_periods alter column marking_period_id set not null;
alter table school_semesters add primary key (marking_period_id);
drop index school_semesters_ind1;
alter table school_quarters add primary key (marking_period_id);
drop index school_quarters_ind1;
create index school_quarters_ind1 on school_quarters (semester_id);
alter table school_progress_periods add primary key (marking_period_id);
drop index school_progress_periods_ind1;
create index school_progress_periods_ind1 on school_progress_periods (quarter_id);
alter table student_report_card_grades alter column syear set not null;
alter table student_report_card_grades alter column student_id set not null;
alter table student_report_card_grades alter column course_period_id set not null;
alter table student_report_card_grades alter column marking_period_id set not null;
alter table student_report_card_grades add primary key (syear,student_id,course_period_id,marking_period_id);
create index student_report_card_grades_ind1 on student_report_card_grades (school_id);
alter table student_gpa_calculated ALTER column student_id set not null;
alter table student_gpa_calculated ALTER column marking_period_id set not null;
alter table student_gpa_calculated ALTER column school_id set not null;
alter table student_gpa_calculated add primary key (student_id,marking_period_id,school_id);
alter table student_gpa_running ALTER column student_id set not null;
alter table student_gpa_running ALTER column marking_period_id set not null;
alter table student_gpa_running ALTER column school_id set not null;
alter table student_gpa_running add primary key (student_id,marking_period_id,school_id);
create index student_gpa_calculated_ind1 on student_gpa_calculated (syear);
create index student_gpa_running_ind1 on student_gpa_running (syear);
alter table people_join_contacts alter column id set not null;
alter table people_join_contacts add primary key (id);
create index people_join_contacts_ind1 on people_join_contacts (person_id);
alter table calendar_events alter column id set not null;
alter table calendar_events add primary key (id);
create index program_config_ind1 on program_config (program,school_id,syear); 
alter table gradebook_grades alter column student_id set not null;
alter table gradebook_grades alter column assignment_id set not null;
alter table gradebook_grades add primary key (student_id,assignment_id);
create index gradebook_grades_ind1 on gradebook_grades (assignment_id);
alter table gradebook_assignments alter column assignment_id set not null;
alter table gradebook_assignments add primary key (assignment_id);
create index gradebook_assignments_ind1  on gradebook_assignments (staff_id,marking_period_id);
create INDEX gradebook_assignments_ind2 on gradebook_assignments (course_id,course_period_id);
create INDEX gradebook_assignments_ind3 on gradebook_assignments (assignment_type_id);
alter table gradebook_assignment_types alter column assignment_type_id set not null;
alter table gradebook_assignment_types add primary key (assignment_type_id);
create index gradebook_assignment_types_ind1 on gradebook_assignments (staff_id,course_id);
alter table report_card_grades alter column id set not null;
alter table report_card_grades add primary key (id);
create index report_card_grades_ind1 on report_card_grades (syear,school_id);
alter table report_card_comments alter column id set not null;
alter table report_card_comments add primary key (id);
create index report_card_comments_ind1 on report_card_comments (syear,school_id);
create index student_eligibility_activities_ind1 on student_eligibility_activities (student_id);
create index eligibility_ind1 on eligibility (student_id,course_period_id,school_date);
alter table eligibility_activities alter column id set not null;
alter table eligibility_activities add primary key (id);
create index eligibility_activities_ind1 on eligibility_activities (school_id,syear);
alter table student_medical alter column id set not null;
alter table student_medical add primary key (id); 
create index student_medical_ind1 on student_medical (student_id);
alter table student_medical_alerts alter column id set not null;
alter table student_medical_alerts add primary key (id); 
create index student_medical_alerts_ind1 on student_medical_alerts (student_id);
VACUUM;
ANALYZE;