<?php
session_start();

class Database {
    private $conn;

    public function __construct() {
        include '../controller/connection.php';
        $this->conn = connect();
    }

    public function getConnection() {
        return $this->conn;
    }
}

class User {
    private $conn;
    private $userId;

    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }

    public function getUserInfo() {
        $sqlUser = "SELECT * FROM users_info WHERE id='$this->userId'";
        return mysqli_fetch_assoc($this->conn->query($sqlUser));
    }

    public function isAuthorized() {
        $thisUser = $this->getUserInfo();
        return $thisUser && ($thisUser['is_admin'] == 1 || $thisUser['is_active'] == 1);
    }
}

class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE id=? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

class Auth {
    public static function checkSession() {
        if (!isset($_SESSION['userid'])) {
            header("Location: ../view/login.php");
            exit();
        }
    }

    public static function getUserId() {
        return $_SESSION['userid'];
    }
}

Auth::checkSession();
$userId = Auth::getUserId();

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn, $userId);
if (!$user->isAuthorized()) {
    echo "User not authorized to delete products.";
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $product = new Product($conn);

    if ($product->deleteProduct($id)) {
        header("Location: product.php?deleted=1");
        exit();
    } else {
        echo "Error deleting product: " . $conn->error;
    }
} else {
    header("Location: product.php");
    exit();
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/product.css">
    <link rel="stylesheet" type="text/css" href="../css/navigation.css">
    <title>Delete Product</title>
</head>
<body>
    <div class="container" style="padding: 50px;">
        <div class="row">
            <div class="col-sm-12">
                <h2>Product Deleted Successfully!</h2>
                <a href="product.php" class="btn btn-primary">Back to Products</a>
            </div>
        </div>
    </div>
</body>
</html>
