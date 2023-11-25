<?php
include_once "database.php";
class Product{
    private int $productId;
    private int $userId;
    private string $title;
    private string $description;
    private float $price;


    public function __construct
    (
        $pProductId= -1,
        $pUserId = -1,
        $pDescription = "",
        $pTitle = "",
        $pPrice = -1
    )
    {
        // we check all cases inside the function
        self::initializeProperties($pProductId,$pUserId,$pDescription,$pTitle,$pPrice);
    }


    public function getProductId() : int
    {
        return $this->productId;
    }

    public function setProductId(string $pProductId) : void
    {
        $this->productId = $pProductId;
    }


    public function getDescription() : string
    {
        return $this->description;
    }

    public function setDescription($pDescription) : void
    {
        $this->description = $pDescription;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $pUserId): void
    {
        $this->userId = $pUserId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $pTitle): void
    {
        $this->title = $pTitle;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $pPrice): void
    {
        $this->price = $pPrice;
    }


    private function initializeProperties ($pProductId,$pUserId,$pDescription,$pTitle,$pPrice) : void
    {
        if ($pProductId < 0){
            // use the default initialization if nothing was sent in the param
            return;
        }
        else if (
            $pProductId > 0 &&
            $pUserId > 0 &&
            strlen($pDescription) > 0 &&
            strlen($pTitle) > 0 &&
            $pPrice > 0
        ){
            $this->productId = $pProductId;
            $this->userId = $pUserId;
            $this->description = $pDescription;
            $this->title = $pTitle;
            $this->price = $pPrice;
        }
        else if ($pProductId > 0){
            // initialize only if the Product id was sent

            $mySqlConnection = openDatabaseConnection();

            $getProductByIdQuery = "SELECT * FROM product WHERE product_id = ?";
            $prepGetProductById = $mySqlConnection->prepare($getProductByIdQuery);
            $prepGetProductById->bind_param("i",$pProductId);
            $prepGetProductById->execute();
            $getProductResult = $prepGetProductById->get_result();

            if ($getProductResult->num_rows > 0){

                $queryProductAssocRow = $getProductResult->fetch_assoc();

                $this->productId = $pProductId;
                $this->userId = $queryProductAssocRow['user_id'];
                $this->title = $queryProductAssocRow['title'];
                $this->description = $queryProductAssocRow['description'];
                $this->price = $queryProductAssocRow['price'];
            }
        }

    }


    public function listProduct() : array{

        $list = array();
        $mySqlConnection = openDatabaseConnection();

        $sqlQueryGetProducts = "SELECT * FROM `PRODUCTS`";
        $results = $mySqlConnection->query($sqlQueryGetProducts);
        while ($row = $results->fetch_assoc()){
            $product = new Product();
            $product->productId = $row['product_id'];
            $product->userId = $row['user_id'];
            $product->title = $row['title'];
            $product->description = $row['description'];
            $product->price = $row['price'];
        }
        array_push($list,$product);

        return $list;
    }

    public static function listProductsByCategory($categoryId): ?array
    {
        $products = [];
        $mySqliConnection = openDatabaseConnection();
        $SQL = "SELECT * FROM product p JOIN product_category pc ON p.product_id = pc.product_id  WHERE pc.category_id = ?";
        $stmt = $mySqliConnection->prepare($SQL);
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $results = $stmt->get_result();
        // Fetch user data (assuming you have a user class or similar)
        while ($row = $results->fetch_assoc()){
            $product = new Product();
            $product->productId = $row['product_id'];
            $product->userId = $row['user_id'];
            $product->title = $row['title'];
            $product->description = $row['description'];
            $product->price = $row['price'];
            $products[] = $product;
        }
        return $products;
    }

    public static function getProductsByUserId($pUserId): ?array
    {
        $products = [];
        $mySqliConnection = openDatabaseConnection();
        $sql = "SELECT * FROM product WHERE user_id = ?";
        $stmt = $mySqliConnection->prepare($sql);
        $stmt->bind_param('i', $pUserId);
        $stmt->execute();
        $results = $stmt->get_result();
        if ($results->num_rows > 0){
            while ($row = $results->fetch_assoc()) {
                $product = new Product();
                $product->productId = $row['product_id'];
                $product->userId = $row['user_id'];
                $product->title = $row['title'];
                $product->description = $row['description'];
                $product->price = $row['price'];
                $products[] = $product;
            }
            return $products;
        }
        return null;
    }
    public static function getLastProductCreatedByUser($pUserId): ?Product
    {
        $mySqliConnection = openDatabaseConnection();
        $sql = "SELECT * FROM product WHERE user_id = ? ORDER BY product_id DESC LIMIT 1";
        $stmt = $mySqliConnection->prepare($sql);
        $stmt->bind_param('i', $pUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0){
            $result = $result->fetch_assoc();
            $product = new Product();
            $product->productId = $result['product_id'];
            $product->userId = $pUserId;
            $product->title = $result['title'];
            $product->description = $result['description'];
            $product->price = $result['price'];
            return $product;
        }
        return null;
    }
    public static function createProduct($pUserId, $pTitle, $pDescription, $pPrice): void {
        $mySqliConnection = openDatabaseConnection();
        $sql = "INSERT INTO product (user_id, title, description, price) VALUES (?, ?, ?, ?)";
        $stmt = $mySqliConnection->prepare($sql);
        $stmt->bind_param('issd', $pUserId, $pTitle, $pDescription, $pPrice);
        $stmt->execute();
        $stmt->close();
        $mySqliConnection->close();
    }

    public static function updateProduct($pUserId, $pTitle, $pDescription, $pPrice, $pProductId) {
        $mySqliConnection = openDatabaseConnection();
        $sql = "UPDATE product SET user_id = ?, title = ?, description = ?, price = ? WHERE product_id = ?";
        $stmt = $mySqliConnection->prepare($sql);
        $stmt->bind_param('issdi', $pUserId, $pTitle, $pDescription, $pPrice, $pProductId);
        $stmt->execute();
        $stmt->close();
        $mySqliConnection->close();
    }

    public static function deleteProduct($pProductId) {
        $mySqliConnection = openDatabaseConnection();
        $sql = "DELETE FROM product WHERE product_id = ?";
        $stmt = $mySqliConnection->prepare($sql);
        $stmt->bind_param('i', $pProductId);
        $stmt->execute();
        $stmt->close();
        $mySqliConnection->close();
    }

    public static function getUserName(){
        $conn = openDatabaseConnection();
        $sql = "Select user.first_name from product join user on product.user_id";
    }


}