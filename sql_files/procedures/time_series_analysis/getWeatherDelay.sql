DROP PROCEDURE IF EXISTS getWeatherDelay;
DELIMITER |
CREATE PROCEDURE getWeatherDelay (IN regex VARCHAR(20))
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
    AND flights.date = delays.date 
    AND flights.tail_num = delays.tail_num
    AND flights.flight_num = delays.flight_num
    AND description LIKE regex;
|
DELIMITER ;
