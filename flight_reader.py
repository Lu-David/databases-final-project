import csv
import os
import random

def format_date(date):
    date = date.split(" ")
    date = date[0]
    date = date.split("/")
    return f'{date[2]}-{date[0]}-{date[1]}'

def format_accident_date(date):
    result = date.split("T")
    return result[0]

def format_time(time):
    while len(time) < 4:
        time = "".join(["0", time])
    return f"{time[0]}{time[1]}:{time[2]}{time[3]}:00"

def format_minutes(minutes):
    minutes = minutes.split(".")
    return minutes[0]

def format_tail_num(tail_num):
    result = tail_num.split(",")
    return result[0]

id = 1

def flights_and_delays_to_sql(input, flight_output, delay_output, cancelled_output, accidents):
    global id
    f = open(input, "r")
    lines = len(f.readlines())
    f.close()
    random_flights = set(random.sample(range(id,id+lines), k=lines//100))
    with open(input) as csvfile:
        with open(flight_output, "a") as flight_sql_file:
            with open(delay_output, "a") as delay_sql_file:
                with open(cancelled_output, "a") as cancelled_sql_file:
                    flights = []
                    delays = []
                    cancels = []
                    first = True
                    reader = csv.reader(csvfile, delimiter=",")
                    for row in reader:
                        if not first and row[2]:
                            date = format_date(row[0])
                            if row[14] == "1.00" and id in random_flights:
                                cancels.append(f"({id}, '{date}', '{row[1]}', '{row[2]}', {row[3]}, '{row[4]}', '{row[7]}')")
                                if len(cancels) >= 1000:
                                    statement = f"""INSERT INTO cancelled VALUES {",".join(cancels)};\n"""
                                    cancelled_sql_file.write(statement)
                                    cancels = []
                            else:
                                if row[16] and (id in random_flights or (date, row[2]) in accidents):

                                    flights.append(f"({id}, '{date}', '{row[1]}', '{row[2]}', {row[3]}, '{row[4]}', '{row[7]}', '{format_time(row[10])}', '{format_time(row[12])}', {format_minutes(row[16])}, {format_minutes(row[17])})")
                                    if len(flights) >= 1000:
                                        statement = f"""INSERT INTO flights VALUES {",".join(flights)};\n"""
                                        flight_sql_file.write(statement)
                                        flights = []
                                    if row[18]:
                                        departure_delay, arrival_delay, carrier_delay, weather_delay, NAS_delay, security_delay, late_aircraft_delay = format_minutes(row[11]), format_minutes(row[13]), format_minutes(row[18]), format_minutes(row[19]), format_minutes(row[20]), format_minutes(row[21]), format_minutes(row[22])
                                        delays.append(f"({id}, {departure_delay}, {arrival_delay}, {carrier_delay}, {weather_delay}, {NAS_delay}, {security_delay}, {late_aircraft_delay})")
                                        if len(delays) >= 1000:
                                            statement = f"""INSERT INTO delays VALUES {",".join(delays)};\n"""
                                            delay_sql_file.write(statement)
                                            delays = []
                            id += 1
                        else:
                            first = False
                    if flights:
                        flight_sql_file.write(f"""INSERT INTO flights VALUES {",".join(flights)};\n""")
                    if delays:
                        delay_sql_file.write(f"""INSERT INTO delays VALUES {",".join(delays)};\n""")
                    if cancels:
                        cancelled_sql_file.write(f"""INSERT INTO cancelled VALUES {",".join(cancels)};\n""")

def accident_read(input):
    accidents = set()
    with open(input) as csvfile:
        reader = csv.reader(csvfile, delimiter=",")
        first = True
        for row in reader:
            if not first and row[6] == "United States":
                date = format_accident_date(row[3])
                tail_num = format_tail_num(row[8])
                if tail_num:
                    accidents.add((date, tail_num))
            else:
                first = False
    return accidents

def main():
    files = os.listdir("datasets/flights")
    accidents = accident_read("datasets/accidents.csv")
    for file in files:
       flights_and_delays_to_sql(f"datasets/flights/{file}", "sql_files/sql_dataset_files/flight.sql", "sql_files/sql_dataset_files/delay.sql", "sql_files/sql_dataset_files/cancelled.sql", accidents)

if __name__ == "__main__":
    main()