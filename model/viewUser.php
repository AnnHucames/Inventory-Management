<?php
session_start();

include "navigation.php";

class User {
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Method to get user details by ID
    public function getUserDetails($id) {
        $sql = "SELECT * FROM users_info WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

// Establish the database connection
$conn = connect();

// Check if the 'id' parameter exists in the query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Instantiate the User class and get the user details
    $user = new User($conn);
    $res = $user->getUserDetails($id);
}
?>
<html>
    <head>
        <title> Users </title>
    </head>
    <body>
        <div class="row" style="padding: 50px;">
            <div class="leftcolumn">
                <?php include('product_cards.php')?>
                <div class="pt-20 pl-20">
                    <div class="col-sm-12" style="background-color: white; border: solid rgb(0, 162, 255);">
                        <div class="text-center">
                            <h1 style="color:#130553;"> User Details</h1>
            
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Name:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;"><?php echo ucwords($res['name']) ?></h4>
                                </div>
                            </div>
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Email:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;"><?php echo $res['email']?></h4>
                                </div>
                            </div>
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Status:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;">
                                        <?php 
                                            echo $res['is_active'] == '1' ? "Active" : "Inactive";
                                        ?>
                                    </h4>
                                </div>
                            </div>
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Position:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;">
                                        <?php 
                                            echo $res['is_admin'] == '1' ? "Admin" : "Manager";
                                        ?>
                                    </h4>
                                </div>
                            </div>
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Work since:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;"><?php echo date("F j, Y", strtotime($res['created_at'])) ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('side_info.php')?>
        </div>
    </body>
</html>
