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
        header('Location: login.php');
        exit();
    }

    // Query the database to check user credentials
    $userQuery = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $userQuery);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_assoc($result);

            // Set session variables
            $_SESSION['loggedInStatus'] = true;
            $_SESSION['user_id'] = $user['id']; // Assuming 'id' is the column in your users table

            $_SESSION['message'] = "Logged In Successfully!";

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit();

        }else{
            array_push($errors, "Invalid Email or Password!");
            $_SESSION['errors'] = $errors;
            header('Location: login.php');
            exit();
        }
    }else{
        array_push($errors, "Something Went Wrong!");
        $_SESSION['errors'] = $errors;
        header('Location: login.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        body {
            background-image: url('https://srmap.edu.in/wp-content/uploads/2022/05/transport-4-scaled.jpg');
            background-size: cover;
            margin-top:10px;
            background-position: center;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.8); /* Add opacity to background color */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top:20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .text-center {
            text-align: center;
        }
        .alert {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .driver-login-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
      
    </style>
</head>
<body>
    
<a href="driver-login.php">Driver login</a>
<!-- Rest of your login page content -->


    <div class="container">
        <h2 class="text-center">SRM BUS RESERVATION</h2>
    
        <hr>
        
        <!-- Your PHP code for displaying errors and messages goes here -->

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label>Email Id</label>
                <input type="email" name="email" required />
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required />
            </div>
            <div class="form-group">
                <button type="submit" name="loginBtn" class="btn btn-primary">Login Now</button>
            </div>
            <div class="text-center">
                <a href="register.php">Click here to Register</a>
            </div>
        </form>
    </div>

</body>
</html>

