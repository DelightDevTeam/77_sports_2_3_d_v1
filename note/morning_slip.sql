SELECT 
    lottery_two_digit_pivots.user_id, 
    lotteries.slip_no, 
    SUM(lottery_two_digit_pivots.sub_amount) as total_sub_amount
FROM 
    lottery_two_digit_pivots
JOIN 
    lotteries ON lottery_two_digit_pivots.lottery_id = lotteries.id
WHERE 
    lottery_two_digit_pivots.res_date = '2024-06-08' 
    AND lottery_two_digit_pivots.session = 'morning'
GROUP BY 
    lottery_two_digit_pivots.user_id, 
    lotteries.slip_no;
