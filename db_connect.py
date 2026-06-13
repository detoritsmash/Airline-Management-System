import mysql.connector

try:
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="airline_db"
    )
    cursor = db.cursor()
    print("Successfully connected to the database!")
except mysql.connector.Error as err:
    print(f"Error: {err}")