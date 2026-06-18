import os
import mysql.connector
from tabulate import tabulate
import matplotlib.pyplot as plt
import seaborn as sns
from db_connect import get_db_connection

def setup_plot_style():
    """Configures clean, professional aesthetics for Seaborn plots."""
    sns.set_theme(style="whitegrid")
    plt.rcParams["font.family"] = "sans-serif"
    plt.rcParams["figure.dpi"] = 100

def fetch_and_plot_general_metrics(cursor):
    """Fetches total counts of users, bookings, and revenue collected (Text-only)."""
    print("\n====================================================")
    print("📊 1. GENERAL SYSTEM METRICS (Text Summary)")
    print("====================================================")
    
    # 1. Count total registered passengers
    cursor.execute("SELECT COUNT(*) FROM Passengers")
    total_users = cursor.fetchone()[0]
    
    # 2. Count total tickets booked
    cursor.execute("SELECT COUNT(*) FROM Bookings")
    total_bookings = cursor.fetchone()[0]
    
    # 3. Sum up total financial revenue generated from tickets
    cursor.execute("SELECT SUM(Price) FROM Tickets")
    total_revenue = cursor.fetchone()[0]
    total_revenue = total_revenue if total_revenue else 0.0
    
    metrics = [
        ["Total Registered Passengers", total_users],
        ["Total Bookings Processed", total_bookings],
        ["Total Financial Revenue Generated", f"₹{total_revenue:,.2f}"]
    ]
    print(tabulate(metrics, headers=["Metric Indicator", "Value"], tablefmt="grid"))

def fetch_and_plot_revenue_by_airline(cursor):
    """Prints a text table and saves a bar chart for airline revenue."""
    print("\n====================================================")
    print("💰 2. REVENUE CONTRIBUTION BY AIRLINE")
    print("====================================================")
    
    query = """
        SELECT a.AirlineName, SUM(t.Price) AS TotalRevenue
        FROM Tickets t
        JOIN Bookings b ON t.BookingID = b.BookingID
        JOIN Flights f ON b.FlightID = f.FlightID
        JOIN Aircrafts ac ON f.AircraftID = ac.AircraftID
        JOIN Airlines a ON ac.AirlineID = a.AirlineID
        GROUP BY a.AirlineID
        ORDER BY TotalRevenue DESC;
    """
    cursor.execute(query)
    data = cursor.fetchall()
    
    if not data:
        print("⚠️ No ticket data available to compute revenue metrics.")
        return

    # --- PART A: PRINT TEXT TABLE ---
    formatted_table = [[row[0], f"₹{float(row[1]):,.2f}"] for row in data]
    print(tabulate(formatted_table, headers=["Airline Operator", "Total Revenue Generated"], tablefmt="fancy_grid"))

    # --- PART B: GENERATE AND SAVE PLOT ---
    print("📈 Generating visual bar layout asset...")
    airlines = [row[0] for row in data]
    revenue = [float(row[1]) for row in data]

    plt.figure(figsize=(10, 5))
    ax = sns.barplot(x=airlines, y=revenue, hue=airlines, palette="Blues_r", legend=False)
    
    plt.title("Total Revenue Generation by Operating Carrier", fontsize=14, pad=15, fontweight='bold')
    plt.xlabel("Airline Operator", fontsize=11, labelpad=10)
    plt.ylabel("Accumulated Revenue (₹)", fontsize=11, labelpad=10)
    
    for p in ax.patches:
        ax.annotate(f"₹{p.get_height():,.0f}", 
                    (p.get_x() + p.get_width() / 2., p.get_height()), 
                    ha='center', va='center', 
                    xytext=(0, 8), 
                    textcoords='offset points', fontsize=9, fontweight='bold')

    plt.tight_layout()
    plt.savefig("analytics_revenue_by_airline.png")
    plt.close()

def fetch_and_plot_popular_routes(cursor):
    """Prints a text table and saves a horizontal bar chart for high-traffic paths."""
    print("\n====================================================")
    print("🔥 3. MOST POPULAR FLIGHT ROUTES")
    print("====================================================")
    
    query = """
        SELECT 
            a.AirlineName,
            dep.City AS DepartureCity,
            arr.City AS ArrivalCity,
            COUNT(b.BookingID) AS TotalBookings
        FROM Bookings b
        JOIN Flights f ON b.FlightID = f.FlightID
        JOIN Aircrafts ac ON f.AircraftID = ac.AircraftID
        JOIN Airlines a ON ac.AirlineID = a.AirlineID
        JOIN Airports dep ON f.DepartureAirportID = dep.AirportID
        JOIN Airports arr ON f.ArrivalAirportID = arr.AirportID
        GROUP BY b.FlightID
        ORDER BY TotalBookings DESC
        LIMIT 5;
    """
    cursor.execute(query)
    data = cursor.fetchall()
    
    if not data:
        print("⚠️ No booking configurations found to plot route popularity.")
        return

    # --- PART A: PRINT TEXT TABLE ---
    print(tabulate(data, headers=["Airline", "Origin", "Destination", "Tickets Booked"], tablefmt="fancy_grid"))

    # --- PART B: GENERATE AND SAVE PLOT ---
    print("📈 Generating horizontal color gradient asset...")
    routes = [f"{row[1]} ➔ {row[2]}" for row in data]
    bookings = [row[3] for row in data]

    plt.figure(figsize=(10, 5))
    sns.barplot(x=bookings, y=routes, hue=routes, palette="crest", legend=False)
    
    plt.title("Top 5 High-Traffic Flight Paths", fontsize=14, pad=15, fontweight='bold')
    plt.xlabel("Number of Reservations Confirmed", fontsize=11, labelpad=10)
    plt.ylabel("Flight Route", fontsize=11, labelpad=10)
    
    plt.tight_layout()
    plt.savefig("analytics_popular_routes.png")
    plt.close()

def main():
    setup_plot_style()
    
    print("====================================================")
    print("🛫 AIRLINE MANAGEMENT SYSTEM - DATA ANALYTICS HUB 🛬")
    print("====================================================")
    
    conn = get_db_connection()
    if not conn:
        return
        
    cursor = conn.cursor()
    
    # Process analytical pipeline sequences
    fetch_and_plot_general_metrics(cursor)
    fetch_and_plot_revenue_by_airline(cursor)
    fetch_and_plot_popular_routes(cursor)
    
    cursor.close()
    conn.close()
    print("\n🔌 Analytics processing complete. Images saved and text logged safely.")

if __name__ == "__main__":
    main()