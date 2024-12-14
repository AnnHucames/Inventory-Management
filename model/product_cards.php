<?php
class ProductStats {
    private $conn;

    // Constructor accepts a database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Method to get the total count of products
    public function getTotalProducts() {
        $sql = "SELECT COUNT(*) as products FROM products";
        $result = $this->conn->query($sql);
        return mysqli_fetch_assoc($result);
    }

    // Method to get the total bought products
    public function getTotalBought() {
        $sql = "SELECT SUM(bought) as total_bought FROM products";
        $result = $this->conn->query($sql);
        return mysqli_fetch_assoc($result);
    }

    // Method to get the total sold products
    public function getTotalSold() {
        $sql = "SELECT SUM(sold) as total_sold FROM products";
        $result = $this->conn->query($sql);
        return mysqli_fetch_assoc($result);
    }

    // Method to calculate the available stock
    public function getStockAvailable() {
        $totalBought = $this->getTotalBought();
        $totalSold = $this->getTotalSold();
        return $totalBought['total_bought'] - $totalSold['total_sold'];
    }
}

// Assuming $conn is the database connection
$productStats = new ProductStats($conn);

$totalProducts = $productStats->getTotalProducts();
$totalBought = $productStats->getTotalBought();
$totalSold = $productStats->getTotalSold();
$stockAvailable = $productStats->getStockAvailable();
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=10">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="../css/product.css">
    </head>
    <body>
         <div class="row">
            <section style="padding: 20px;">
                <div class="col-sm-3">
                    <div class="card card-green">
                        <h3>Total<br>Products</h3>
                        <h2 style="color: #282828; text-align: center;"><?php echo $totalProducts['products']; ?></h2>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card card-yellow">
                        <h3>Products<br>Stock In</h3>
                        <h2 style="color: #282828; text-align: center;"><?php echo $totalBought['total_bought']; ?></h2>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card card-blue">
                        <h3>Products<br>Stock Out</h3>
                        <h2 style="color: #282828; text-align: center;"><?php echo $totalSold['total_sold']; ?></h2>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card card-red">
                        <h3>Available<br>Stock</h3>
                        <h2 style="color: #282828; text-align: center;"><?php echo $stockAvailable; ?></h2>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
