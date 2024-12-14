<?php
session_start();
$currentPage = 'dashboard.php';
include "navigation.php";

// OOP Class to manage products
class ProductManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getRecentProducts($date) {
        $sql = "SELECT * FROM products WHERE updated_at > '$date'";
        return $this->conn->query($sql);
    }

    public function calculateStock($bought, $sold) {
        return $bought - $sold;
    }

    public function searchProducts($searchTerm) {
        $sql = "SELECT * FROM products WHERE name LIKE '%$searchTerm%'";
        return $this->conn->query($sql);
    }
}

// Connect to the database
$conn = connect();

// Instantiate the ProductManager class
$productManager = new ProductManager($conn);

// Get products updated within the last 7 days
$date = date('Y-m-d', strtotime('-7 days'));
$prod = $productManager->getRecentProducts($date);

// Search functionality
$searchResult = null;
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $searchResult = $productManager->searchProducts($searchTerm);
}
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=10">
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <title>Dashboard</title>
        <style>
            .header-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        </style>
    </head>
    <body>
        <div class="row" style="padding: 40px;">    
            <div class="leftcolumn">
                <?php include('product_cards.php')?>
                <div class="card">
                    <div class="header-container">
                        <h1 style="color: #00a2ff;">Inventory Management</h1>
                        <!-- Print button aligned to the right -->
                        <a href="printing.php" target="_blank" rel="noopener noreferrer">
                            <button class="btn btn-secondary">Print</button>
                        </a>
                    </div>

                    <!-- Search Form -->
                    <form method="POST" action="" style="margin-bottom: 20px;">
                        <input type="text" name="searchTerm" placeholder="Search for products..." class="form-control" required>
                        <button type="submit" name="search" class="btn btn-primary" style="margin-top: 10px;">Search</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Stock In</th>
                                    <th>Stock Out</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Display search results if available, otherwise display recent products
                                $prodToDisplay = $searchResult ? $searchResult : $prod;

                                if (mysqli_num_rows($prodToDisplay) > 0) {
                                    while ($row = mysqli_fetch_assoc($prodToDisplay)) {
                                        $stock = $productManager->calculateStock($row['bought'], $row['sold']);
                                        echo "<tr>";
                                        echo "<td>".$row['name']."</td>";
                                        echo "<td>".$row['bought']."</td>";
                                        echo "<td>".$row['sold']."</td>";
                                        echo "<td>".$stock."</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No products found!</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php include('side_info.php')?>
        </div>
    </body>
</html>
