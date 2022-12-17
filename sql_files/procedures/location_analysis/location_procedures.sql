/* Location specific queries: */
DROP PROCEDURE IF EXISTS getWeather;
DELIMITER |
CREATE PROCEDURE getWeather (IN date VARCHAR(10), IN city VARCHAR(10))
    SELECT time_recorded, humidity, pressure, temperature, wind_direction, wind_speed
    FROM weather
    WHERE date_recorded=date and city_name=city
    ORDER BY time_recorded ASC;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS getDisaster;
DELIMITER |
CREATE PROCEDURE getDisaster (IN target_date VARCHAR(10), IN target_city VARCHAR(10))
    SELECT incident_type
    FROM disasters d, city_locations cl
    WHERE cl.city=target_city AND cl.state=d.state AND d.date=target_date;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS outgoingFlights;
DELIMITER |
CREATE PROCEDURE outgoingFlights (IN target_date VARCHAR(10), IN target_city VARCHAR(10))
    SELECT f.origin, COUNT(*) outgoing_flights
    FROM flights f, airports a
    WHERE a.city=target_city AND a.airport_code=f.origin AND f.date=target_date
    GROUP BY f.origin
    ORDER BY f.origin ASC;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS incomingFlights;
DELIMITER |
CREATE PROCEDURE incomingFlights (IN target_date VARCHAR(10), IN target_city VARCHAR(10))
    SELECT f.destination, COUNT(*) incoming_flights
    FROM flights f, airports a
    WHERE a.city=target_city AND a.airport_code=f.destination AND f.date=target_date
    GROUP BY f.destination
    ORDER BY f.destination ASC;
|
DELIMITER ;

/*Only delays for departure from given city*/
DROP PROCEDURE IF EXISTS locationDelays;
DELIMITER |
CREATE PROCEDURE locationDelays (IN target_date VARCHAR(10), IN target_city VARCHAR(100))
    SELECT f.origin, AVG(d.weather_delay) avg_weather_delay, AVG(NAS_delay) avg_NAS_delay, AVG(security_delay) avg_security_delay, AVG(late_aircraft_delay) avg_late_aircraft
    FROM flights f, airports a, delays d
    WHERE a.city=target_city AND a.airport_code=f.origin AND f.date=target_date AND f.flight_id=d.flight_id
    GROUP BY f.origin;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS locationCancels;
DELIMITER |
CREATE PROCEDURE locationCancels (IN target_date VARCHAR(10), IN target_city VARCHAR(10))
    SELECT c.origin, COUNT(*)
    FROM airports a, cancelled c
    WHERE a.city=target_city AND a.airport_code=c.origin AND c.date=target_date
    GROUP BY c.origin;
|
DELIMITER ;