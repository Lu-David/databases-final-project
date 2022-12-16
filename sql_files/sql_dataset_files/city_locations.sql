
drop table city_locations;
create table city_locations (
    city    		    VARCHAR(100),
    state               VARCHAR(2),    
    country             VARCHAR(100),    
    latitutde           DECIMAL(10, 5),
    longitude           DECIMAL(10, 5),
    primary key         (city)
);
    
INSERT INTO city_locations VALUES ('Portland', 'OR', 'United States', 45.523449, -122.676208),('San Francisco', 'CA', 'United States', 37.774929, -122.419418),('Seattle', 'WA', 'United States', 47.606209, -122.332069),('Los Angeles', 'CA', 'United States', 34.052231, -118.243683),('San Diego', 'CA', 'United States', 32.715328, -117.157257),('Las Vegas', 'NV', 'United States', 36.174969, -115.137222),('Phoenix', 'AZ', 'United States', 33.44838, -112.074043),('Albuquerque', 'NM', 'United States', 35.084492, -106.651138),('Denver', 'CO', 'United States', 39.739151, -104.984703),('San Antonio', 'TX', 'United States', 29.42412, -98.493629),('Dallas', 'TX', 'United States', 32.783058, -96.806671),('Houston', 'TX', 'United States', 29.763281, -95.363274),('Kansas City', 'KS', 'United States', 39.099731, -94.578568),('Minneapolis', 'MN', 'United States', 44.979969, -93.26384),('Saint Louis', 'MO', 'United States', 38.62727, -90.197891),('Chicago', 'IL', 'United States', 41.850029, -87.650047),('Nashville', 'TN', 'United States', 36.16589, -86.784439),('Indianapolis', 'IN', 'United States', 39.768379, -86.158043),('Atlanta', 'GA', 'United States', 33.749001, -84.387978),('Detroit', 'MI', 'United States', 42.331429, -83.045753),('Jacksonville', 'FL', 'United States', 30.33218, -81.655647),('Charlotte', 'NC', 'United States', 35.227089, -80.843132),('Miami', 'FL', 'United States', 25.774269, -80.193657),('Pittsburgh', 'PA', 'United States', 40.44062, -79.995888),('Philadelphia', 'PA', 'United States', 39.952339, -75.163788),('New York', 'NY', 'United States', 40.714272, -74.005966),('Boston', 'MA', 'United States', 42.358429, -71.059769);