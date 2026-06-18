USE Airlines123123;

-- Populating Airlines
INSERT INTO Airlines (AirlineName, Country) VALUES
('Air India', 'India'),
('Emirates', 'UAE'),
('Qatar Airways', 'Qatar'),
('Singapore Airlines', 'Singapore'),
('Etihad Airways', 'UAE');

-- Populating Airports
INSERT INTO Airports (Name, City, Country) VALUES
('Indira Gandhi International Airport', 'Delhi', 'India'),
('Dubai International Airport', 'Dubai', 'UAE'),
('Hamad International Airport', 'Doha', 'Qatar'),
('Changi Airport', 'Singapore', 'Singapore'),
('Kempegowda International Airport', 'Bangalore', 'India');

-- Populating Aircrafts
INSERT INTO Aircrafts (Model, Capacity, AirlineID) VALUES
('Boeing 737', 180, 1),
('Airbus A380', 500, 2),
('Boeing 787', 250, 3),
('Airbus A350', 300, 4),
('Airbus A320', 180, 5);

-- Populating Flights
INSERT INTO Flights (AircraftID, DepartureAirportID, ArrivalAirportID, DepartureTime, ArrivalTime) VALUES
(1, 1, 2, '2025-02-10 08:00:00', '2025-02-10 11:00:00'),
(2, 2, 3, '2025-02-12 14:00:00', '2025-02-12 18:30:00'),
(3, 3, 4, '2025-02-14 09:30:00', '2025-02-14 15:00:00'),
(4, 4, 5, '2025-02-16 06:00:00', '2025-02-16 09:00:00'),
(5, 5, 1, '2025-02-18 19:00:00', '2025-02-18 22:45:00');

-- Populating Passengers (Note: Use valid hashed passwords when testing the login engine)
INSERT INTO Passengers (FullName, Email, Password, Nationality) VALUES
('Rahul Kumar', 'rahul@example.com', 'hashed_password_1', 'Indian'),
('Ayesha Ahmed', 'ayesha@example.com', 'hashed_password_2', 'UAE'),
('John Lee', 'johnlee@example.com', 'hashed_password_3', 'Singaporean'),
('Mohammed Ali', 'mohammed@example.com', 'hashed_password_4', 'Qatari'),
('Samantha Rose', 'samantha@example.com', 'hashed_password_5', 'British');

-- Populating Bookings
INSERT INTO Bookings (PassengerID, FlightID, BookingDate) VALUES
(1, 1, '2025-02-01'),
(2, 2, '2025-02-02'),
(3, 3, '2025-02-03'),
(4, 4, '2025-02-04'),
(5, 5, '2025-02-05');

-- Populating Employees
INSERT INTO Employees (FullName, Position, AirlineID) VALUES
('Rohan Mehta', 'Pilot', 1),
('Sarah Johnson', 'Cabin Crew', 2),
('Ahmed Zayed', 'Ground Staff', 3),
('Lisa Wong', 'Flight Attendant', 4),
('George Mathews', 'Technician', 5);

-- Populating Tickets
INSERT INTO Tickets (BookingID, SeatNumber, Price) VALUES
(1, '12A', 12000.00),
(2, '7F', 34000.00),
(3, '9C', 28000.00),
(4, '4B', 45000.00),
(5, '19D', 15000.00);