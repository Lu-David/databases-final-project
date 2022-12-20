import csv

def format_date(date):
    result = date.split("T")
    return result[0]

def format_tail_num(tail_num):
    result = tail_num.split(",")
    return result[0]

def accidents_to_sql(input, output):
    with open(input) as csvfile:
        with open(output, "a") as sql_file:
            reader = csv.reader(csvfile, delimiter=",")
            first = True
            for row in reader:
                if not first and row[6] == "United States":
                    date = format_date(row[3])
                    tail_num = format_tail_num(row[8])
                    if tail_num:
                        row[14] = 0 if not row[14] else row[14]
                        row[15] = 0 if not row[14] else row[15]
                        row[16] = 0 if not row[14] else row[16]
                        sql_file.write(f"INSERT INTO accidents VALUES ( '{date}', '{tail_num}', '{row[4]}', '{row[5]}', {row[14]}, {row[15]}, {row[16]} );\n")
                else:
                    first = False

def main():
    accidents_to_sql("datasets/accidents.csv", "sql_files/accidents.sql")

if __name__ == "__main__":
    main()