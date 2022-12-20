/* City specific queries: */
DROP PROCEDURE IF EXISTS getWeather;
DELIMITER |
CREATE PROCEDURE getWeather (IN date VARCHAR(10), IN state VARCHAR(100))
    SELECT time_recorded, humidity, pressure, temperature, wind_direction, wind_speed
    FROM weather
    WHERE date_recorded=date and city_name=city
    ORDER BY time_recorded ASC;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS getDisaster;
DELIMITER |
CREATE PROCEDURE getDisaster (IN target_date VARCHAR(10), IN target_state VARCHAR(100))
    SELECT incident_type
    FROM disasters d, city_locations cl
    WHERE cl.city=target_city AND cl.state=d.state AND d.date=target_date;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS outgoingFlights;
DELIMITER |
CREATE PROCEDURE outgoingFlights (IN target_date VARCHAR(10), IN target_state VARCHAR(100))
    SELECT f.origin, COUNT(*) outgoing_flights
    FROM flights f, airports a
    WHERE a.city=target_city AND a.airport_code=f.origin AND f.date=target_date
    GROUP BY f.origin
    ORDER BY f.origin ASC;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS incomingFlights;
DELIMITER |
CREATE PROCEDURE incomingFlights (IN target_date VARCHAR(10), IN target_state VARCHAR(100))
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
CREATE PROCEDURE locationCancels (IN target_date VARCHAR(10), IN target_city VARCHAR(100))
    SELECT c.origin, COUNT(*)
    FROM airports a, cancelled c
    WHERE a.city=target_city AND a.airport_code=c.origin AND c.date=target_date
    GROUP BY c.origin;
|
DELIMITER ;

/*State specific queries*/
DROP PROCEDURE IF EXISTS getStateWeather;
DELIMITER |
CREATE PROCEDURE getStateWeather (IN date VARCHAR(10), IN target_state VARCHAR(2))
    SELECT time_recorded, AVG(humidity) humidity, AVG(pressure) pressure, AVG(temperature) temperature, AVG(wind_direction) wind_direction, AVG(wind_speed) wind_speed
    FROM weather w, city_locations c
    WHERE w.date_recorded=date AND c.city=w.city_name AND c.state=target_state
    GROUP BY time_recorded
    ORDER BY time_recorded ASC;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS getStateDisaster;
DELIMITER |
CREATE PROCEDURE getStateDisaster (IN target_date VARCHAR(10), IN target_state VARCHAR(2))
    SELECT incident_type
    FROM disasters d
    WHERE d.state=target_state AND d.date=target_date;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS outgoingStateFlights;
DELIMITER |
CREATE PROCEDURE outgoingStateFlights (IN target_date VARCHAR(10), IN target_state VARCHAR(2))
    SELECT COUNT(*) outgoing_flights
    FROM flights f, airports a
    WHERE a.state=target_state AND a.airport_code=f.origin AND f.date=target_date
|
DELIMITER ;

DROP PROCEDURE IF EXISTS incomingStateFlights;
DELIMITER |
CREATE PROCEDURE incomingStateFlights (IN target_date VARCHAR(10), IN target_state VARCHAR(2))
    SELECT COUNT(*) incoming_flights
    FROM flights f, airports a
    WHERE a.state=target_state AND a.airport_code=f.destination AND f.date=target_date
|
DELIMITER ;

DROP PROCEDURE IF EXISTS locationStateDelays;
DELIMITER |
CREATE PROCEDURE locationStateDelays (IN target_date VARCHAR(10), IN target_state VARCHAR(100))
    SELECT AVG(d.weather_delay) avg_weather_delay, AVG(NAS_delay) avg_NAS_delay, AVG(security_delay) avg_security_delay, AVG(late_aircraft_delay) avg_late_aircraft
    FROM flights f, airports a, delays d
    WHERE a.state=target_state AND a.airport_code=f.origin AND f.date=target_date AND f.flight_id=d.flight_id
|
DELIMITER ;

DROP PROCEDURE IF EXISTS locationStateCancels;
DELIMITER |
CREATE PROCEDURE locationStateCancels (IN target_date VARCHAR(10), IN target_state VARCHAR(100))
    SELECT COUNT(*) Cancels
    FROM airports a, cancelled c
    WHERE a.state=target_state AND a.airport_code=c.origin AND c.date=target_date
|
DELIMITER ;

/*Airline Specific Queries*/
DROP PROCEDURE IF EXISTS airlineFlights;
DELIMITER |
CREATE PROCEDURE airlineFlights (IN year YEAR, IN airline_name VARCHAR(100))
    SELECT f.date, COUNT(*)
    FROM flights f, airline_company a
    WHERE a.carrier_code=f.carrier_code AND a.airline=airline_name AND YEAR(f.date)=year
    GROUP BY a.airline, f.date;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS airlineDelays;
DELIMITER |
CREATE PROCEDURE airlineDelays (IN year YEAR, IN airline_name VARCHAR(100))
    SELECT f.date, COUNT(*)
    FROM flights f, delays d, airline_company a
    WHERE f.flight_id=d.flight_id AND a.carrier_code=f.carrier_code AND a.airline=airline_name AND YEAR(f.date)=year
    GROUP BY a.airline, f.date;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS airlineCancels;
DELIMITER |
CREATE PROCEDURE airlineCancels (IN year YEAR, IN airline_name VARCHAR(100))
    SELECT c.date, COUNT(*)a
    FROM cancelled c, airline_company a
    WHERE a.carrier_code=c.carrier_code AND a.airline=airline_name AND YEAR(c.date)=year
    GROUP BY a.airline, c.date;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS airlineStock;
DELIMITER |
CREATE PROCEDURE airlineStock (IN year YEAR, IN airline_name VARCHAR(100))
    SELECT s.date, s.open, s.close
    FROM stocks s, airline_company a
    WHERE YEAR(s.date)=year AND s.company=a.stock_code AND a.airline=airline_name
|
DELIMITER ;

DROP PROCEDURE IF EXISTS airlineAccidents;
DELIMITER |
CREATE PROCEDURE airlineAccidents (IN year YEAR, IN airline_name VARCHAR(100))
    SELECT f.date, COUNT(*)
    FROM airline_company ac, flights f, accidents a
    WHERE a.date=f.date AND a.tail_num=f.tail_num AND f.carrier_code=ac.carrier_code AND ac.airline=airline_name AND YEAR(f.date)=year
    GROUP BY f.date;
|
DELIMITER ;

/*airline analysis*/
-- Get average delay for each airline over its entire history
DROP PROCEDURE IF EXISTS getAirlineDelay
DELIMITER |
CREATE PROCEDURE getAirlineDelay()
    SELECT carrier_code, AVG(departure_delay)
    FROM delays NATURAL JOIN flights 
    GROUP BY carrier_code;
|
DELIMITER ;

DROP PROCEDURE IF EXISTS getFlightsPerDate;
DELIMITER |
CREATE PROCEDURE getFlightsPerDate (IN start_date VARCHAR(10), IN end_date VARCHAR(10))
    SELECT date, COUNT(date)
    FROM flights
    WHERE date >= start_date AND date <= end_Date
    GROUP BY date;
|
DELIMITER ;

-- Get Stock Price data for each airline 
DROP PROCEDURE IF EXISTS getStockPrice
DELIMITER |
CREATE PROCEDURE getStockPrice(IN target_date DATE)
    SELECT company, open, close
    FROM stocks
    WHERE date = target_date;
|
DELIMITER ;

/*weather delay analysis*/
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
    LIMIT num_points;
|
DELIMITER ;