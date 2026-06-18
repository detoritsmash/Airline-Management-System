<?php

require_once 'config/db.php';

//CHECKING IF USER IS LOGGED IN OR NOT. IF NOT, REDIRECT TO LOGIN PAGE
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// If someone didn't click a flight from index.php, stop the script.
if (!isset($_GET['flight_id'])) {
    die("Error: No flight was selected. Please go back to the homepage and select a flight.");
}

$flight_id = intval($_GET['flight_id']); // Get the ID from the URL parameter

// This query gathers the cities and airline names so the user can review them before buying.
$flight_query = "SELECT f.*, a.AirlineName, dep.City AS DepCity, arr.City AS ArrCity 
                 FROM Flights f
                 JOIN Aircrafts ac ON f.AircraftID = ac.AircraftID
                 JOIN Airlines a ON ac.AirlineID = a.AirlineID
                 JOIN Airports dep ON f.DepartureAirportID = dep.AirportID
                 JOIN Airports arr ON f.ArrivalAirportID = arr.AirportID
                 WHERE f.FlightID = ?";

$stmt = $conn->prepare($flight_query);
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();

// If the ID in the URL doesn't match any flight in the database, stop.
if (!$flight) {
    die("Error: This flight does not exist.");
}

// 5. HANDLE "CONFIRM PURCHASE" BUTTON CLICK
// This block runs ONLY when the user submits the HTML form below via POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_id = $_SESSION['user_id'];
    $booking_date = date('Y-m-d'); // Captures today's date
    $seat_number = "14A";          // Hardcoded seat number for simplicity
    $ticket_price = 14500.00;      // Hardcoded pricing for simplicity

    // --- START DATABASE TRANSACTION ---
    // We use a transaction because we need to insert into TWO tables.
    // If one fails, both fail, keeping our database clean.
    $conn->begin_transaction();

    try {
        // Step A: Insert a record into the 'Bookings' table
        $book_sql = "INSERT INTO Bookings (PassengerID, FlightID, BookingDate) VALUES (?, ?, ?)";
        $book_stmt = $conn->prepare($book_sql);
        $book_stmt->bind_param("iis", $passenger_id, $flight_id, $booking_date);
        $book_stmt->execute();
        
        // Grab the brand-new BookingID that MySQL just generated for this insertion
        $new_booking_id = $conn->insert_id; 

        // Step B: Insert a record into the 'Tickets' table using that new BookingID
        $ticket_sql = "INSERT INTO Tickets (BookingID, SeatNumber, Price) VALUES (?, ?, ?)";
        $ticket_stmt = $conn->prepare($ticket_sql);
        $ticket_stmt->bind_param("isd", $new_booking_id, $seat_number, $ticket_price);
        $ticket_stmt->execute();

        // If both inserts worked perfectly, save the changes permanently!
        $conn->commit();
        
        // Take the user straight to their dashboard to see their new ticket
        header("Location: dashboard.php?status=success");
        exit;

    } catch (Exception $e) {
        // If anything goes wrong during the process, undo all changes
        $conn->rollback();
        $error_message = "System Error: Could not complete your booking. " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Your Ticket - Airlines</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        .navbar {
            background-color: #0056b3;
            padding: 15px;
            color: white;
            margin-bottom: 30px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            float: right;
        }
        /* Review Box Layout */
        .review-box {
            background-color: white;
            border: 1px solid #ddd;
            padding: 30px;
            max-width: 500px;
            margin: 0 auto;
        }
        .route-heading {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border: 1px solid #eee;
            margin-bottom: 20px;
        }
        .confirm-btn {
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
        }
        .cancel-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #dc3545;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <strong>Airline Management System</strong>
        <a href="dashboard.php">My Bookings</a>
        <a href="index.php">Home</a>
    </div>

    <div class="review-box">
        <h2>Review Your Flight Details</h2>
        <p style="color: #666;">Please check your itinerary details before confirming.</p>

        <?php if(isset($error_message)): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <div class="route-heading">
            <h3>
                <?= htmlspecialchars($flight['DepCity']) ?> 
                &rarr; 
                <?= htmlspecialchars($flight['ArrCity']) ?>
            </h3>
            <p style="margin: 0; color: #555;">Operating Carrier: <strong><?= htmlspecialchars($flight['AirlineName']) ?></strong></p>
        </div>

        <p><strong>Departure Time:</strong> <?= $flight['DepartureTime'] ?></p>
        <p><strong>Arrival Time:</strong> <?= $flight['ArrivalTime'] ?></p>
        <p><strong>Assigned Seat:</strong> 14A</p>
        <p style="font-size: 18px;"><strong>Total Price:</strong> <span style="color: #28a745; font-weight: bold;">₹14,500.00</span></p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <form method="POST" action="">
            <button type="submit" class="confirm-btn">Confirm & Buy Ticket</button>
        </form>

        <a href="index.php" class="cancel-btn">Cancel and Go Back</a>
    </div>

</body>
</html>
