<?php
$menu['Admin']['admin'] = array(
						1=>'Settings',
						'Admin/Settings.php'=>_('Site Configuration'),
						2=>'School Setup',
						'Admin/Schools.php?new_school=true'=>_('Add a School'),
						'Admin/CopySchool.php'=>_('Copy School'),
						'Admin/Rollover.php'=>_('Rollover'),
						3=>'Students',
						'Admin/StudentFields.php'=>_('Student Fields'),
						'Admin/AddressFields.php'=>_('Address Fields'),
						'Admin/PeopleFields.php'=>_('Contact Fields'),
						'Admin/EnrollmentCodes.php'=>_('Enrollment Codes'),
						'Admin/Ethnicity.php'=>_('Ethnicity Report'),
						'Admin/DeleteStudents.php'=>_('Delete Students'),
						4=>'Users',
						'Admin/Profiles.php'=>_('User Profiles'),
						'Admin/Exceptions.php'=>_('User Permissions'),
						'Admin/UserFields.php'=>_('User Fields'),
						5=>'Schedule',
						'Admin/Scheduler.php'=>_('Run Scheduler'),
						6=>'Grades',
						'Admin/CalcGPA.php'=>_('Calculate GPA'),
						'Admin/ReportCardGrades.php'=>_('Grading Scales'),
						'Admin/ReportCardComments.php'=>_('Report Card Comments'),
                        'Admin/ReportCardCommentCodes.php'=>_('Comment Codes'),
                        'Admin/EditHistoryMarkingPeriods.php'=>_('History Marking Periods'),
                        7=>'Attendance',
                        'Admin/AttendanceCodes.php'=>_('Attendance Codes'),
                        8=>_('Utilities'),
						'Admin/FixDailyAttendance.php'=>_('Recalculate Daily Attendance'),
						'Admin/DuplicateAttendance.php'=>_('Delete Duplicate Attendance'),
                        9=>'Eligibility',
                        'Admin/EntryTimes.php'=>_('Entry Times'),
                        10=>'Food Service',
                        'Admin/DailyMenus.php'=>_('Daily Menus'),
						'Admin/MenuItems.php'=>_('Meal Items'),
						'Admin/Menus.php'=>_('Meals'),
						'Admin/Kiosk.php'=>_('Kiosk Preview'),
						11=>'Discipline',
						'Admin/ReferralForm.php'=>_('Referral Form'),
						12=>'Coodle',
						'Admin/ImportDataToCoodle.php'=>_('Import Existing Data to Moodle'),
						13=>'Notes',
						'Admin/MyNotes.php'=>_('Admin Notes')
					);

$exceptions['Admin'] = array(
						'Admin/Schools.php?new_school=true'=>true,
						'Admin/Rollover.php'=>true
					);
?>