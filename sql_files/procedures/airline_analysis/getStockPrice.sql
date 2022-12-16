-- Get Stock Price data for each airline 
DROP PROCEDURE IF EXISTS getStockPrice
DELIMITER |
CREATE PROCEDURE getStockPrice(IN target_date DATE)
    SELECT company, open, close
    FROM stocks
    WHERE date = target_date;
|
DELIMITER ;