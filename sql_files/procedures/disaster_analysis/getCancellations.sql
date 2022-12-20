DROP PROCEDURE IF EXISTS getAvgCancellations;
DELIMITER |
CREATE PROCEDURE getAvgCancellations (in offset INT)
    SELECT AVG(cnt), STDDEV(cnt) FROM (
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

-- cancellation count by disaster type
DROP PROCEDURE IF EXISTS getCancDisType;
DELIMITER |
CREATE PROCEDURE getCancDisType ()

    SELECT T.d_type, COUNT(c_id) as cnt FROM 
    (
        SELECT d.incident_type as d_type, d.date as d_date, d.state as d_state, c_in.cancel_id as c_id
        FROM cancelled as c_in JOIN airports as a_in 
        ON c_in.destination = a_in.airport_code
        JOIN (SELECT *, DATE_ADD(date, INTERVAL 0 day) as d_off FROM disasters) as d
        ON a_in.state = d.state AND c_in.date = d.d_off
        UNION 
        SELECT d.incident_type as d_type, d.date as d_date, d.state as d_state, c_out.cancel_id as c_id
        FROM cancelled as c_out JOIN airports as a_out 
        ON c_out.destination = a_out.airport_code
        JOIN (SELECT *, DATE_ADD(date, INTERVAL 0 day) as d_off FROM disasters) as d
        ON a_out.state = d.state AND c_out.date = d.d_off
    ) as T GROUP BY T.d_type;
|
DELIMITER ;

DROP INDEX disaster_idx ON disasters;
CREATE INDEX disaster_idx on disasters(state, date);
DROP INDEX cancelled_idx ON cancelled;
CREATE INDEX cancelled_idx on cancelled (origin, destination, date);

-- DROP PROCEDURE IF EXISTS getNumFlights;
-- DELIMITER |
-- CREATE PROCEDURE getNumFlights (in offset INT)
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