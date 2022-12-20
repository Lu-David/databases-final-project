DROP PROCEDURE IF EXISTS getDescDelay;
DELIMITER |
CREATE PROCEDURE getDescDelay (IN regex VARCHAR(20))
SELECT AVG(departure_delay), STDDEV(departure_delay) FROM 
    (
    SELECT *, DATE_FORMAT(flights.departure_time,'%H:00:00') 
        AS dep_time_floor
    FROM flights
    ) AS flights, 
    weather, airports, delays
    WHERE airports.airport_code = flights.origin 
    AND airports.city = weather.city_name 
    AND flights.dep_time_floor = weather.time_recorded 
    AND flights.date = weather.date_recorded
    AND flights.flight_id = delays.flight_id
    AND description LIKE regex;
|
DELIMITER ;
