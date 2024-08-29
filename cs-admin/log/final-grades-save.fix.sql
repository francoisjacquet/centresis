ALTER TABLE `student_report_card_grades` CHANGE `weighted_gp` `weighted_gp` DOUBLE NULL DEFAULT '0',
CHANGE `unweighted_gp` `unweighted_gp` DOUBLE NULL DEFAULT '0',
CHANGE `gp_scale` `gp_scale` DOUBLE NULL DEFAULT '0',
CHANGE `credit_attempted` `credit_attempted` DOUBLE NULL DEFAULT '0',
CHANGE `credit_earned` `credit_earned` DOUBLE NULL DEFAULT '0'