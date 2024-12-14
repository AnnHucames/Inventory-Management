<?php
session_start();
include '../controller/connection.php'; 

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register($name, $uname, $email, $password, $rPassword) {
        $message = '';
        
        // Validate passwords
        if ($password !== $rPassword) {
            return "Passwords don't match!";
        }

        // Hash the password
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to insert user data
        $stmt = $this->conn->prepare("INSERT INTO users_info(name, u_name, email, password) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $uname, $email, $hashedPass);

        // Execute the prepared statement
        if ($stmt->execute()) {
            header('Location: login.php');
            exit(); // Ensure no further code is executed
        } else {
            return 'Connection not established!';
        }
    }
}

$conn = connect();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $uname = mysqli_real_escape_string($conn, $_POST['uname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $rPass = mysqli_real_escape_string($conn, $_POST['rpassword']);

    $user = new User($conn);
    $message = $user->register($name, $uname, $email, $pass, $rPass);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="../css/registration.css">
    <title>Registration Form</title>
</head>
<body>
    <form method="POST" action="registration.php" enctype="multipart/form-data">
        <div class="container reg">
            <span>
                <?php 
                    if ($message != '') 
                        echo $message; 
                ?>
            </span>
            <h1>Registration Form</h1>
            <hr>
            <div>
                <label for="name">Your Name<span>*</span></label>
                <input name="name" id="name" type="text" placeholder="Enter Your Name" required>
            </div>
            <div>
                <label for="uname">Your Username<span>*</span></label>
                <input name="uname" id="uname" type="text" placeholder="Enter Your Username" required>
            </div>
            <div>
                <label for="email">Your Email</label>
                <input name="email" id="email" type="text" placeholder="Enter Your Email">
            </div>
            <div>   
                <label for="password">Password<span>*</span></label>
                <input name="password" id="password" type="password" placeholder="Enter A Password" required>
            </div>
            <div>
                <label for="rpassword">Password Confirmation<span>*</span></label>
                <input name="rpassword" id="rpassword" type="password" placeholder="Repeat the Password" required>
            </div>
            <div style="text-align: center; padding: 20px;">
                <input type="submit" class="btn btn-success" value="Submit" name="submit">  
            </div>
            <div style="text-align: center;">
                <p>Already have an account? 
                    <br>
                    <a href="login.php">
                        <input type="button" class="btn btn-primary" value="Sign In">
                    </a>
                </p>
            </div>
        </div>
    </form>
</body>
</html>
