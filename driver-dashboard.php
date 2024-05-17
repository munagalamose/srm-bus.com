<?php
session_start();

// Include the database connection file
require_once 'db_connection.php';

// Check if the driver is logged in
if (!isset($_SESSION['loggedInStatus']) || $_SESSION['loggedInStatus'] !== true) {
    header('Location: driver-login.php');
    exit();
}

// Fetch driver details using driver ID
if (isset($_SESSION['driver_id'])) {
    $driverId = $_SESSION['driver_id'];
    $driverQuery = "SELECT * FROM drivers WHERE driver_id='$driverId'";
    $driverResult = mysqli_query($conn, $driverQuery);
    if ($driverResult && mysqli_num_rows($driverResult) == 1) {
        $driver = mysqli_fetch_assoc($driverResult);
        $welcomeMessage = "Welcome, " . $driver['name'];

        // Query to fetch passenger details for the driver based on bookings
        $passengerQuery = "SELECT u.name, u.phone, u.email, b.bus_no, b.starting_point
                           FROM users u
                           JOIN bookings bk ON u.id = bk.id
                           JOIN buses b ON bk.bus_id = b.bus_id
                           WHERE b.driver_id = '$driverId'";
        $passengerResult = mysqli_query($conn, $passengerQuery);
        if ($passengerResult && mysqli_num_rows($passengerResult) > 0) {
            $passengers = mysqli_fetch_all($passengerResult, MYSQLI_ASSOC);
        } else {
            $passengers = [];
            $noPassengersMessage = "No passengers booked under your buses yet.";
        }
    } else {
        $_SESSION['message'] = "Driver details not found!";
        header('Location: driver-login.php');
        exit();
    }
} else {
    $_SESSION['message'] = "Driver ID not set!";
    header('Location: driver-login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>
<style>
   .center {
    text-align: center;
    margin-top:30px;
}

    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mt-4 card card-body shadow">
                    <?php if (isset($welcomeMessage)) : ?>
                        <h4><?php echo $welcomeMessage; ?></h4>
                    <?php else : ?>
                        <h4>Welcome</h4>
                    <?php endif; ?>
                    <hr>
                    <?php if (!empty($passengers)) : ?>
                        <h5>Passengers Under Your Buses</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Bus No</th>
                                    <th>Starting Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($passengers as $passenger) : ?>
                                    <tr>
                                        <td><?php echo $passenger['name']; ?></td>
                                        <td><?php echo $passenger['phone']; ?></td>
                                        <td><?php echo $passenger['email']; ?></td>
                                        <td><?php echo $passenger['bus_no']; ?></td>
                                        <td><?php echo $passenger['starting_point']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif (!empty($noPassengersMessage)) : ?>
                        <p><?php echo $noPassengersMessage; ?></p>
                    <?php else : ?>
                        <p>No passengers booked under your buses yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="center">
    <a href="live-location.html" class="button">Live Location</a>
 
</div>

        

</body>
</html>
