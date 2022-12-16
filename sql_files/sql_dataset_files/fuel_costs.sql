DROP TABLE fuel_costs;
CREATE TABLE fuel_costs (
    year            YEAR,
	month           VARCHAR(10),
	cost_per_gal    FLOAT,
	primary key 	(year, month)
);
INSERT INTO fuel_costs VALUES ( 2015, 'January', 1.94 );
INSERT INTO fuel_costs VALUES ( 2015, 'February', 2.23 );
INSERT INTO fuel_costs VALUES ( 2015, 'March', 2.01 );
INSERT INTO fuel_costs VALUES ( 2015, 'April', 1.92 );
INSERT INTO fuel_costs VALUES ( 2015, 'May', 2.09 );
INSERT INTO fuel_costs VALUES ( 2015, 'June', 2.04 );
INSERT INTO fuel_costs VALUES ( 2015, 'July', 1.83 );
INSERT INTO fuel_costs VALUES ( 2015, 'August', 1.68 );
INSERT INTO fuel_costs VALUES ( 2015, 'September', 1.59 );
INSERT INTO fuel_costs VALUES ( 2015, 'October', 1.63 );
INSERT INTO fuel_costs VALUES ( 2015, 'November', 1.57 );
INSERT INTO fuel_costs VALUES ( 2015, 'December', 1.44 );
INSERT INTO fuel_costs VALUES ( 2016, 'January', 1.29 );
INSERT INTO fuel_costs VALUES ( 2016, 'February', 1.24 );
INSERT INTO fuel_costs VALUES ( 2016, 'March', 1.28 );
INSERT INTO fuel_costs VALUES ( 2016, 'April', 1.33 );
INSERT INTO fuel_costs VALUES ( 2016, 'May', 1.43 );
INSERT INTO fuel_costs VALUES ( 2016, 'June', 1.79 );
INSERT INTO fuel_costs VALUES ( 2016, 'July', 1.5 );
INSERT INTO fuel_costs VALUES ( 2016, 'August', 1.44 );
INSERT INTO fuel_costs VALUES ( 2016, 'September', 1.48 );
INSERT INTO fuel_costs VALUES ( 2016, 'October', 1.59 );
INSERT INTO fuel_costs VALUES ( 2016, 'November', 1.45 );
INSERT INTO fuel_costs VALUES ( 2016, 'December', 1.61 );
INSERT INTO fuel_costs VALUES ( 2017, 'January', 1.65 );
INSERT INTO fuel_costs VALUES ( 2017, 'February', 1.7 );
INSERT INTO fuel_costs VALUES ( 2017, 'March', 1.65 );
INSERT INTO fuel_costs VALUES ( 2017, 'April', 1.66 );
INSERT INTO fuel_costs VALUES ( 2017, 'May', 1.6 );
INSERT INTO fuel_costs VALUES ( 2017, 'June', 1.53 );
INSERT INTO fuel_costs VALUES ( 2017, 'July', 1.55 );
INSERT INTO fuel_costs VALUES ( 2017, 'August', 1.65 );
INSERT INTO fuel_costs VALUES ( 2017, 'September', 1.81 );
INSERT INTO fuel_costs VALUES ( 2017, 'October', 1.82 );
INSERT INTO fuel_costs VALUES ( 2017, 'November', 1.87 );
INSERT INTO fuel_costs VALUES ( 2017, 'December', 1.91 );
