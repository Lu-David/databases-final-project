from dotenv import load_dotenv
import gdown
import pandas as pd
import sqlalchemy as sal
import os
import csv

def get_fuel_costs(file_name):
    with open(file_name) as csvfile:
        reader = csv.reader(csvfile, delimiter=",")
        data = pd.DataFrame(columns=["year", "month", "cost_per_gal"])
        line  = 0
        for row in reader:
            if line > 2:
                data.loc[len(data.index)] = [row[0], row[1], row[4]]
            else:
                line += 1
        return data

def fuel_to_sql(input, output):
    with open(input) as csvfile:
        with open(output, "a") as sql_file:
            reader = csv.reader(csvfile, delimiter=",")
            line = 0
            for row in reader:
                if line > 2:
                    # s = f"INSERT INTO fuel_costs VALUES {row[0]}, {row[1], {row[4]}}"
                    sql_file.write(f"INSERT INTO fuel_costs VALUES ( {row[0]}, '{row[1]}', {row[4]} );\n")
                else:
                    line += 1

def main():
    fuel_to_sql("../datasets/fuel_costs.csv", "../sql_files/fuel_costs.sql")

if __name__ == "__main__":
    main()