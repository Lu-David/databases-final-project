-- Get average delay for each airline over its entire history
DROP PROCEDURE IF EXISTS getAirlineDelay
DELIMITER |
CREATE PROCEDURE getAirlineDelay()
    SELECT carrier_code, AVG(departure_delay)
    FROM delays NATURAL JOIN flights 
    GROUP BY carrier_code;
|
DELIMITER ;