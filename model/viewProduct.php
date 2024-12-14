<?php
session_start(); 

include "navigation.php"; 
$conn = connect(); 

class Product {
    private $conn;
    private $id;
    private $productData;

    public function __construct($conn, $id = null) {
        $this->conn = $conn;
        $this->id = $id;
        $this->productData = null;
    }

    public function fetchProductDetails() {
        if ($this->id) {
            $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $this->productData = $result->fetch_assoc();
            }
        }
    }

    public function getProductData() {  
        return $this->productData;
    }

    public function getProductImage() {
        return isset($this->productData['image']) ? htmlspecialchars($this->productData['image'], ENT_QUOTES, 'UTF-8') : null;
    }

    public function getFormattedDate() {
        return isset($this->productData['created_at']) ? htmlspecialchars(date("F j, Y", strtotime(str_replace('-', '/', $this->productData['created_at']))), ENT_QUOTES, 'UTF-8') : '';
    }

    public function getProductName() {
        return isset($this->productData['name']) ? htmlspecialchars(ucwords($this->productData['name']), ENT_QUOTES, 'UTF-8') : '';
    }

    public function getBoughtQuantity() {
        return isset($this->productData['bought']) ? htmlspecialchars($this->productData['bought'], ENT_QUOTES, 'UTF-8') : '';
    }

    public function getSoldQuantity() {
        return isset($this->productData['sold']) ? htmlspecialchars($this->productData['sold'], ENT_QUOTES, 'UTF-8') : '';
    }

    public function getProductId() {
        return isset($this->productData['id']) ? htmlspecialchars($this->productData['id'], ENT_QUOTES, 'UTF-8') : '';
    }
}

if (isset($_GET['id'])) {
    $product = new Product($conn, intval($_GET['id'])); // Instantiate Product class with the ID from URL
    $product->fetchProductDetails(); // Fetch product details from the database
    $productData = $product->getProductData(); // Get product data
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=10"> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../css/product.css"> 
    <link rel="stylesheet" type="text/css" href="../css/navigation.css"> 
    <title>Products</title> <!-- Page title -->
</head>

<body>
    <div class="row" style="padding: 50px;"> 
        <div class="leftcolumn"> 
            <?php include('product_cards.php'); ?> <!-- Include product cards display -->
            <div class="pt-20 pl-20"> <!-- Padding for product details -->
                <div class="col-sm-12" style="background-color: white; border: solid rgb(0, 162, 255);"> <!-- Product detail container -->
                    <div class="text-center">
                        <h1 style="color:#130553;">Product Details</h1> <!-- Header for product details -->
                    </div>
                    <div class="row p-20"> <!-- Row for product information -->
                        <div class="row col-sm-6">
                            <div class="col-sm-6 p-20 pull-left">
                                <?php if ($product->getProductImage()): ?> <!-- Check if the image is set -->
                                    <img src="<?php echo $product->getProductImage(); ?>" height="250" width="250" alt="Product Image"> <!-- Display product image -->
                                <?php else: ?>
                                    <img src="placeholder.jpg" height="250" width="250" alt="No Image Available"> <!-- Placeholder if no image -->
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row col-sm-6">
                            <h4 class="pull-left col-sm-6">Name:</h4> <!-- Label for product name -->
                            <div class="col-sm-6">
                                <h4 class="pull-left" style="color: black;"><?php echo $product->getProductName(); ?></h4> <!-- Display product name -->
                            </div>
                        </div>
                        <div class="row col-sm-6">
                            <h4 class="pull-left col-sm-6">Buy Quantity:</h4> <!-- Label for buy quantity -->
                            <div class="col-sm-6">
                                <h4 class="pull-left" style="color: black;"><?php echo $product->getBoughtQuantity(); ?></h4> <!-- Display bought quantity -->
                            </div>
                        </div>
                        <div class="row col-sm-6">
                            <h4 class="pull-left col-sm-6">Sell Quantity:</h4> <!-- Label for sell quantity -->
                            <div class="col-sm-6">
                                <h4 class="pull-left" style="color: black;"><?php echo $product->getSoldQuantity(); ?></h4> <!-- Display sold quantity -->
                            </div>
                        </div>
                        <div class="row col-sm-6">
                            <h4 class="pull-left col-sm-6">Created at:</h4> <!-- Label for creation date -->
                            <div class="col-sm-6">
                                <h4 class="pull-left" style="color: black;"><?php echo $product->getFormattedDate(); ?></h4> <!-- Display creation date -->
                            </div>
                        </div>
                        <div class="row col-sm-6 text-center" style="padding: 20px"> <!-- Buttons section -->
                            <div class="col-sm-6">
                                <a href="editProduct.php?id=<?php echo $product->getProductId(); ?>"><button class="btn btn-warning">Edit</button></a> <!-- Button to edit product -->
                            </div>
                            <div class="col-sm-6">
                                <a href="deleteProduct.php?id=<?php echo $product->getProductId(); ?>"><button class="btn btn-danger">Delete</button></a> <!-- Button to delete product -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('side_info.php'); ?> 
    </div>
</body>
</html>
