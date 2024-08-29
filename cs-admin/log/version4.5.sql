ALTER TABLE `people` ADD `custom_3` CHAR( 1 ) NOT NULL ,
ADD `custom_4` CHAR( 1 ) NOT NULL ,
ADD `custom_5` CHAR( 1 ) NOT NULL ,
ADD `custom_6` CHAR( 1 ) NOT NULL ,
ADD `custom_7` CHAR( 1 ) NOT NULL ,
ADD `custom_8` CHAR( 1 ) NOT NULL ,
ADD `custom_9` CHAR( 1 ) NOT NULL ,
ADD `custom_10` CHAR( 1 ) NOT NULL ,
ADD `custom_11` CHAR( 1 ) NOT NULL ,
ADD `custom_12` CHAR( 1 ) NOT NULL ,
ADD `custom_13` CHAR( 1 ) NOT NULL ,
ADD `custom_14` CHAR( 1 ) NOT NULL ,
ADD `custom_15` CHAR( 1 ) NOT NULL ,
ADD `custom_16` CHAR( 1 ) NOT NULL ,
ADD `custom_17` CHAR( 1 ) NOT NULL ,
ADD `custom_18` CHAR( 1 ) NOT NULL ,
ADD `custom_19` CHAR( 1 ) NOT NULL ,
ADD `custom_20` CHAR( 1 ) NOT NULL ,
ADD `custom_21` CHAR( 1 ) NOT NULL ,
ADD `custom_22` CHAR( 1 ) NOT NULL ;

INSERT INTO `people_field_categories` (`id`, `title`, `sort_order`, `custody`, `emergency`) VALUES
(4, 'General Info', NULL, NULL, NULL);

INSERT INTO `people_fields` (`id`, `type`, `search`, `title`, `sort_order`, `select_options`, `category_id`, `system_field`, `required`, `default_selection`) VALUES
(3, 'radio', NULL, '1st', NULL, '1st', 4, NULL, NULL, NULL),
(4, 'radio', NULL, '2nd', NULL, '2nd', 4, NULL, NULL, NULL),
(5, 'radio', NULL, '3rd', NULL, '3rd', 4, NULL, NULL, NULL),
(6, 'radio', NULL, '4th', NULL, '4th', 4, NULL, NULL, NULL),
(7, 'radio', NULL, '5th', NULL, '5th', 4, NULL, NULL, NULL),
(8, 'radio', NULL, '6th', NULL, '6th', 4, NULL, NULL, NULL),
(9, 'radio', NULL, '7th', NULL, '7th', 4, NULL, NULL, NULL),
(10, 'radio', NULL, '8th', NULL, '8th', 4, NULL, NULL, NULL),
(11, 'radio', NULL, 'Donor', NULL, 'Donor', 4, NULL, NULL, NULL),
(12, 'radio', NULL, 'TK', NULL, 'TK', 4, NULL, NULL, NULL),
(13, 'radio', NULL, 'KDG', NULL, 'KDG', 4, NULL, NULL, NULL),
(14, 'radio', NULL, 'alum', NULL, NULL, 4, NULL, NULL, NULL),
(15, 'radio', NULL, 'Board Member', NULL, NULL, 4, NULL, NULL, NULL),
(16, 'radio', NULL, 'Federation/Clergy', NULL, NULL, 4, NULL, NULL, NULL),
(17, 'radio', NULL, 'Grandparents', NULL, NULL, 4, NULL, NULL, NULL),
(18, 'radio', NULL, 'Inactive', NULL, NULL, 4, NULL, NULL, NULL),
(19, 'radio', NULL, 'Misc. Supporter', NULL, NULL, 4, NULL, NULL, NULL),
(20, 'radio', NULL, 'Parent', NULL, NULL, 4, NULL, NULL, NULL),
(21, 'radio', NULL, 'Parent Alum', NULL, NULL, 4, NULL, NULL, NULL),
(22, 'radio', NULL, 'PT Officer', NULL, NULL, 4, NULL, NULL, NULL);


UPDATE people ppl, (SELECT students.student_id, CONCAT(students.last_name,' ',students.first_name) AS STUDENT_NAME, staff.staff_id, CONCAT(staff.last_name,' ', staff.first_name) AS PARENT_NAME, staff.username AS UNAME, staff.password AS PWORD, staff.syear, staff.profile_id AS PROFILEID, people.person_id AS PID, CONCAT(people.last_name, ' ', people.first_name) AS PERSON_NAME, students_join_people.address_id, students_join_people.custody, students_join_people.student_relation, staff.custom_28, staff.custom_29, staff.custom_30, staff.custom_31, staff.custom_32, staff.custom_33, staff.custom_35, staff.custom_36, staff.custom_10, staff.custom_27, staff.custom_34, staff.custom_6, staff.custom_7, staff.custom_8, staff.custom_13, staff.custom_20, staff.custom_9, staff.custom_19, staff.custom_11, staff.custom_23 FROM student_enrollment INNER JOIN (((staff INNER JOIN (students_join_users INNER JOIN students ON students_join_users.student_id = students.student_id) ON staff.staff_id = students_join_users.staff_id) INNER JOIN students_join_people ON students.student_id = students_join_people.student_id) INNER JOIN people ON students_join_people.person_id = people.person_id) ON student_enrollment.student_id = students_join_people.student_id WHERE staff.syear=2013 AND student_enrollment.syear = staff.syear AND students_join_people.custody = 'Y' AND students_join_people.address_id IS NOT NULL AND people.first_name LIKE CONCAT('%',staff.first_name,'%') AND people.last_name LIKE CONCAT('%',staff.last_name,'%') AND staff.profile = 'parent' GROUP BY students_join_people.address_id, students_join_people.student_relation ORDER BY students.student_id ASC) upd
SET ppl.username = upd.UNAME, ppl.password = upd.PWORD, ppl.profile_id = upd.PROFILEID, ppl.custom_3 = upd.custom_28, ppl.custom_4 = upd.custom_29, ppl.custom_5 = upd.custom_30, ppl.custom_6 = upd.custom_31, ppl.custom_7 = upd.custom_32, ppl.custom_8 = upd.custom_33, ppl.custom_9 = upd.custom_35, ppl.custom_10 = upd.custom_36, ppl.custom_11 = upd.custom_10, ppl.custom_12 = upd.custom_27, ppl.custom_13 = upd.custom_34, ppl.custom_14 = upd.custom_6, ppl.custom_15 = upd.custom_7, ppl.custom_16 = upd.custom_8, ppl.custom_17 = upd.custom_13, ppl.custom_18 = upd.custom_20, ppl.custom_19 = upd.custom_9, ppl.custom_20 = upd.custom_19, ppl.custom_21 = upd.custom_11, ppl.custom_22 = upd.custom_23
WHERE ppl.person_id = upd.PID;
