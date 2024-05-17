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
$name = $phone = $email = $password = '';
$errors = [];

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $errors[] = "Please enter the driver's name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone"]))) {
        $errors[] = "Please enter the phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $errors[] = "Please enter the email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $errors[] = "Please enter the password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If no errors, insert data into the drivers table
    if (empty($errors)) {
      
        $insertQuery = "INSERT INTO drivers (name, phone_number, email, password) VALUES ('$name', '$phone', '$email', '$password')";
        $insertResult = mysqli_query($conn, $insertQuery);
        if ($insertResult) {
            $_SESSION['success_message'] = "Driver added successfully.";
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
    <title>Add Driver</title>
</head>
<body>
    <h2>Add Driver</h2>
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
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $name; ?>">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo $phone; ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $email; ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" value="<?php echo $password; ?>">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
