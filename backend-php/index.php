<?php 

require_once 'config/db.php';

$airports_query = "SELECT AirportID, City, Name FROM Airports ORDER BY City ASC";
$airports_result = $conn->query($airports_query);
$airports = [];
while ($row = $airports_result->fetch_assoc()) {
    $airports[] = $row;
}

//listening for search form submission
$flights = [];
$search_triggered = false;

if (isset($_GET['source']) && isset($_GET['destination'])) {
    $search_triggered = true;
    $source = intval($_GET['source']);
    $destination = intval($_GET['destination']);

    // Relational query joining Flights -> Aircrafts -> Airlines -> Airports
    $search_query = "SELECT f.FlightID, f.DepartureTime, f.ArrivalTime, 
                            a.AirlineName, ac.Model,
                            dep.City AS DepCity, arr.City AS ArrCity
                     FROM Flights f
                     JOIN Aircrafts ac ON f.AircraftID = ac.AircraftID
                     JOIN Airlines a ON ac.AirlineID = a.AirlineID
                     JOIN Airports dep ON f.DepartureAirportID = dep.AirportID
                     JOIN Airports arr ON f.ArrivalAirportID = arr.AirportID
                     WHERE f.DepartureAirportID = ? AND f.ArrivalAirportID = ?";

    $stmt = $conn->prepare($search_query);
    $stmt->bind_param("ii", $source, $destination);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Airlines System - Flight Finder</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        /* Top Navigation Bar */
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
        /* Simple Form Box */
        .search-box {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            max-width: 600px;
            margin: 0 auto 30px auto; /* Centers the box */
        }
        /* Input Layout styling */
        .form-element {
            margin-bottom: 15px;
        }
        .form-element label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-element select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .search-btn {
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }
        /* Results Flight Card Layout */
        .results-section {
            max-width: 600px;
            margin: 0 auto;
        }
        .flight-card {
            background-color: white;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
        }
        .book-link-btn {
            background-color: #0056b3;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <strong>Airline Management System</strong>
        <a href="dashboard.php">My Bookings</a>
        <a href="index.php">Home</a>
    </div>

    <div class="search-box">
        <h2>Search For Available Flights</h2>
        
        <form method="GET" action="index.php">
            
            <div class="form-element">
                <label>Leaving From:</label>
                <select name="source" required>
                    <option value="">-- Select Departure City --</option>
                    <?php foreach ($airports as $ap): ?>
                        <option value="<?= $ap['AirportID'] ?>" <?= (isset($_GET['source']) && $_GET['source'] == $ap['AirportID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ap['City']) ?> (<?= htmlspecialchars($ap['Name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-element">
                <label>Going To:</label>
                <select name="destination" required>
                    <option value="">-- Select Destination City --</option>
                    <?php foreach ($airports as $ap): ?>
                        <option value="<?= $ap['AirportID'] ?>" <?= (isset($_GET['destination']) && $_GET['destination'] == $ap['AirportID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ap['City']) ?> (<?= htmlspecialchars($ap['Name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="search-btn">Search Flights</button>
        </form>
    </div>

    <div class="results-section">
        <?php if ($search_triggered): ?>
            <h3>Available Flight Results</h3>
            
            <?php if (empty($flights)): ?>
                <p style="background-color: #fff; padding: 15px; border: 1px solid #ddd;">
                    No direct flights found between these cities.
                </p>
            <?php else: ?>
                
                <?php foreach ($flights as $flight): ?>
                    <div class="flight-card">
                        <h3><?= htmlspecialchars($flight['AirlineName']) ?></h3>
                        <p><strong>Aircraft Model:</strong> <?= htmlspecialchars($flight['Model']) ?></p>
                        <p><strong>Route:</strong> <?= htmlspecialchars($flight['DepCity']) ?> &rarr; <?= htmlspecialchars($flight['ArrCity']) ?></p>
                        <p><strong>Departure Time:</strong> <?= $flight['DepartureTime'] ?></p>
                        <p><strong>Arrival Time:</strong> <?= $flight['ArrivalTime'] ?></p>
                        
                        <a href="book_flight.php?flight_id=<?= $flight['FlightID'] ?>" class="book-link-btn">
                            Select and Book
                        </a>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>