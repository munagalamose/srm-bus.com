<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

// Include database connection
require_once 'db_connection.php';

// Initialize variables
$busNo = $startingPoint = $driverId = '';
$errors = [];

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate bus number
    if (empty(trim($_POST["bus_no"]))) {
        $errors[] = "Please enter the bus number.";
    } else {
        $busNo = trim($_POST["bus_no"]);
    }

    // Validate starting point
    if (empty(trim($_POST["starting_point"]))) {
        $errors[] = "Please enter the starting point.";
    } else {
        $startingPoint = trim($_POST["starting_point"]);
    }

    // Validate driver ID
    if (empty(trim($_POST["driver_id"]))) {
        $errors[] = "Please enter the driver ID.";
    } else {
        $driverId = trim($_POST["driver_id"]);
        // Check if driver ID exists in the drivers table
        $checkDriverQuery = "SELECT * FROM drivers WHERE driver_id = '$driverId'";
        $checkDriverResult = mysqli_query($conn, $checkDriverQuery);
        if (!$checkDriverResult || mysqli_num_rows($checkDriverResult) == 0) {
            $errors[] = "Driver ID not found in the database.";
        } else {
            // Fetch driver name
            $driverData = mysqli_fetch_assoc($checkDriverResult);
            $driverName = $driverData['name'];
        }
    }

    // If no errors, insert data into the buses table
    if (empty($errors)) {
        $insertQuery = "INSERT INTO buses (bus_no, starting_point, driver_id, driver_name) 
                        VALUES ('$busNo', '$startingPoint', '$driverId', '$driverName')";
        $insertResult = mysqli_query($conn, $insertQuery);
        if ($insertResult) {
            $_SESSION['success_message'] = "Bus added successfully.";
            header("Location: admin-dashboard.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bus</title>
</head>
<body>
    <h2>Add Bus</h2>
    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Bus Number</label>
            <input type="text" name="bus_no" value="<?php echo $busNo; ?>">
        </div>
        <div class="form-group">
            <label>Starting Point</label>
            <input type="text" name="starting_point" value="<?php echo $startingPoint; ?>">
        </div>
        <div class="form-group">
            <label>Driver ID</label>
            <input type="text" name="driver_id" value="<?php echo $driverId; ?>">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
