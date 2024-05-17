<?php
session_start();

// Include the database connection file
require_once 'db_connection.php';

// Check if the login form is submitted
if(isset($_POST['loginBtn']))
{
    // Sanitize and validate input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $errors = [];

    if($email == '' OR $password == ''){
        array_push($errors, "All fields are mandatory");
    }

    if($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        array_push($errors, "Email is not valid");
    }

    // Check for errors
    if(count($errors) > 0)
    {
        $_SESSION['errors'] = $errors;
        header('Location: driver-login.php');
        exit();
    }

    // Query the database to check driver credentials
    $driverQuery = "SELECT * FROM drivers WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $driverQuery);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $driver = mysqli_fetch_assoc($result);

            // Set session variables
            $_SESSION['loggedInStatus'] = true;
            $_SESSION['driver_id'] = $driver['driver_id']; // Assuming 'driver_id' is the column in your drivers table

            $_SESSION['message'] = "Logged In Successfully!";

            // Redirect to dashboard or driver page
            header('Location: driver-dashboard.php');
            exit();

        }else{
            array_push($errors, "Invalid Email or Password!");
            $_SESSION['errors'] = $errors;
            header('Location: driver-login.php');
            exit();
        }
    }else{
        array_push($errors, "Something Went Wrong!");
        $_SESSION['errors'] = $errors;
        header('Location: driver-login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvuCOnrZCLZ9Te5Gy8Byy9v0_feArqKoAbqfOpmywcRA&s'); /* Replace 'path_to_your_image.jpg' with the actual path to your image */
          background-size:cover;
          
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 3px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .form-group .signup-link {
            margin-top: 10px;
            text-align: center;
        }
        .form-group .signup-link a {
            color: #007bff;
            text-decoration: none;
        }
        .form-group .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <h2>Driver Login</h2>
        <form action="driver-login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />
            </div>
            <div class="form-group">
                <button type="submit" name="loginBtn">Login</button>
            </div>
            
        </form>
    </div>
</body>
</html>
