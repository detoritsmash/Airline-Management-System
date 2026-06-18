import mysql.connector

def get_db_connection():
    """Establishes and returns a clean connection to the MySQL database."""
    try:
        db = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",  
            database="Airlines123123"
        )
        return db
    except mysql.connector.Error as err:
        print(f"Database Connection Error: {err}")
        return None