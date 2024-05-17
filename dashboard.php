<?php
// Start the session and include the database connection
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['loggedInStatus']) || $_SESSION['loggedInStatus'] !== true) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$hasBookedBus = false;
$bookedBuses = [];

// Fetch user details using ID
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT * FROM users WHERE id='$userId'";
    $userResult = mysqli_query($conn, $userQuery);
    if ($userResult && mysqli_num_rows($userResult) == 1) {
        $user = mysqli_fetch_assoc($userResult);
        $welcomeMessage = "Welcome, " . $user['name'];

        // Fetch email from user details
        $email = $user['email'];

        // Query to check if the user has booked a bus
        $checkBookingQuery = "SELECT * FROM bookings WHERE email='$email'";
        $checkBookingResult = mysqli_query($conn, $checkBookingQuery);
        if ($checkBookingResult && mysqli_num_rows($checkBookingResult) > 0) {
            $hasBookedBus = true;

            // Fetch booked buses for the user
            $bookedBusesQuery = "SELECT b.bus_no, b.starting_point, b.driver_name, b.seats_available, bk.status
                                 FROM buses b
                                 JOIN bookings bk ON b.bus_id = bk.bus_id
                                 WHERE bk.email = '$email'";
            $bookedBusesResult = mysqli_query($conn, $bookedBusesQuery);
            if ($bookedBusesResult && mysqli_num_rows($bookedBusesResult) > 0) {
                $bookedBuses = mysqli_fetch_all($bookedBusesResult, MYSQLI_ASSOC);
            }
        }
    } else {
        $_SESSION['message'] = "User details not found!";
        header('Location: login.php');
        exit();
    }
} else {
    $_SESSION['message'] = "User ID not set!";
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container-fluid {
            padding-top: 20px;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .content {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .welcome-message {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
          text-align:center;
        }
        th {
            background-color: #f0f0f0;
        }
        .btn-container {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .a{
            text-align:center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Dashboard</h1>
    </div>

    <div class="container-fluid">
        <div class="content">
            <?php if (isset($welcomeMessage)) : ?>
                <div class="welcome-message"><?php echo $welcomeMessage; ?></div>
            <?php else : ?>
                <div class="welcome-message">Welcome</div>
            <?php endif; ?>

            <?php if ($hasBookedBus) : ?>
                <div class="table-container">
                    <h2>Your Booked Bus detials</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Bus No</th>
                                <th>Starting Point</th>
                                <th>Driver Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookedBuses as $bus) : ?>
                                <tr>
                                    <td><?php echo $bus['bus_no']; ?></td>
                                    <td><?php echo $bus['starting_point']; ?></td>
                                    <td><?php echo $bus['driver_name']; ?></td>
                                    <td><?php echo $bus['status']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p>No buses booked yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Book Bus Button -->
    <?php if (!$hasBookedBus) : ?>
        <div class="btn-container">
            <a href="book-bus.php" class="btn">Book Bus</a>
        </div>
    <?php endif; ?>

    <!-- Logout Button -->
    <a href="logout.php" class="btn logout-btn">Logout</a>
    <div class="a">
        <a  href="live-location.html">View Live Location</a>
    </div> 

</body>
</html>

