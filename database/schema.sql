CREATE DATABASE IF NOT EXISTS Airlines123123;
USE Airlines123123;

-- 1. Airlines Table
CREATE TABLE Airlines (
    AirlineID INT PRIMARY KEY AUTO_INCREMENT,
    AirlineName VARCHAR(100) NOT NULL,
    Country VARCHAR(50) NOT NULL
);

INSERT INTO Airlines (AirlineName, Country) VALUES
('Air India', 'India'),
('Emirates', 'UAE'),
('Qatar Airways', 'Qatar'),
('Singapore Airlines', 'Singapore'),
('Etihad Airways', 'UAE');

-- 2. Airports Table
CREATE TABLE Airports (
    AirportID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    City VARCHAR(50) NOT NULL,
    Country VARCHAR(50) NOT NULL
);

INSERT INTO Airports (Name, City, Country) VALUES
('Indira Gandhi International Airport', 'Delhi', 'India'),
('Dubai International Airport', 'Dubai', 'UAE'),
('Hamad International Airport', 'Doha', 'Qatar'),
('Changi Airport', 'Singapore', 'Singapore'),
('Kempegowda International Airport', 'Bangalore', 'India');

-- 3. Aircrafts Table
CREATE TABLE Aircrafts (
    AircraftID INT PRIMARY KEY AUTO_INCREMENT,
    Model VARCHAR(50) NOT NULL,
    Capacity INT NOT NULL,
    AirlineID INT,
    FOREIGN KEY (AirlineID) REFERENCES Airlines(AirlineID) ON DELETE SET NULL
);

INSERT INTO Aircrafts (Model, Capacity, AirlineID) VALUES
('Boeing 737', 180, 1),
('Airbus A380', 500, 2),
('Boeing 787', 250, 3),
('Airbus A350', 300, 4),
('Airbus A320', 180, 5);

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

INSERT INTO Flights (AircraftID, DepartureAirportID, ArrivalAirportID, DepartureTime, ArrivalTime) VALUES
(1, 1, 2, '2025-02-10 08:00:00', '2025-02-10 11:00:00'),
(2, 2, 3, '2025-02-12 14:00:00', '2025-02-12 18:30:00'),
(3, 3, 4, '2025-02-14 09:30:00', '2025-02-14 15:00:00'),
(4, 4, 5, '2025-02-16 06:00:00', '2025-02-16 09:00:00'),
(5, 5, 1, '2025-02-18 19:00:00', '2025-02-18 22:45:00');

-- 5. Passengers Table
CREATE TABLE Passengers (
    PassengerID INT PRIMARY KEY AUTO_INCREMENT,
    FullName VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,  -- Prevents duplicate user accounts
    Password VARCHAR(255) NOT NULL, -- In a real application, this should be hashed for security
    Nationality VARCHAR(50)
);

INSERT INTO Passengers (FullName, Email, Password, Nationality) VALUES
('Rahul Kumar', 'rahul@example.com', 'hashed_password_1', 'Indian'),
('Ayesha Ahmed', 'ayesha@example.com', 'hashed_password_2', 'UAE'),
('John Lee', 'johnlee@example.com', 'hashed_password_3', 'Singaporean'),
('Mohammed Ali', 'mohammed@example.com', 'hashed_password_4', 'Qatari'),
('Samantha Rose', 'samantha@example.com', 'hashed_password_5', 'British');

-- 6. Bookings Table
CREATE TABLE Bookings (
    BookingID INT PRIMARY KEY AUTO_INCREMENT,
    PassengerID INT,
    FlightID INT,
    BookingDate DATE NOT NULL,
    FOREIGN KEY (PassengerID) REFERENCES Passengers(PassengerID) ON DELETE CASCADE,
    FOREIGN KEY (FlightID) REFERENCES Flights(FlightID) ON DELETE CASCADE,
    INDEX (PassengerID), -- Improves retrieval performance for user dashboards
    INDEX (FlightID)
);

INSERT INTO Bookings (PassengerID, FlightID, BookingDate) VALUES
(1, 1, '2025-02-01'),
(2, 2, '2025-02-02'),
(3, 3, '2025-02-03'),
(4, 4, '2025-02-04'),
(5, 5, '2025-02-05');

-- 7. Employees Table
CREATE TABLE Employees (
    EmployeeID INT PRIMARY KEY AUTO_INCREMENT,
    FullName VARCHAR(100) NOT NULL,
    Position VARCHAR(50) NOT NULL,
    AirlineID INT,
    FOREIGN KEY (AirlineID) REFERENCES Airlines(AirlineID) ON DELETE SET NULL
);

INSERT INTO Employees (FullName, Position, AirlineID) VALUES
('Rohan Mehta', 'Pilot', 1),
('Sarah Johnson', 'Cabin Crew', 2),
('Ahmed Zayed', 'Ground Staff', 3),
('Lisa Wong', 'Flight Attendant', 4),
('George Mathews', 'Technician', 5);

-- 8. Tickets Table
CREATE TABLE Tickets (
    TicketID INT PRIMARY KEY AUTO_INCREMENT,
    BookingID INT,
    SeatNumber VARCHAR(10) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (BookingID) REFERENCES Bookings(BookingID) ON DELETE CASCADE -- Clear ticket if booking is removed
);

INSERT INTO Tickets (BookingID, SeatNumber, Price) VALUES
(1, '12A', 12000.00),
(2, '7F', 34000.00),
(3, '9C', 28000.00),
(4, '4B', 45000.00),
(5, '19D', 15000.00);