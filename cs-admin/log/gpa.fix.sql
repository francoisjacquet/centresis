DELIMITER $$

DROP FUNCTION IF EXISTS credit $$

DROP FUNCTION IF EXISTS calc_cum_cr_gpa $$
DROP PROCEDURE IF EXISTS calc_cum_cr_gpa $$
CREATE PROCEDURE calc_cum_cr_gpa(mp_id CHAR(20), s_id INT) 
BEGIN
    UPDATE student_mp_stats smstarget
    INNER JOIN
    (
        SELECT student_id, marking_period_id, (sum((tg.weighted_gp/tg.gp_scale)*tg.credit_attempted)/sum(tg.credit_attempted)) as weighted_gpa,
        (sum((tg.unweighted_gp/tg.gp_scale)*tg.credit_attempted)/sum(tg.credit_attempted)) as unweighted_gpa
        
        FROM transcript_grades tg INNER JOIN (
          SELECT weighted_gp, unweighted_gp, gp_scale, credit_attempted, credit_earned, school_scale 
          FROM transcript_grades INNER JOIN marking_periods mpinfo ON mpinfo.marking_period_id = transcript_grades.marking_period_id where transcript_grades.student_id = s_id
          AND (transcript_grades.end_date <= mpinfo.end_date and (transcript_grades.parent_end_date is null or transcript_grades.parent_end_date >  mpinfo.end_date) or transcript_grades.marking_period_id = mp_id)
          AND transcript_grades.gp_scale > 0 and transcript_grades.credit_attempted > 0 and class_rank = 'Y' ) as x group by tg.school_scale
    ) as sms1
    ON smstarget.student_id = sms1.student_id and smstarget.marking_period_id = sms1.marking_period_id
    SET cum_cr_weighted_factor = sms1.weighted_gpa, cum_cr_unweighted_factor = sms1.unweighted_gpa;
END$$


DROP FUNCTION IF EXISTS calc_cum_gpa $$
DROP PROCEDURE IF EXISTS calc_cum_gpa $$
CREATE PROCEDURE calc_cum_gpa(mp_id CHAR(20), s_id INT) 
BEGIN
    UPDATE student_mp_stats smstarget
    INNER JOIN 
    (
        SELECT student_id, marking_period_id, (sum((tg.weighted_gp/tg.gp_scale)*tg.credit_attempted)/sum(tg.credit_attempted)) as weighted_gpa,
        (sum((tg.unweighted_gp/tg.gp_scale)*tg.credit_attempted)/sum(tg.credit_attempted)) as unweighted_gpa
        
        FROM transcript_grades tg INNER JOIN (
          SELECT weighted_gp, unweighted_gp, gp_scale, credit_attempted, credit_earned, school_scale 
          FROM transcript_grades INNER JOIN marking_periods mpinfo ON mpinfo.marking_period_id = transcript_grades.marking_period_id where student_id = s_id
          AND (transcript_grades.end_date <= mpinfo.end_date and (transcript_grades.parent_end_date is null or transcript_grades.parent_end_date >  mpinfo.end_date) or transcript_grades.marking_period_id = mp_id)
          AND gp_scale > 0 and credit_attempted > 0 ) as x group by tg.school_scale
    ) as sms1
    ON smstarget.student_id = sms1.student_id and smstarget.marking_period_id = sms1.marking_period_id
    SET cum_weighted_factor = sms1.weighted_gpa, cum_unweighted_factor = sms1.unweighted_gpa;
END$$


DROP FUNCTION IF EXISTS calc_cum_gpa_mp $$
CREATE FUNCTION `calc_cum_gpa_mp`(mp_id CHAR(20)) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE s_id VARCHAR(20);	
	DECLARE my_cur CURSOR FOR
		SELECT student_id AS s_id from student_mp_stats where marking_period_id = mp_id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    OPEN my_cur;
        my_cur_loop:
        LOOP FETCH my_cur INTO s_id;
			IF done = 1 THEN
                LEAVE my_cur_loop;
            END IF;		
			CALL calc_cum_gpa(mp_id, s_id);
			CALL calc_cum_cr_gpa(mp_id, s_id);
        END LOOP my_cur_loop;
    CLOSE my_cur;	
  RETURN 1;
END$$



DROP FUNCTION IF EXISTS set_class_rank_mp $$
CREATE FUNCTION set_class_rank_mp(mp_id CHAR(20)) RETURNS int(11)
    DETERMINISTIC
