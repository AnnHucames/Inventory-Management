<?php
session_start();
$currentPage = 'product.php';
include "navigation.php";

class Product {
    private $conn;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function insertProduct($pName, $buy, $img) {
        $m = '';
        $iName = $img['name'];
        $tempName = $img['tmp_name'];
        $format = explode('.', $iName);
        $actualName = strtolower($format[0]);
        $actualFormat = strtolower($format[1]);
        $allowedFormats = ['jpg', 'png', 'jpeg', 'gif'];

        if (in_array($actualFormat, $allowedFormats)) {
            $location = 'Uploads/' . $actualName . '.' . $actualFormat;
            $sql = "INSERT INTO products(name, bought, image, created_at) VALUES ('$pName', '$buy', '$location', current_timestamp())";
            if ($this->conn->query($sql) === true) {
                move_uploaded_file($tempName, $location);
                $m = "Product Inserted!";
            }
        }
        return $m;
    }

    public function fetchAllProducts() {
        $sql = "SELECT * FROM products";
        return $this->conn->query($sql);
    }
}

// Assuming 'connect()' is a function that returns the database connection
$conn = connect();
$product = new Product($conn);

if (isset($_POST['submit'])) {
    $pName = $_POST['pname'];
    $buy = $_POST['buy'];
    $img = $_FILES['pimage'];
    $message = $product->insertProduct($pName, $buy, $img);
}

$res = $product->fetchAllProducts();

$showForm = isset($_GET['add']) && $_GET['add'] == '1';
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=10">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="../css/product.css">
        <link rel="stylesheet" type="text/css" href="../css/navigation.css">
        <style>
            .floating-form {
                display: <?php echo $showForm ? 'block' : 'none'; ?>;
                position: fixed;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                background: white;
                border: 2px solid #ccc;
                padding: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                z-index: 1000;
            }
            .overlay {
                display: <?php echo $showForm ? 'block' : 'none'; ?>;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
        </style>
        <title>Products</title>
    </head>
    
    <body>
        <div class="row" style="padding: 50px;">
            <div class="leftcolumn">
                <?php include('product_cards.php')?>
                <div class="card">
                    <div class="text-center">
                        <h1 style="text-align: center; color: #00a2ff">'Inventory Management'</h1>
                        <a href="product.php?add=1" class="btn btn-primary">Add New Product</a>
                        <h4 style="color: green">
                            <?php
                            // Show success message after deletion
                            if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
                                echo "Product successfully deleted!";
                            }
                            ?>
                        </h4>
                    </div>
                    <div class="overlay"></div>
                    <div class="floating-form">
                        <h2>Add New Product</h2>
                        <form method="POST" action="product.php" enctype="multipart/form-data">
                            <div class="form-group pt-20">
                                <label for="name">Product Name</label>
                                <input name="pname" type="text" class="form-control" placeholder="Product Name" id="name" required>
                            </div>
                            <div class="form-group pt-20">
                                <label for="buy">Buying Amount</label>
                                <input name="buy" type="text" class="form-control" placeholder="Buying Amount" id="buy" required>
                            </div>
                            <div class="form-group pt-20">
                                <label for="pimage">Product Image</label>
                                <input name="pimage" type="file" id="pimage" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-success">Add</button>
                            <a href="product.php" class="btn btn-danger">Cancel</a>
                        </form>
                    </div>
                    <div class="table_container">
                        <h1 style="text-align: center; color:white;">Products Table</h1>
                        <div class="table-responsive">
                            <table class="table table-dark" id="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Stock In</th>
                                        <th>Stock Out</th>
                                        <th>Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($res) > 0) {
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $stock = $row['bought'] - $row['sold'];
                                            echo "<tr>";
                                            echo "<td>".$row['name']."</td>";
                                            echo "<td>".$row['bought']."</td>";
                                            echo "<td>".$row['sold']."</td>";
                                            echo "<td>".$stock."</td>";
                                            echo "<td>
                                                    <a href='viewProduct.php?id=".$row['id']."' class='btn btn-success btn-sm'>View</a>
                                                    <a href='editProduct.php?id=".$row['id']."' class='btn btn-warning btn-sm'>Edit</a>";
                                            if ($thisUser['is_admin'] == 1) {
                                                echo "<a href='deleteProduct.php?id=".$row['id']."' onclick='return confirm(\"Are you sure you want to delete this product?\")' class='btn btn-danger btn-sm'>Delete</a>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No products found!</td></tr>";
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
