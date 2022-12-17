import csv

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

def flights_and_delays_to_sql(input, flight_output, delay_output, cancelled_output):
    with open(input) as csvfile:
        with open(flight_output, "a") as flight_sql_file:
            with open(delay_output, "a") as delay_sql_file:
                with open(cancelled_output, "a") as cancelled_sql_file:
                    flights = []
                    delays = []
                    cancels = []
                    first = True
                    id = 0
                    reader = csv.reader(csvfile, delimiter=",")
                    for row in reader:
                        if not first and row[2]:
                            date = format_date(row[0])
                            if row[15] == "1.00":
                                cancels.append(f"({id}, '{date}', '{row[1]}', '{row[2]}', {row[3]}, '{row[4]}', '{row[8]}')")
                                id += 1
                                if len(cancels) >= 1000:
                                    statement = f"""INSERT INTO cancelled VALUES {",".join(cancels)};\n"""
                                    cancelled_sql_file.write(statement)
                                    cancels = []
                            else:
                                departure_time = format_time(row[11])
                                arrival_time = format_time(row[13])
                                if row[18]:
                                    flights.append(f"({id}, '{date}', '{row[1]}', '{row[2]}', {row[3]}, '{row[4]}', '{row[8]}', '{departure_time}', '{arrival_time}', {format_minutes(row[18])}, {format_minutes(row[19])})")
                                    if len(flights) >= 1000:
                                        statement = f"""INSERT INTO flights VALUES {",".join(flights)};\n"""
                                        flight_sql_file.write(statement)
                                        flights = []
                                    if row[20]:
                                        departure_delay, arrival_delay, carrier_delay, weather_delay, NAS_delay, security_delay, late_aircraft_delay = format_minutes(row[12]), format_minutes(row[14]), format_minutes(row[20]), format_minutes(row[21]), format_minutes(row[22]), format_minutes(row[23]), format_minutes(row[24])
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
def main():
    flights_and_delays_to_sql("datasets/flights.csv", "sql_files/sql_dataset_files/flight.sql", "sql_files/sql_dataset_files/delay.sql", "sql_files/sql_dataset_files/cancelled.sql")

if __name__ == "__main__":
    main()