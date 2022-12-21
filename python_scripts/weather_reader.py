import gdown
import pandas as pd 
from pandas.io import sql
from dotenv import dotenv_values
import sqlalchemy
from sqlalchemy import Table, Column, Integer, String, MetaData
import os 
import logging
from sqlalchemy import event
import datetime

config = dotenv_values(".env")
database_username = config['USER']
database_password = config['PASSWORD']
database_ip       = config['HOST']
database_name     = 'flights'
database_connection = sqlalchemy.create_engine('mysql+mysqlconnector://{0}:{1}@{2}/{3}'.
                                               format(database_username, database_password, 
                                                      database_ip, database_name))

@event.listens_for(database_connection, "before_cursor_execute")
def receive_before_cursor_execute(
       conn, cursor, statement, params, context, executemany
        ):
            if executemany:
                cursor.fast_executemany = True

##################
# City Locations #
##################

url = 'https://drive.google.com/uc?id=1mOzthsIlHLlAMhxMN5DuUGuEFm-qBX42'
output = './city_locations.csv'
gdown.download(url, output, quiet=True)

city_locations_df = pd.read_csv(output)

# df.to_sql(con=database_connection, name='city_locations', index = False, 
#                 if_exists='replace')

table_name = "city_locations"
with open('../sql_files/sql_dataset_files/city_locations.sql', 'w') as f:
    f.write(f"""
drop table {table_name};
create table {table_name} (
    city    		    VARCHAR(100),    
    country             VARCHAR(100),    
    latitutde           DECIMAL(10, 5),
    longitude           DECIMAL(10, 5)
);
    """)

    values = [str(val) for val in list(city_locations_df.itertuples(index=False, name = None))]
    statement = f"""
INSERT INTO {table_name} VALUES {",".join(values)};
    """
    f.write(statement)

##################
# Weather        #
##################

base_url = "https://drive.google.com/uc?id="
source_ids = {
    "humidty" : '18c2fd5iahMgs8yoHo3NjxkrqtaLXp-U8',
    "pressure" : '1_MSCJkedd6g3CBVR376oh3GSHpm_5GuJ',
    "temperature" : '1vrBAZUS5jEGdhPe83H25QHnQgpzCPcFC',
    "description" : '1f9ZYDHTo5r0E0pCbBUMBLqFi2G700RFZ',
    "wind_direction" : '1lGVvDTejZMayoDB3AkXswhdOCwGDLRoX',
    "wind_speed" : '1PVBx6RJI84xLFaJaHAI-U-O_o1bWa53M'
}

for field_name, gdoc_id in source_ids.items():
    url = base_url + gdoc_id
    output = f'./{field_name}.csv'
    if not os.path.exists(output):
        gdown.download(url, output, quiet=False)
    else:
        logging.info(f"{output} already exists in this directory!")

df_ls = []
for field_name in list(source_ids.keys()):
    output = f'./{field_name}.csv'
    df = pd.read_csv(output)
    df = pd.melt(df, id_vars = ['datetime'], var_name = 'city_name', value_name = field_name)
    df_ls.append(df)

weather_df = df_ls[0]
for df_other in df_ls[1:]:
    weather_df = weather_df.merge(df_other, how = 'outer', on = ['datetime', 'city_name'])

weather_df['datetime']= pd.to_datetime(weather_df['datetime'])
# weather_df['city_name'] = weather_df['city_name'].astype('|S80')
# weather_df['description'] = weather_df['description'].astype('|S80')


weather_df = weather_df[weather_df['datetime'] > pd.Timestamp(2015, 1, 1)]
# weather_df = weather_df.head()
weather_df = weather_df.astype('str')

new = weather_df['datetime'].str.split(" ", expand = True)
weather_df = weather_df.drop(['datetime'], errors = 'ignore', axis = 1)
weather_df.insert(0, 'date', new[0])
weather_df.insert(1, 'time', new[1])

table_name = "weather"
field_names = ['']
with open('../sql_files/sql_dataset_files/weather.sql', 'w') as f:
    f.write(f"""
drop table {table_name};
create table {table_name} (
    date_recorded   	DATE,
    time_recorded   	TIME,
    city_name		    VARCHAR(100),    
    humidity		    DECIMAL(5, 2),
    pressure		    DECIMAL(7, 2),
    temperature		    DECIMAL(32, 16),
    description		    VARCHAR(1000),
    wind_direction		SMALLINT,
    wind_speed		    SMALLINT
);
    """)

    for i in range(len(weather_df) // 1000):
        values = [str(val) for val in list(weather_df[1000 * i:1000 * (i + 1)].itertuples(index=False, name = None))]
        statement = f"""
    INSERT INTO {table_name} VALUES {",".join(values)};
        """
        statement = statement.replace("'nan'", "NULL")
        f.write(statement)
        
# import time 
# start = time.time()
# weather_df.to_sql(con=database_connection, name='weather', 
#                 if_exists='replace', index = False, method='multi', chunk_size = 5000)
# end = time.time()
# print(start - end)