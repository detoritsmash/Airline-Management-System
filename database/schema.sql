CREATE DATABASE IF NOT EXISTS Airlines123123;
USE Airlines123123;

-- 1. Airlines Table
CREATE TABLE Airlines (
    AirlineID INT PRIMARY KEY AUTO_INCREMENT,
    AirlineName VARCHAR(100) NOT NULL,
    Country VARCHAR(50) NOT NULL
);

-- 2. Airports Table
CREATE TABLE Airports (
    AirportID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    City VARCHAR(50) NOT NULL,
    Country VARCHAR(50) NOT NULL
);

-- 3. Aircrafts Table
CREATE TABLE Aircrafts (
    AircraftID INT PRIMARY KEY AUTO_INCREMENT,
    Model VARCHAR(50) NOT NULL,
    Capacity INT NOT NULL,
    AirlineID INT,
    FOREIGN KEY (AirlineID) REFERENCES Airlines(AirlineID) ON DELETE SET NULL
);

-- 4. Flights Table
CREATE TABLE Flights (
    FlightID INT PRIMARY KEY AUTO_INCREMENT,
    AircraftID INT,
    DepartureAirportID INT,
    ArrivalAirportID INT,
    DepartureTime DATETIME NOT NULL,
    ArrivalTime DATETIME NOT NULL,
    FOREIGN KEY (AircraftID) REFERENCES Aircrafts(AircraftID) ON DELETE SET NULL,
    FOREIGN KEY (DepartureAirportID) REFERENCES Airports(AirportID) ON DELETE CASCADE,
    FOREIGN KEY (ArrivalAirportID) REFERENCES Airports(AirportID) ON DELETE CASCADE
);

-- 5. Passengers Table
CREATE TABLE Passengers (
    PassengerID INT PRIMARY KEY AUTO_INCREMENT,
    FullName VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,  
    Password VARCHAR(255) NOT NULL,
    Nationality VARCHAR(50)
);

-- 6. Bookings Table
CREATE TABLE Bookings (
    BookingID INT PRIMARY KEY AUTO_INCREMENT,
    PassengerID INT,
    FlightID INT,
    BookingDate DATE NOT NULL,
    FOREIGN KEY (PassengerID) REFERENCES Passengers(PassengerID) ON DELETE CASCADE,
    FOREIGN KEY (FlightID) REFERENCES Flights(FlightID) ON DELETE CASCADE,
    INDEX (PassengerID), 
    INDEX (FlightID)
);

-- 7. Employees Table
CREATE TABLE Employees (
    EmployeeID INT PRIMARY KEY AUTO_INCREMENT,
    FullName VARCHAR(100) NOT NULL,
    Position VARCHAR(50) NOT NULL,
    AirlineID INT,
    FOREIGN KEY (AirlineID) REFERENCES Airlines(AirlineID) ON DELETE SET NULL
);

-- 8. Tickets Table
CREATE TABLE Tickets (
    TicketID INT PRIMARY KEY AUTO_INCREMENT,
    BookingID INT,
    SeatNumber VARCHAR(10) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (BookingID) REFERENCES Bookings(BookingID) ON DELETE CASCADE
);