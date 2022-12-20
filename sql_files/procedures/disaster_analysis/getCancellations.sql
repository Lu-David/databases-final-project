DROP PROCEDURE IF EXISTS getNumCancellations;
DELIMITER |
CREATE PROCEDURE getNumCancellations (in offset INT)
    SELECT COUNT(*) FROM (
        SELECT T.d_date, T.d_state, COUNT(c_id) as cnt FROM 
        (
            SELECT d.date as d_date, d.state as d_state, c_in.cancel_id as c_id
            FROM cancelled as c_in JOIN airports as a_in 
            ON c_in.destination = a_in.airport_code
            JOIN (SELECT *, DATE_ADD(date, INTERVAL offset day) as d_off FROM disasters) as d
            ON a_in.state = d.state AND c_in.date = d.d_off
            UNION 
            SELECT d.date as d_date, d.state as d_state, c_out.cancel_id as c_id
            FROM cancelled as c_out JOIN airports as a_out 
            ON c_out.destination = a_out.airport_code
            JOIN (SELECT *, DATE_ADD(date, INTERVAL offset day) as d_off FROM disasters) as d
            ON a_out.state = d.state AND c_out.date = d.d_off
        ) as T GROUP BY T.d_date, T.d_state
    ) AS U;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS getNumFlights;
DELIMITER |
CREATE PROCEDURE getNumFlights (in offset INT)
    SELECT COUNT(*) FROM (
        SELECT T.d_date, T.d_state, COUNT(f_id) as cnt FROM 
        (
            SELECT d.date as d_date, d.state as d_state, f_in.flight_id as f_id
            FROM flights as f_in JOIN airports as a_in 
            ON f_in.destination = a_in.airport_code
            JOIN (SELECT *, DATE_ADD(date, INTERVAL offset day) as d_off FROM disasters) as d
            ON a_in.state = d.state AND f_in.date = d.d_off
            UNION 
            SELECT d.date as d_date, d.state as d_state, f_out.flight_id as f_id
            FROM flights as f_out JOIN airports as a_out 
            ON f_out.destination = a_out.airport_code
            JOIN (SELECT *, DATE_ADD(date, INTERVAL offset day) as d_off FROM disasters) as d
            ON a_out.state = d.state AND f_out.date = d.d_off
        ) as T GROUP BY T.d_date, T.d_state
    ) AS U;
|
DELIMITER ;


-- no airport code for Puerto Rico or Virgin Islands
-- DROP PROCEDURE IF EXISTS getPropCancelled;
-- DELIMITER |
-- CREATE PROCEDURE getPropCancelled (in offset INT)
--     SELECT COUNT(*) FROM (
--         SELECT T.d_date, T.d_state, COUNT(f_id) as cnt FROM 
--         (
--             SELECT d.date as d_date, d.state as d_state, f_in.flight_id as f_id
--             FROM flights as f_in JOIN airports as a_in 
--             ON f_in.destination = a_in.airport_code
--             JOIN (SELECT *, DATE_ADD(date, INTERVAL offset day) as d_off FROM disasters) as d
--             ON a_in.state = d.state AND f_in.date = d.d_off
--             UNION 
--             SELECT d.date as d_date, d.state as d_state, f_out.flight_id as f_id
--             FROM flights as f_out JOIN airports as a_out 
--             ON f_out.destination = a_out.airport_code
--             JOIN (SELECT *, DATE_ADD(date, INTERVAL offset day) as d_off FROM disasters) as d
--             ON a_out.state = d.state AND f_out.date = d.d_off
--         ) as T GROUP BY T.d_date, T.d_state
--     ) AS U;
-- |
-- DELIMITER ;