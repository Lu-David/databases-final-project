from dotenv import load_dotenv
import gdown
import pandas as pd
import sqlalchemy as sal
import os
import csv
import random

flight_id = 0

def format_date(date):
    date = date.split(" ")
    date = date[0]
    date = date.split("/")
    return f'{date[2]}-{date[0]}-{date[1]}'

def format_time(time):
    while len(time) < 4:
        time = "".join(["0", time])
    return f"{time[0]}{time[1]}:{time[2]}{time[3]}:00"

def format_minutes(minutes):
    minutes = minutes.split(".")
    return minutes[0]

def get_flights_and_delays(file_name):
    global flight_id
    with open(file_name) as csvfile:
        first = True
        reader = csv.reader(csvfile, delimiter=",")
        flight_data = pd.DataFrame(columns=["flight_id", "date", "time", "carrier_code", "tail_num", "origin", "destination", "departure_time", "arrival_time", "cancelled", "time_of_flight", "distance"])
        delay_data = pd.DataFrame(columns=["flight_id", "departure_delay", "arrival_delay", "carrier_delay", "weather_delay", "NAS_delay", "security_delay", "late_aircraft_delay"])
        for row in reader:
            if not first:
                date, time = format_date(row[0])
                cancelled = "yes" if row[15] == "1.00" else "no"
                flight_data.loc[len(flight_data.index)] = [flight_id, date, time, row[1], row[2], row[4], row[8], row[11], row[13], cancelled, row[18], row[19]]
                if row[20]:
                    delay_data.loc[len(delay_data.index)] = [flight_id, row[12], row[14], row[20], row[21], row[22], row[23], row[24]]
                flight_id += 1
            else:
                first = False
        return flight_data, delay_data

def flights_and_delays_to_sql(input, flight_output, delay_output, cancelled_output):
    global flight_id
    with open(input) as csvfile:
        with open(flight_output, "a") as flight_sql_file:
            with open(delay_output, "a") as delay_sql_file:
                with open(cancelled_output, "a") as cancelled_sql_file:
                    first = True
                    reader = csv.reader(csvfile, delimiter=",")
                    for row in reader:
                        if not first and row[2]:
                            date = format_date(row[0])
                            if row[15] == "1.00":
                                cancelled_sql_file.write(f"INSERT INTO cancelled VALUES ( {flight_id}, '{date}', '{row[1]}', '{row[2]}', '{row[4]}', '{row[8]}' );\n")
                            else:
                                departure_time = format_time(row[11])
                                arrival_time = format_time(row[13])
                                flight_sql_file.write(f"INSERT INTO flights VALUES ( {flight_id}, '{date}', '{row[1]}', '{row[2]}', '{row[4]}', '{row[8]}', '{departure_time}', '{arrival_time}', {format_minutes(row[18])}, {format_minutes(row[19])} );\n")
                                if row[20]:
                                    departure_delay = format_minutes(row[12])
                                    arrival_delay = format_minutes(row[14])
                                    carrier_delay = format_minutes(row[20])
                                    weather_delay = format_minutes(row[21])
                                    NAS_delay = format_minutes(row[22])
                                    security_delay = format_minutes(row[23])
                                    late_aircraft_delay = format_minutes(row[24])
                                    delay_sql_file.write(f"INSERT INTO delays VALUES ( {flight_id}, {departure_delay}, {arrival_delay}, {carrier_delay}, {weather_delay}, {NAS_delay}, {security_delay}, {late_aircraft_delay} );\n")
                            flight_id += 1
                        else:
                            first = False

def main():
    flights_and_delays_to_sql("datasets/flights.csv", "sql_files/flight.sql", "sql_files/delay.sql", "sql_files/cancelled.sql")

if __name__ == "__main__":
    main()