BEGIN
    UPDATE student_mp_stats smstarget

	INNER JOIN 
    (
    SELECT 
    mp.syear, mp.marking_period_id, sgm.student_id, se.grade_id, sgm.cum_cr_weighted_factor,
    (SELECT count(*)+1 
       FROM student_mp_stats sgm3
       WHERE sgm3.cum_cr_weighted_factor > sgm.cum_cr_weighted_factor
         AND sgm3.marking_period_id = mp.marking_period_id 
         AND sgm3.student_id in (select distinct sgm2.student_id 
                FROM student_mp_stats sgm2, student_enrollment se2
                WHERE sgm2.student_id = se2.student_id 
                    AND sgm2.marking_period_id = mp.marking_period_id 
                    AND se2.grade_id = se.grade_id
                    AND se2.syear = se.syear)
    ) AS netrank,
    
    (SELECT count(*) 
       FROM student_mp_stats sgm4
       WHERE
         sgm4.marking_period_id = mp.marking_period_id 
         AND sgm4.student_id in (select distinct sgm5.student_id 
                FROM student_mp_stats sgm5, student_enrollment se3
                WHERE sgm5.student_id = se3.student_id 
                	AND sgm5.marking_period_id = mp.marking_period_id 
                    AND se3.grade_id = se.grade_id
                    AND se3.syear = se.syear)
    ) AS netclass_size
    FROM student_enrollment se, student_mp_stats sgm, marking_periods mp
    
    WHERE 
    se.student_id = sgm.student_id
    AND sgm.marking_period_id = mp.marking_period_id
    AND mp.marking_period_id = mp_id
    AND se.syear = mp.syear
    AND NOT sgm.cum_cr_weighted_factor is null
    ORDER BY grade_id, netrank ) AS rank

    ON smstarget.marking_period_id = rank.marking_period_id
    AND smstarget.student_id = rank.student_id

	SET cum_rank = rank.netrank, class_size = rank.netclass_size;    
RETURN 1;
END$$


DROP PROCEDURE IF EXISTS calc_gpa_mp $$
CREATE PROCEDURE calc_gpa_mp(s_id INT, mp_id CHAR(20)) 
BEGIN
  DECLARE count INTEGER;
  SELECT COUNT(*) INTO count FROM student_mp_stats AS oldrec WHERE student_id = s_id and marking_period_id = mp_id;
  IF count > 0 THEN
    UPDATE student_mp_stats smstarget
	INNER JOIN 
    (
      SELECT 
	  	student_id, marking_period_id, 
        SUM(weighted_gp*credit_attempted/gp_scale) as sum_weighted_factors, 
        SUM(unweighted_gp*credit_attempted/gp_scale) as sum_unweighted_factors, 
        SUM(credit_attempted) as gp_credits,
        SUM( case when class_rank = 'Y' THEN weighted_gp*credit_attempted/gp_scale END ) as cr_weighted,
        SUM( case when class_rank = 'Y' THEN unweighted_gp*credit_attempted/gp_scale END ) as cr_unweighted,
        SUM( case when class_rank = 'Y' THEN credit_attempted END) as cr_credits

        FROM student_report_card_grades WHERE student_id = s_id
            AND marking_period_id = mp_id
            AND NOT gp_scale = 0 AND NOT marking_period_id LIKE 'E%' group by student_id, marking_period_id
	) as rcg
    ON smstarget.student_id = rcg.student_id and smstarget.marking_period_id = rcg.marking_period_id
    SET 
        smstarget.sum_weighted_factors = rcg.sum_weighted_factors, 
        smstarget.sum_unweighted_factors = rcg.sum_unweighted_factors, 
        smstarget.cr_weighted_factors = rcg.cr_weighted,
        smstarget.cr_unweighted_factors = rcg.cr_unweighted,
        smstarget.gp_credits = rcg.gp_credits,
        smstarget.cr_credits = rcg.cr_credits;       
  ELSE
    INSERT INTO student_mp_stats (student_id, marking_period_id, sum_weighted_factors, sum_unweighted_factors, grade_level_short, cr_weighted_factors, cr_unweighted_factors, gp_credits, cr_credits)
    SELECT 
        srcg.student_id, srcg.marking_period_id, 
        SUM(weighted_gp*credit_attempted/gp_scale) as sum_weighted_factors, 
        SUM(unweighted_gp*credit_attempted/gp_scale) as sum_unweighted_factors, 
        eg.short_name,
        SUM( case when class_rank = 'Y' THEN weighted_gp*credit_attempted/gp_scale END ) as cr_weighted,
        SUM( case when class_rank = 'Y' THEN unweighted_gp*credit_attempted/gp_scale END ) as cr_unweighted,
        SUM(credit_attempted) as gp_credits,
        SUM(case when class_rank = 'Y' THEN credit_attempted END) as cr_credits
    FROM student_report_card_grades AS srcg join marking_periods mp on (mp.marking_period_id = srcg.marking_period_id) left outer join enroll_grade AS eg on (eg.student_id = srcg.student_id and eg.syear = mp.syear and eg.school_id = mp.school_id)
    WHERE srcg.student_id = s_id and srcg.marking_period_id = mp_id and not srcg.gp_scale = 0 
    AND NOT srcg.marking_period_id LIKE 'E%' group by srcg.student_id, srcg.marking_period_id, eg.short_name;
  END IF;
END$$


DROP TRIGGER IF EXISTS srcg_mp_stats_insert $$
CREATE TRIGGER srcg_mp_stats_insert
    AFTER INSERT ON student_report_card_grades
        FOR EACH ROW BEGIN
            CALL calc_gpa_mp(NEW.student_id, NEW.marking_period_id);
END$$

DROP TRIGGER IF EXISTS srcg_mp_stats_update $$
CREATE TRIGGER srcg_mp_stats_update
	AFTER UPDATE ON student_report_card_grades
		 FOR EACH ROW BEGIN
            CALL calc_gpa_mp(OLD.student_id, OLD.marking_period_id);
END$$

DROP TRIGGER IF EXISTS srcg_mp_stats_delete $$
CREATE TRIGGER srcg_mp_stats_delete
    AFTER DELETE ON student_report_card_grades
        FOR EACH ROW BEGIN
        CALL calc_gpa_mp(OLD.student_id, OLD.marking_period_id);
END$$

DELIMITER ;