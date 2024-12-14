<?php
session_start();

$currentPage = 'user.php';
include "navigation.php";

// User class to handle user-related operations
class User {
    private $conn;
    private $id;
    private $userData;

    public function __construct($conn, $id) {
        $this->conn = $conn;
        $this->id = $id;
        $this->userData = $this->getUserData();
    }

    // Fetch user data
    private function getUserData() {
        $sql = "SELECT * FROM users_info WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update user information
    public function updateInfo($uname, $email, $pass, $npass, $cpass) {
        $message = '';
        
        if ($this->userData['password'] == md5($pass)) {
            $sql = "UPDATE users_info SET ";
            if (!empty($uname) && $uname != $this->userData['name']) {
                $sql .= "name = ?,";
            }
            if (!empty($email) && $email != $this->userData['email']) {
                $sql .= "email = ?,";
            }
            if (!empty($npass) && $npass == $cpass && $npass != $this->userData['password']) {
                $sql .= "password = ?,";
                $npass = md5($npass);
            }
            $sql = rtrim($sql, ',');
            $sql .= " WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            if (!empty($uname) && !empty($email) && !empty($npass)) {
                $stmt->bind_param("sssi", $uname, $email, $npass, $this->id);
            } elseif (!empty($uname) && !empty($email)) {
                $stmt->bind_param("ssi", $uname, $email, $this->id);
            } elseif (!empty($uname) && !empty($npass)) {
                $stmt->bind_param("ssi", $uname, $npass, $this->id);
            } elseif (!empty($email) && !empty($npass)) {
                $stmt->bind_param("ssi", $email, $npass, $this->id);
            } else {
                $stmt->bind_param("si", $uname, $this->id);
            }

            $stmt->execute();
            $message = 'User Information Successfully Updated!';
        } else {
            $message = "Credentials mismatch!";
        }

        return $message;
    }

    // Get all users data
    public function getAllUsers() {
        $sql = "SELECT * FROM users_info";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function getUserDataById() {
        return $this->userData;
    }
}

// Initialize connection
$conn = connect();
$user = new User($conn, $_SESSION['userid']);

if (isset($_POST['submit'])) {
    $uname = mysqli_real_escape_string($conn, $_POST['uname']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $npass = mysqli_real_escape_string($conn, $_POST['npass']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpass']);
    $message = $user->updateInfo($uname, $_POST['email'], $pass, $npass, $cpass);
}

$res = $user->getAllUsers();
?>

<html>
<head>
    <title> Users </title>
</head>
<body>
    <div class="row" style="padding: 40px;">
        <div class="leftcolumn">
            <?php include('product_cards.php')?>
            <div class="card">
                <div class="text-center">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('updateInfo').style.display='block'">
                        Update Your Info
                    </button>
                    <h4 style="color: green"><?php echo isset($message) ? $message : ''; ?></h4>
                    <div class="modal" id="updateInfo" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
                        <div style=" margin: 15% auto; border: 1px solid #888; width: 80%;">
                            <div class="modal-header">
                            <button type="button" class="close" style="background-color: white;" onclick="document.getElementById('updateInfo').style.display='none'">
                            <span aria-hidden="true">&times;</span>
                            </button>
                                <h2 style="color: black;"><?php echo $user->getUserDataById()['name']; ?></h2>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="user.php" enctype="multipart/form-data">
                                    <div class="form-group pt-20">
                                        <div class="col-sm-4">
                                            <label for="uname" class="pr-10"> User Name</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="uname" type="text" class="login-input" placeholder="User Name" id="uname" value="<?php echo $user->getUserDataById()['name']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group pt-20">
                                        <div class="col-sm-4">
                                            <label for="email" class="pr-10"> Email </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="email" type="email" class="login-input" placeholder="Email Address" value="<?php echo $user->getUserDataById()['email']; ?>" id="email" required>
                                        </div>
                                    </div>
                                    <div class="form-group pt-20">
                                        <div class="col-sm-4">
                                            <label for="pass" class="pr-10"> Password</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="pass" class="login-input" type="password" id="pass" required>
                                        </div>
                                    </div>
                                    <div class="form-group pt-20">
                                        <div class="col-sm-4">
                                            <label for="npass" class="pr-10">New Password</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="npass" class="login-input" type="password" id="npass">
                                        </div>
                                    </div>
                                    <div class="form-group pt-20">
                                        <div class="col-sm-4">
                                            <label for="cpass" class="pr-10">Confirm New Password</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="cpass" class="login-input" type="password" id="cpass">
                                        </div>
                                    </div>
                                    <div class="form-group" style="text-align: center;">
                                        <button type="submit" value="submit" name="submit" class="btn btn-success">Change</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table_container">
                    <h1 style="text-align: center; color:black;">Users Table</h1>
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Is Active</th>
                                    <?php
                                    if ($user->getUserDataById()['is_active'] == 1) {
                                        echo '<th>Last Login Time</th>';
                                        echo '<th>Action</th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($res) > 0) {
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        echo '<tr>';
                                        echo '<td>' . $row['name'] . '</td>';
                                        echo '<td>' . $row['email'] . '</td>';
                                        $active = ($row['is_active'] == '1') ? "Active" : "Inactive";
                                        echo '<td>' . $active . '</td>';
                                        if ($user->getUserDataById()['is_active'] == 1) {
                                            echo '<td>' . date("Y-m-d h:i:sa", strtotime($row['last_login_time'])) . '</td>';
                                            echo "<td><a href='viewUser.php?id=" . $row['id'] . "' class='btn btn-success btn-sm'>" . 
                                                "<span class='glyphicon glyphicon-eye-open'></span> </a>";
                                        }
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include('side_info.php')?>
    </div>
</body>
</html>
