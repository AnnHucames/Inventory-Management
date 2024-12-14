<?php
session_start();

include "../controller/connection.php"; 
class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login($uname, $pass) {
        $message = '';

        
        $stmt = $this->conn->prepare("SELECT * FROM users_info WHERE u_name = ?");
        $stmt->bind_param("s", $uname); 
        $stmt->execute(); 
        $res = $stmt->get_result(); 

        if ($res->num_rows === 1) { 
            $user = $res->fetch_assoc(); 

            
            if (password_verify($pass, $user['password'])) {
                
                $stmt = $this->conn->prepare("UPDATE users_info SET last_login_time = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->bind_param("i", $user['id']); 
                $stmt->execute(); 

            
                $_SESSION['user'] = $user['name']; 
                $_SESSION['userid'] = $user['id']; 
                header('Location: ../model/dashboard.php'); 
                exit(); 
            } else {
                
                $message = 'Username or password is incorrect!';
            }
        } else {
            
            $message = 'Username or password is incorrect!';
        }

        return $message;
    }
}

$conn = connect(); 
$message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $uname = mysqli_real_escape_string($conn, $_POST['uname']); 
    $pass = mysqli_real_escape_string($conn, $_POST['password']); 
    $user = new User($conn); 
    $message = $user->login($uname, $pass); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="../css/login.css"> 
    <title>Login</title> 
</head>
<body>
    <div class="logo">
        
    </div>
    <form method="POST"> 
        <div class="box bg-img"> 
            <div class="content"> 
                <h2>Log<span> In</span></h2> 
                <hr> 
                <div class="forms"> 
                    <p style="color: red; padding: 20px;">
                        <?php if ($message) echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?> 
                    </p>
                    <div class="user-input">
                        <input name="uname" type="text" class="login-input" placeholder="Username" required /> 
                        <i class="fas fa-user"></i> 
                    </div>
                    <div class="pass-input">
                        <input name="password" type="password" class="login-input" placeholder="Password" required /> 
                    </div>
                </div>

                <button class="login-btn" type="submit">Sign In</button> 
                <p class="new-account">Not a user? <a href="registration.php">Sign Up now!</a></p> 
            </div>
        </div>
    </form>
</body>
</html>
