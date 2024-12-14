<?php
session_start();

// Include necessary files
include "navigation.php";

// Product class to handle logic
class Product
{
    private $conn;
    private $id;
    private $pName;
    private $buy;
    private $sell;
    private $message = '';
    private $productData;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function setProductId($id)
    {
        $this->id = $id;
    }

    public function fetchProduct()
    {
        if (isset($_GET['id'])) {
            $this->id = $_GET['id'];
            $sql = "SELECT * FROM products WHERE id = '$this->id' LIMIT 1";
            $result = $this->conn->query($sql);
            $this->productData = $result ? $result->fetch_assoc() : null;
        }
    }

    public function updateProduct()
    {
        if (isset($_POST['id'])) {
            $this->id = $_POST['id'];
            $this->pName = $_POST['pname'];
            $this->buy = intval($_POST['buy']);
            $this->sell = intval($_POST['sell']);

            // Check if Buy Quantity is greater than or equal to Sell Quantity
            if ($this->buy >= $this->sell) {
                if (isset($_POST['Submit'])) {
                    // Prepare and execute the UPDATE SQL query
                    $sql = "UPDATE products SET name = '$this->pName', bought = '$this->buy', sold = '$this->sell' WHERE id = '$this->id'";
                    if ($this->conn->query($sql) === true) {
                        $this->message = "Product Updated Successfully!";
                        header("Location: product.php"); // Redirect to product listing after update
                        exit;
                    } else {
                        $this->message = "Error: Could not update product!";
                    }
                }
            } else {
                $this->message = "Buy quantity must be greater than or equal to Sell quantity!";
            }
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getProductData()
    {
        return $this->productData;
    }

    public function getProductImage()
    {
        return $this->productData['image'];
    }
}

// Database connection
$conn = connect();
$product = new Product($conn);

// Handle form submission and update logic
$product->updateProduct();

// Fetch product data for editing
$product->fetchProduct();
$productData = $product->getProductData();
$img = $product->getProductImage();
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=10">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../css/product.css">
    <link rel="stylesheet" type="text/css" href="../css/navigation.css">
    <title> Products </title>
</head>

<body>
    <div class="row" style="padding: 50px;">
        <div class="leftcolumn">
            <?php include('product_cards.php') ?>
            <div class="pt-20 pl-20">
                <div class="col-sm-12" style="background-color: white; border: solid rgb(0, 162, 255);">
                    <div class="text-center">
                        <h1 style="color:#130553;"> Edit Product</h1>
                        <h4 style="color: red;"> <?php echo $product->getMessage(); ?> </h4> <!-- Updated to use getMessage() -->
                    </div>
                    <div class="row p-20">
                        <div class="row col-sm-6">
                            <div class="col-sm-6 p-20 pull-left">
                                <img src="<?php echo $img; ?>" height="250" width="250">
                            </div>
                        </div>
                        <form method="POST" action="editProduct.php" class="row">
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Name:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;">
                                        <input type="text" class="login-input" name="pname" value="<?php echo $productData['name']; ?>" placeholder="Product Name" required>
                                    </h4>
                                </div>
                            </div>
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Buy Quantity:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;">
                                        <input type="number" class="login-input" name="buy" value="<?php echo $productData['bought']; ?>" placeholder="Buy Quantity" required>
                                    </h4>
                                </div>
                            </div>
                            <div class="row col-sm-6">
                                <h4 class="pull-left col-sm-6">Sell Quantity:</h4>
                                <div class="col-sm-6">
                                    <h4 class="pull-left" style="color: black;">
                                        <input type="number" class="login-input" name="sell" value="<?php echo $productData['sold']; ?>" placeholder="Sell Quantity" required>
                                    </h4>
                                </div>
                            </div>
                            <input type="hidden" value="<?php echo $productData['id']; ?>" name="id">
                            <div class="row col-sm-6 text-center" style="padding: 20px">
                                <div class="col-sm-6">
                                    <input class="btn btn-success" type="submit" name="Submit" value="Submit">
                                </div>
                            </div>
                        </form>
                    </div>                               
                </div>
            </div>
        </div>
        <?php include('side_info.php') ?>
    </div>
</body>
</html>
