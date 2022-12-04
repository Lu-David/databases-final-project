from dotenv import load_dotenv
import gdown
import pandas as pd
import sqlalchemy as sal
import os
import csv

def format_state(state):
    result = state.split("-")
    return result[1]

def get_airports(file_name):
    with open(file_name) as csvfile:
        reader = csv.reader(csvfile, delimiter=",")
        data = pd.DataFrame(columns=["airport_code", "state", "city", "size"])
        line = 0
        for row in reader:
            if line > 1 and row[8] == "US" and row[13]:
                state = format_state(row[9])
                data.loc[len(data.index)] = [row[13], state, row[10], row[2]]
            else:
                line += 1
        return data

def airports_to_sql(input, output):
    with open(input) as csvfile:
        with open(output, "a") as sql_file:
            reader = csv.reader(csvfile, delimiter=",")
            line = 0
            for row in reader:
                if line > 1 and row[8] == "US" and row[13]:
                    state = format_state(row[9])
                    sql_file.write(f"INSERT INTO airports VALUES ( '{row[13]}', '{state}', '{row[10]}', '{row[2]}' );\n")
                else:
                    line += 1

def main():
    airports_to_sql("datasets/airports.csv", "sql_files/airports.sql")

if __name__ == "__main__":
    main()