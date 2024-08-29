ALTER TABLE `history_marking_periods` CHANGE `parent_id` `parent_id` INT( 32 ) NOT NULL; 
ALTER TABLE `history_marking_periods` DROP PRIMARY KEY;
SELECT @marking_period_id:=0;
UPDATE history_marking_periods
SET marking_period_id = (@marking_period_id:=@marking_period_id+1)
ORDER BY parent_id;
ALTER TABLE `history_marking_periods` ADD PRIMARY KEY ( `marking_period_id` );
ALTER TABLE `history_marking_periods` CHANGE `marking_period_id` `marking_period_id` INT( 32 ) NOT NULL AUTO_INCREMENT;
