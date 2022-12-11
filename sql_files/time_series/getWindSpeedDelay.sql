-- -- 
-- SELECT flights.flight_id, date, carrier_code, tail_num, origin, city_name, departure_time, departure_delay, wind_speed FROM 
--     (
--     SELECT *, DATE_FORMAT(flights.departure_time,'%H:00:00') 
--         AS dep_time_floor
--     FROM flights
--     ) AS flights, 
--     (
--     SELECT *
--     FROM weather
--     ) AS weather, 
--     airports, delays
--     WHERE airports.airport_code = flights.origin 
--     AND airports.city = weather.city_name 
--     AND flights.dep_time_floor = weather.time_recorded 
--     AND flights.date = weather.date_recorded
--     AND flights.flight_id = delays.flight_id;
    
-- -- 
-- select cast(concat(date, ' ', departure_time) as datetime) as dep_time from flights;

DROP PROCEDURE IF EXISTS getWindSpeedDelay;
DELIMITER |
CREATE PROCEDURE getWindSpeedDelay (IN num_points INTEGER)
SELECT departure_delay, wind_speed FROM 
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
    AND description LIKE "%fog%" 
    LIMIT num_points;
|
DELIMITER ;
