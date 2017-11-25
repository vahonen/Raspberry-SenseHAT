#! /usr/bin/python
# -*- coding: cp1252 -*-

from sense_hat import SenseHat
import time
import os
import MySQLdb
import serial

sense = SenseHat()

while True:
    temperature = sense.get_temperature_from_pressure()
    pressure = sense.get_pressure()
    humidity = sense.get_humidity()

    cpu_temp = os.popen('vcgencmd measure_temp').readline()
    cpu_temp = float(cpu_temp.replace("temp=","").replace("'C\n",""))
                                                                                                                                                                                                          
    raw_temp = sense.get_temperature_from_pressure()
    #print("raaka lämpö",raw_temp)
    #print("cpu lämpö", cpu_temp)
                                                                                                                         
    temperature = raw_temp - ((cpu_temp - raw_temp)/ 1.5)
    # cpu:n aiheuttaman lämmön eliminointi (ei täysin tarkka, mutta antaa kohtuullisen hyvän loppputuloksen)

    
    temperature = round(temperature, 1)
    pressure = round(pressure, 1)
    humidity = round(humidity, 1)

    # omuodostetaan yhteys LAMP-serverin tietokantaan
    db = MySQLdb.connect("192.168.0.114", "testaaja", "salasana", "sense_data")
    curs=db.cursor()
    insert_stmt = (
	
	
	  "INSERT INTO data_table (date, time, temperature, humidity, pressure) "
      "VALUES (%s, %s, %s, %s, %s)"
    )


    date_ = (os.popen('date +%Y-%m-%d').read())
    time_ = (os.popen('date +%T').read())

    date_  = date_[:-1] # remove (carriage) returns
    time_  = time_[:-1]

    print("date",date_)
    print("time",time_)
    print("temperature",temperature)
    print("humidity", humidity)
    print("pressure",pressure)
	
	

    try:
    	    curs.execute (insert_stmt, (date_, time_, temperature, humidity, pressure))
    	    db.commit()
    	    print("Data committed")

    except:
    	    print( "Error: the database is being rolled back")
    	    db.rollback()

    #msg = "T = %s P = %s H = %s" % (temperature, pressure, humidity)
    #sense.show_message(msg)

    time.sleep(60)