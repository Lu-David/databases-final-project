DROP PROCEDURE IF EXISTS getFlightsPerDate;
DELIMITER |
CREATE PROCEDURE getFlightsPerDate (IN start_date VARCHAR(10), IN end_date VARCHAR(10))
    SELECT date, COUNT(date)
    FROM flights
    WHERE date >= start_date AND date <= end_Date
    GROUP BY date;
|
DELIMITER ;