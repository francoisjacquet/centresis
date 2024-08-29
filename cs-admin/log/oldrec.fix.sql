DELIMITER $$

DROP PROCEDURE IF EXISTS calc_gpa_mp $$
CREATE PROCEDURE calc_gpa_mp(s_id INT, mp_id CHAR(20)) 
BEGIN
  DECLARE count INTEGER;
  SELECT COUNT(*) INTO count FROM student_mp_stats AS oldrec WHERE student_id = s_id and marking_period_id = mp_id;
  IF count > 0 THEN
    UPDATE student_mp_stats AS smstarget
	INNER JOIN 
    (
      SELECT 
        (weighted_gp*credit_attempted/gp_scale) as sum_weighted_factors, 
        (unweighted_gp*credit_attempted/gp_scale) as sum_unweighted_factors, 
        (credit_attempted) as gp_credits,
        ( case when class_rank = 'Y' THEN weighted_gp*credit_attempted/gp_scale END ) as cr_weighted,
        ( case when class_rank = 'Y' THEN unweighted_gp*credit_attempted/gp_scale END ) as cr_unweighted,
        ( case when class_rank = 'Y' THEN credit_attempted END) as cr_credits

        FROM student_report_card_grades WHERE student_id = s_id
            AND marking_period_id = mp_id
            AND NOT gp_scale = 0 AND NOT marking_period_id LIKE 'E%' group by student_id, marking_period_id
	) as rcg
    ON smstarget.student_id = s_id and smstarget.marking_period_id = mp_id
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
        (weighted_gp*credit_attempted/gp_scale) as sum_weighted_factors, 
        (unweighted_gp*credit_attempted/gp_scale) as sum_unweighted_factors, 
        eg.short_name,
        ( case when class_rank = 'Y' THEN weighted_gp*credit_attempted/gp_scale END ) as cr_weighted,
        ( case when class_rank = 'Y' THEN unweighted_gp*credit_attempted/gp_scale END ) as cr_unweighted,
        (credit_attempted) as gp_credits,
        (case when class_rank = 'Y' THEN credit_attempted END) as cr_credits
    FROM student_report_card_grades AS srcg join marking_periods mp on (mp.marking_period_id = srcg.marking_period_id) left outer join enroll_grade AS eg on (eg.student_id = srcg.student_id and eg.syear = mp.syear and eg.school_id = mp.school_id)
    WHERE srcg.student_id = s_id and srcg.marking_period_id = mp_id and not srcg.gp_scale = 0 
    AND NOT srcg.marking_period_id LIKE 'E%' group by srcg.student_id, srcg.marking_period_id, eg.short_name;
  END IF;
END$$

DELIMITER ;