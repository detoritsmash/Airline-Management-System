<?php

require_once 'config/db.php';

//MOCK USER SESSION (Lock to Passenger #1: Rahul Kumar for testing consistency)
$_SESSION['user_id'] = 1; 
$passenger_id = $_SESSION['user_id'];

// This block runs ONLY when a user clicks a "Cancel Ticket" link, which passes ?cancel_booking_id=X in the URL
if (isset($_GET['cancel_booking_id'])) {
    $cancel_id = intval($_GET['cancel_booking_id']);
    
    
    $delete_sql = "DELETE FROM Bookings WHERE BookingID = ? AND PassengerID = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $cancel_id, $passenger_id);
    $delete_stmt->execute();
    
    // Refreshing the dashboard 
    header("Location: dashboard.php?status=cancelled");
    exit;
}

//FETCHING PASSENGER NAME AND ACCOUNT INFO
$user_sql = "SELECT FullName, Email FROM Passengers WHERE PassengerID = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $passenger_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

//THE ADVANCED SQL JOIN QUERY
$itinerary_query = "SELECT b.BookingID, t.TicketID, t.SeatNumber, t.Price,
                           f.DepartureTime, f.ArrivalTime, a.AirlineName,
                           dep.City AS DepCity, arr.City AS ArrCity
                    FROM Bookings b
                    JOIN Tickets t ON b.BookingID = t.BookingID
                    JOIN Flights f ON b.FlightID = f.FlightID
                    JOIN Aircrafts ac ON f.AircraftID = ac.AircraftID
                    JOIN Airlines a ON ac.AirlineID = a.AirlineID
                    JOIN Airports dep ON f.DepartureAirportID = dep.AirportID
                    JOIN Airports arr ON f.ArrivalAirportID = arr.AirportID
                    WHERE b.PassengerID = ? 
                    ORDER BY b.BookingID DESC";

$itinerary_stmt = $conn->prepare($itinerary_query);
$itinerary_stmt->bind_param("i", $passenger_id);
$itinerary_stmt->execute();
$bookings = $itinerary_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard - Airline Management System</title>
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
        /* Notification Alert Boxes */
        .alert-box {
            padding: 15px;
            border-radius: 4px;
            max-width: 900px;
            margin: 0 auto 20px auto;
            font-weight: bold;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Layout Container */
        .dashboard-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 25px;
            border: 1px solid #ddd;
        }
        /* Data Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .cancel-link-btn {
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <strong>Airline Management System</strong>
        <a href="dashboard.php">My Bookings</a>
        <a href="index.php">Home</a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert-box success">Flight successfully booked! Your ticket order has been processed below.</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['status']) && $_GET['status'] === 'cancelled'): ?>
        <div class="alert-box danger">Ticket reservation canceled. Your record has been removed.</div>
    <?php endif; ?>

    <div class="dashboard-container">
        <h2>Passenger Profile Dashboard</h2>
        <p><strong>Passenger Name:</strong> <?= htmlspecialchars($user['FullName']) ?></p>
        <p><strong>Registered Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">

        <h3>Your Booking History & Active Itineraries</h3>
        
        <?php if ($bookings->num_rows === 0): ?>
            <p style="color: #666; font-style: italic;">You have no current active flight itineraries registered.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Airline</th>
                        <th>Route</th>
                        <th>Departure Window</th>
                        <th>Seat</th>
                        <th>Price Paid</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($b = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $b['BookingID'] ?></td>
                            <td><strong><?= htmlspecialchars($b['AirlineName']) ?></strong></td>
                            <td><?= htmlspecialchars($b['DepCity']) ?> &rarr; <?= htmlspecialchars($b['ArrCity']) ?></td>
                            <td><?= $b['DepartureTime'] ?></td>
                            <td><?= htmlspecialchars($b['SeatNumber']) ?></td>
                            <td>₹<?= number_format($b['Price'], 2) ?></td>
                            <td>
                                <a href="dashboard.php?cancel_booking_id=<?= $b['BookingID'] ?>" 
                                   class="cancel-link-btn"
                                   onclick="return confirm('Are you sure you want to cancel this ticket selection?');">
                                    Cancel Ticket
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>