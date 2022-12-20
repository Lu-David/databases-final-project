DROP TABLE airline_company;
CREATE TABLE airline_company (
    stock_code          VARCHAR(100),
    carrier_code        VARCHAR(2),
    airline             VARCHAR(100),
    primary key         (airline),
    foreign key         (stock_code)
        references      stocks(company)
);
INSERT INTO airline_company VALUES ( 'UAL', 'UA', 'United Airlines');
INSERT INTO airline_company VALUES ( 'SAVE', 'NK', 'Spirit Airlines');
INSERT INTO airline_company VALUES ( 'JBLU', 'B6', 'JetBlue');
INSERT INTO airline_company VALUES ( 'ALK', '027', 'Alaska Airlines');
INSERT INTO airline_company VALUES ( 'AAL', 'AA', 'American Airlines');
INSERT INTO airline_company VALUES ( 'LUV', 'WN', 'Southwest Airlines');
INSERT INTO airline_company VALUES ( 'DAL', 'DL', 'Delta Air Lines');