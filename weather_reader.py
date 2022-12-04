import gdown
import pandas as pd 
from pandas.io import sql
from dotenv import dotenv_values
import sqlalchemy
from sqlalchemy import Table, Column, Integer, String, MetaData
import os 
import logging

config = dotenv_values(".env")
database_username = config['USER']
database_password = config['PASSWORD']
database_ip       = config['HOST']
database_name     = 'flights'
database_connection = sqlalchemy.create_engine('mysql+mysqlconnector://{0}:{1}@{2}/{3}'.
                                               format(database_username, database_password, 
                                                      database_ip, database_name))

##################
# City Locations #
##################

url = 'https://drive.google.com/uc?id=1mOzthsIlHLlAMhxMN5DuUGuEFm-qBX42'
output = './city_locations.csv'
gdown.download(url, output, quiet=False)

df = pd.read_csv(output)

df.to_sql(con=database_connection, name='city_locations', 
                if_exists='replace')


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

print(len(weather_df))
weather_df.iloc[:10000, :].to_sql(con=database_connection, name='weather', 
                if_exists='replace', chunksize = 10000)