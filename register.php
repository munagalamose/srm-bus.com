<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRMAP REGISTRATION FORM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://srmap.edu.in/wp-content/uploads/2022/05/transport-1-scaled.jpg'); /* Replace 'background.jpg' with your actual image path */
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .card h4 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .card form input,
        .card form select,
        .card form button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .card form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .card form button:hover {
            background-color: #0056b3;
        }
        .text-center {
            text-align: center;
        }
        .text-center a {
            text-decoration: none;
            color: #007bff;
        }
        .text-center a:hover {
            color: #0056b3;
        }
        .alert {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
        }
        .alert-warning {
            background-color: #ffeeba;
            border: 1px solid #ffc107;
            color: #856404;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h4>REGISTER</h4>
            <hr>
            <?php
            session_start();
            if(isset($_SESSION['errors']) && count($_SESSION['errors']) > 0){
                foreach($_SESSION['errors'] as $error){
                    ?>
                    <div class="alert alert-warning"><?= $error; ?></div>
                    <?php
                }
                unset($_SESSION['errors']);
            }

            if(isset($_SESSION['message'])){
                echo '<div class="alert alert-success">'.$_SESSION['message'].'</div>';
                unset($_SESSION['message']);
            }

            require_once "db_connection.php";

            if(isset($_POST['registerBtn']))
            {
                $name = mysqli_real_escape_string($conn,$_POST['name']);
                $phone = mysqli_real_escape_string($conn,$_POST['phone']);
                $email = mysqli_real_escape_string($conn,$_POST['email']);
                $password = mysqli_real_escape_string($conn,$_POST['password']);
                $category = mysqli_real_escape_string($conn,$_POST['category']);

                $errors = [];

                if($name == '' OR $phone == '' OR $email == '' OR $password == '' OR $category == ''){
                    array_push($errors, "All fields are required");
                }

                if($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
                    array_push($errors, "Enter valid email address");
                }

                if($email != ''){
                    $userCheck = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
                    if($userCheck){
                        if(mysqli_num_rows($userCheck) > 0){
                            array_push($errors, "Email already registered");
                        }
                    }else{
                        array_push($errors, "Something Went Wrong!");
                    }
                }

                if(count($errors) > 0){
                    $_SESSION['errors'] = $errors;
                    header('Location: register.php');
                    exit();
                }

                $query = "INSERT INTO users (name, phone, email, password, category) VALUES ('$name', '$phone', '$email', '$password', '$category')";
                $userResult = mysqli_query($conn, $query);

                if($userResult){
                    $_SESSION['message'] = "Registered Successfully";
                    header('Location: login.php');
                    exit();
                }else{
                    $_SESSION['message'] = "Something Went Wrong";
                    header('Location: register.php');
                    exit();
                }
            }
            ?>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" />
                </div>
                <div class="mb-3">
                    <label>Phone</label>
                    <input type="number" name="phone" class="form-control" />
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" />
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" />
                </div>
                <div class="mb-3">
                    <label>Select Category</label>
                    <select name="category" class="form-control">
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="driver">Driver</option>
                    </select>
                </div>
                <div class="mb-3">
                    <button type="submit" name="registerBtn" class="btn btn-primary w-100">Submit</button>
                </div>
                <div class="text-center">
                    <a href="login.php">Click here to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
