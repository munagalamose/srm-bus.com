<?php
session_start();

// Include database connection
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedInStatus']) || $_SESSION['loggedInStatus'] !== true) {
    header('Location: login.php');
    exit();
}

// Get the user ID from the session
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch user details using ID
    $userQuery = "SELECT * FROM users WHERE id='$userId'";
    $userResult = mysqli_query($conn, $userQuery);
    if ($userResult && mysqli_num_rows($userResult) == 1) {
        $user = mysqli_fetch_assoc($userResult);
        $id = $user['id'];
        $email = $user['email'];
    } else {
        // User details not found
        $_SESSION['message'] = "User details not found!";
        header('Location: login.php');
        exit();
    }
} else {
    // User ID not set in session
    $_SESSION['message'] = "User ID not set!";
    header('Location: login.php');
    exit();
}

// Query to fetch all buses
$busQuery = "SELECT * FROM buses";
$busResult = mysqli_query($conn, $busQuery);

// Check if there are any buses available
if (mysqli_num_rows($busResult) > 0) {
    // Buses available, fetch and display details
    $buses = mysqli_fetch_all($busResult, MYSQLI_ASSOC);
} else {
    // No buses available
    $buses = [];
    $noBusesMessage = "No buses available.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Bus</title>
   
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mt-4 card card-body shadow">
                    <h4>Available Buses</h4>
                    <hr>
                    <?php if (!empty($buses)) : ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Bus Number</th>
                                    <th>Starting Point</th>
                                    <th>Driver Name</th>
                                    <th>Seats Available</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($buses as $bus) : ?>
                                    <tr>
                                        <td><?php echo $bus['bus_no']; ?></td>
                                        <td><?php echo $bus['starting_point']; ?></td>
                                        <td><?php echo $bus['driver_name']; ?></td>
                                        <td><?php echo $bus['seats_available']; ?></td>
                                        <td>
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                                <input type="hidden" name="bus_id" value="<?php echo $bus['bus_id']; ?>">
                                                <button type="submit" name="bookBtn" class="btn btn-primary">Book</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php echo isset($noBusesMessage) ? $noBusesMessage : "No buses available."; ?></p>
                    <?php endif; ?>

                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bookBtn'])) {
                        // Get the bus ID from the form
                        $busId = $_POST['bus_id'];

                        // Insert booking data into the bookings table
                        $sql = "INSERT INTO bookings (id, bus_id, status, email) VALUES ('$id', '$busId', 'confirmed', '$email')";
                        mysqli_query($conn, $sql);

                        // Redirect to the dashboard after booking
                        header("Location: dashboard.php");
                        exit();
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    

</body>
</html>
