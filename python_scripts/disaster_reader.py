from dotenv import load_dotenv
import gdown
import pandas as pd
import sqlalchemy as sal
import os
import csv

def format_date(date):
    date = date.split("T")
    return date[0]

def get_disasters(file_name):
    with open(file_name) as csvfile:
        reader = csv.reader(csvfile, delimiter=",")
        data = pd.DataFrame(columns=["fema_declaration_id", "state", "date", "incident_type"])
        first = True
        for row in reader:
            if not first and 2012 <= int(row[5]) <= 2017:
                date = format_date(row[4])
                data.loc[len(data.index)] = [row[0], row[2], date, row[6]]
            else:
                first = False
        return data

def disasters_to_sql(input, output):
    fema = set()
    with open(input) as csvfile:
        with open(output, "a") as sql_file:
            reader = csv.reader(csvfile, delimiter=",")
            first = True
            for row in reader:
                if not first and 2012 <= int(row[5]) <= 2017:
                    date = format_date(row[4])
                    if (row[0], row[2], date) not in fema:
                        fema.add((row[0], row[2], date))
                        sql_file.write(f"INSERT INTO disasters VALUES ( '{row[0]}', '{row[2]}', '{date}', '{row[6]}' );\n")
                else:
                    first = False

def main():
    disasters_to_sql("../datasets/disasters.csv", "../sql_files/sql_dataset_files/disasters.sql")

if __name__ == "__main__":
    main()