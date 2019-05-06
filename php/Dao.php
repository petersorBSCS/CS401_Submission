<?php

class Dao
{
    // Heroku deployment values:
    private $host   = "us-cdbr-iron-east-01.cleardb.net";
    private $db     = "heroku_19da46878ce8676";
    private $user   = "b4e5a57fb76a7e";
    private $pass   = "d621799b";

    private $force_clearDB = true;  // For populating the Heroku database (testing)


    public function getConnection(){
        $_SESSION["salt"] = hash("sha256", "salty");

        if (!$this->force_clearDB){ $this->checklocal(); }

        return new PDO("mysql:host={$this->host};dbname={$this->db}",$this->user, $this->pass);
    }

    private function checklocal(){
        $dir = __DIR__;

        if ($dir == "C:\Users\Rob\Documents\infinite-woodland-34036\php"){
            $this->host   = "localhost";
            $this->db     = "neighborsgarden";
            $this->user   = "root";
            $this->pass   = "";
        }
    }

    // AJAX Handlers
    public function checkUniqueEmail($email){

        $conn = $this->getConnection();

        $loginQuery = "SELECT COUNT(*) as C from user where email = :email";
        $query = $conn->prepare($loginQuery);

        $query->bindParam(":email", $email);

        $query->execute();

        $count = $query->fetchAll();


        return $count;
    }

    public function checkUniqueUsername($username){

        $conn = $this->getConnection();

        $loginQuery = "SELECT COUNT(*) as C from user where username = :username";
        $query = $conn->prepare($loginQuery);

        $query->bindParam(":username", $username);

        $query->execute();

        $count = $query->fetchAll();

        return $count;
    }
    // End AJAX Handlers

    public function addDescr($type=null,$id=null, $url=null){

        if ($type==null || $id==null || $url==null){
            return null;
        }

        $conn = $this->getConnection();

        $queryString = "";

        if ($type=="grower") {
            $queryString = "INSERT INTO user_descr (userID,url) VALUES (:id,:url)";
        } else if($type=="product")  {
            $queryString = "INSERT INTO product_descr (productID,url) VALUES (:id,:url)";
        }

        echo "\nQS: ".$queryString."\n";

        $query = $conn->prepare($queryString);

        $query->bindParam(":id", $id);
        $query->bindParam(":url", $url);

        return $query->execute();

    }

    public function checkExistingDescr($type=null,$id=null){

        if ($type==null || $id==null){
            return null;
        }

        $conn = $this->getConnection();

        $queryString = "";

        if ($type=="grower") {
            $queryString = "SELECT COUNT(id) as count , url as url from user_descr where userID=:id";
        } else if($type=="product")  {
            $queryString = "SELECT COUNT(id) as count , url as url from product_descr where productID=:id";
        } else {
            // Shoppers don't get a description
            return null;
        }

        $query = $conn->prepare($queryString);

        $query->bindParam(":id", $id);

        $query->execute();
        $result = $query->fetch();

        if ($result["count"]==0){
            return null;
        } else {
            return $result["url"];
        }

    }

    // Fetch the order details for a particular item
    public function updateOrderItem($orderItemID, $qty, $ship_date){

        $conn = $this->getConnection();

        $queryString = "UPDATE order_item set qty=:qty, delivery_date=:ship_date 
                  WHERE order_item.id=:orderItemID";

        $query = $conn->prepare($queryString);

        $query->bindParam(":orderItemID", $orderItemID);
        $query->bindParam(":qty", $qty);
        $query->bindParam(":ship_date", $ship_date);

        return $query->execute(); // Return the status

    }

    // Fetch the order details for a particular item
    public function fetchOrderItem($orderItemID){

        $conn = $this->getConnection();

        $queryString = "SELECT 
                        product.name AS prodName,
                        product.unit_type AS unit_type,
                        order_item.qty AS qty,
                        order_item.delivery_date AS ship_date
	              FROM 
	                   product 
	                     JOIN order_item 
	                       ON product.id=order_item.productID 
                  WHERE order_item.id=:orderItemID";

        $query = $conn->prepare($queryString);

        $query->bindParam(":orderItemID", $orderItemID);

        $query->execute();

        $resultSet = $query->fetchAll();

        return $resultSet[0];

    }

    // Return a list of all the known product names
    public function getProducts(){
        $resultSet = $this->getConnection()->query("SELECT DISTINCT name from product ORDER BY name");
        $resultSet = $resultSet->fetchAll();
        return $resultSet;
    }

    // Return a list of all the known product names
    public function getProductPics($prodID){

        if($prodID==null){
            return;
        }

        $conn = $this->getConnection();

        $queryString = "SELECT 
                        product_image.url AS url,
                        product_image.id AS id       
                          FROM 
                               product_image  
                          WHERE product_image.productID=:id";

        $query = $conn->prepare($queryString);

        $query->bindParam(":id", $prodID);

        $query->execute();

        $resultSet = $query->fetchAll();

        return $resultSet;
    }

    // Get the info needed to generate product Thumbnails
    public function getProductThumbs($filters=null)
    {
        $conn = $this->getConnection();

/*
        echo "<pre>";
        echo "Filters: </br>";
        echo print_r($filters);
        echo "</pre>";
*/

        // Array for building up the filter elements as we go
        $filtersArray = array();


        if (isset($filters["names"])) {

            // Unroll the names array
            $namesFilter = "";
            $namesIdx = 0;
            foreach ($filters["names"] as $name) {
                if ($namesIdx != 0) {
                    $namesFilter .= " OR ";
                }
                $namesFilter .= "product.name=:prod_" . $name;
                $namesIdx++;
            }
            array_push($filtersArray, $namesFilter);

        }

        if (isset($filters["zips"])) {

            // Unroll the names array
            $zipsFilter = "";
            $zipsIdx = 0;
            foreach ($filters["zips"] as $zip) {
                if ($zipsIdx != 0) {
                    $zipsFilter .= " OR ";
                }
                $zipsFilter .= "user.zip=:zip_" . $zip;
                $zipsIdx++;
            }
            array_push($filtersArray, $zipsFilter);
        }

        if (isset($filters["beforeAfter"]) && isset($filters["date"])) {

            $dateFilter = " product.harvest_date ";

            if ($filters["beforeAfter"] == "past") {
                $dateFilter .= "< ";
            } else {
                $dateFilter .= "> ";
            }

            $dateFilter .= ":date";
            array_push($filtersArray, $dateFilter);
        }



        if(isset($filters["grower"])){
            $growerFilter = " user.username=:growerName";
            array_push($filtersArray,$growerFilter);
        }


        // Form the where clause
        $whereIdx=0;
        foreach($filtersArray as $filter){
            if ($whereIdx==0){
                $whereClause = "WHERE ";
            }

            if ($whereIdx!=0) {
                $whereClause .= " AND ";
            }
            $whereClause .= $filter;
            $whereIdx++;
        }

        $queryString="";
        if ($filters==null) {
            $queryString = "SELECT 
                        product.name AS prodName,
                        product.id AS prodID,
                        product.growerID AS growerID,
                        product_image.url AS prodURL, 
                        product.harvest_date AS prodHarvest, 
                        user.username as growerName,
                        user.zip AS zip
	              FROM 
	                   product_image 
	                     JOIN product 
	                       ON product_image.productID=product.id 
	                     JOIN user 
	                       ON user.id=product.growerID
                    ORDER BY prodID";
        } else {
            $queryString = "SELECT 
                        product.name AS prodName,
                        product.id AS prodID,
                        product.growerID AS growerID,
                        product_image.url AS prodURL, 
                        product.harvest_date AS prodHarvest, 
                        user.username as growerName,
                        user.zip as zip 
	              FROM 
	                   product_image 
	                     RIGHT OUTER JOIN product 
	                       ON product_image.productID=product.id 
	                     JOIN user 
	                       ON user.id=product.growerID ".$whereClause."
                    ORDER BY prodID";

//            return $queryString;

        }




//        return $query;

        // Fetch the product list from DB
        $resultSet = array();
        if($filters==null){
            $resultSet = $conn->query($queryString);
            $resultSet = $resultSet->fetchAll();
        } else {
            // Need to prepare and bind ...
            $query = $conn->prepare($queryString);

            // Loop through all the product names and bind those parameters
            $logString = "";
            if(isset($filters["names"])) {
                $namesArr = array();
                foreach ($filters["names"] as $name) {
                    array_push($namesArr, $name);
                    $query->bindParam(":prod_".$name, $namesArr[sizeof($namesArr) - 1]);
                    $logString.= "Binding ".":prod_".$name." to ".$namesArr[sizeof($namesArr) - 1]."</br>";
                }
            }
            //return $queryString."</br>".$logString;


            if (isset($filters["zips"])) {
                // This needs to be outside of the for loop scope
                $zipsArr = array();
                foreach ($filters["zips"] as $zip) {
                    array_push($zipsArr, $zip);
                    $query->bindParam(":zip_" . $zip, $zipsArr[sizeof($zipsArr) - 1]);
                }
            }

            // This needs to be outside of the for loop scope
            if (isset($filters["grower"])) {
                $query->bindParam(":growerName",$filters["grower"]);
            }

            if (isset($filters["beforeAfter"]) && isset($filters["date"])) {
                $query->bindParam(":date",$filters["date"]);
            }


            $query->execute();

            $resultSet = $query->fetchAll();
        }

        return $resultSet;
    }

    public function getProductDetails($prodID) {


        // Create the DB connection
        $conn = $this->getConnection();

        // Fetch the product details
        $queryString = "SELECT 
                        product.*,
                        user.username as growerName,
                        user.zip as zip
	              FROM 
	                   product 
	                     JOIN user 
	                       ON product.growerID=user.id
                  WHERE product.id = :prodID";

        $query = $conn->prepare($queryString);

        $query->bindParam(":prodID", $prodID);

        $query->execute();

        $prodDetails = $query->fetchAll();

        // Array for holding the product info
        $resultSet["prodDetails"] = $prodDetails;

        // Fetch the image url(s)
        $queryString = "SELECT 
                        product_image.url as url
	              FROM 
                        product_image 
                  WHERE product_image.productID = :prodID";

        $query = $conn->prepare($queryString);

        $query->bindParam(":prodID", $prodID);

        $query->execute();

        $prodImages = $query->fetchAll();

        // Array for holding the product info
        $resultSet["prodImages"] = $prodImages;

        return $resultSet;
    }

    public function loginUser($email, $password, $usertype=null){
        $conn = $this->getConnection();

        $passwordHashed = hash("sha256", $password.$_SESSION["salt"]);

        $usertypeLogin = "";
        if($usertype!=null){
            $usertypeLogin = " and usertype=:usertype";
        }

        $loginQuery = "SELECT * from user where email = :email and password = :password".$usertypeLogin;

        // Aggregate for the user login (=1 is good)
        $validQueryString = "SELECT COUNT(id) as num from user where email = :email and password = :password".$usertypeLogin;
        $query = $conn->prepare($validQueryString);
        if($usertype!=null){
            $query->bindParam(":usertype", $usertype);
        }
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $passwordHashed);
        $query->execute();
        $result = $query->fetchAll();

        if ($result[0]["num"]==0){
            // Bad Login Credentials
            $result[0]["validLogin"] = "false";
        } elseif ($result[0]["num"]==2){
            // Just return
        } else {
            $query = $conn->prepare($loginQuery);
            if($usertype!=null){
                $query->bindParam(":usertype", $usertype);
            }
            $query->bindParam(":email", $email);
            $query->bindParam(":password", $passwordHashed);
            $query->execute();
            $result = $query->fetchAll();
            $result[0]["validLogin"] = "true";
            $result[0]["num"] = 1;
        }

        return reset($result);
    }


    // Wipe this order from the database
    public function removeOrderItem($order_itemID){
        $conn = $this->getConnection();

        $queryString = "DELETE FROM order_item WHERE id=:order_itemID";

        $query = $conn->prepare($queryString);
        $query->bindParam(":order_itemID", $order_itemID);

        $query->execute();

    }

    // Wipe this order from the database
    public function removeOrder($orderID){
        $conn = $this->getConnection();

        $queryString = "DELETE FROM orders WHERE orders.id=:orderID";

        $query = $conn->prepare($queryString);
        $query->bindParam(":orderID", $orderID);

        $query->execute();

        $queryString = "DELETE FROM order_item WHERE order_item.orderID=:orderID";

        $query = $conn->prepare($queryString);
        $query->bindParam(":orderID", $orderID);

        $query->execute();
    }

    public function orderCheckout($orderID){
        // Change the status on the order to "ordered"
        $conn = $this->getConnection();

        $queryString = "UPDATE orders SET status=:status WHERE orders.id=:orderID";

        $query = $conn->prepare($queryString);

        $status = "ordered";

        $query->bindParam(":orderID", $orderID);
        $query->bindParam(":status", $status);

        $query->execute();

    }

    public function updateProduct($params){

        // Check if anything is null
        foreach($params as $param) {
            if ($param==null) {return;}
        }

        $conn = $this->getConnection();

        $queryString = "UPDATE product 
                          SET 
                              name=:name,
                              unit_type=:unit_type,                                           
                              category=:category,                              
                              stage=:stage,
                              harvest_date=:harvest_date,                              
                              shelf_life=:shelf_life,                              
                              yield=:yield,                              
                              cost=:cost,                              
                              price=:price                              
                          WHERE id=:id";

        $query = $conn->prepare($queryString);
        $query->bindParam(":name", $params["name"]);
        $query->bindParam(":unit_type", $params["unit_type"]);
        $query->bindParam(":category", $params["category"]);
        $query->bindParam(":stage", $params["stage"]);
        $query->bindParam(":harvest_date", $params["harvest_date"]);
        $query->bindParam(":shelf_life", $params["shelf_life"]);
        $query->bindParam(":yield", $params["yield"]);
        $query->bindParam(":cost", $params["cost"]);
        $query->bindParam(":price", $params["price"]);
        $query->bindParam(":id", $params["prodID"]);

        return $query->execute();

    }

    public function removeProduct($prodID){

        if ($prodID==null){
            return;
        }

        $conn = $this->getConnection();

        // Fetch the existing product images
        $queryString = "SELECT 
                            product_image.url AS url  
                            FROM 
                                product_image
                                WHERE product_image.productID=:id";

        $query = $conn->prepare($queryString);
        $query->bindParam(":id", $prodID);
        $query->execute();
        $images = $query->fetchAll();

        // Delete these images from the server
        $prodImgDir = "../img/items/";

        foreach($images as $img){
            unlink($prodImgDir.$img["url"]);
        }

        // Delete the images from the database
        $queryString = "DELETE 
                            FROM 
                                product_image
                                WHERE product_image.productID=:id";

        $query = $conn->prepare($queryString);
        $query->bindParam(":id", $prodID);
        $query->execute();

        // Delete the corresponding order items
        $queryString = "DELETE 
                            FROM 
                                order_item
                                WHERE order_item.productID=:id";

        $query = $conn->prepare($queryString);
        $query->bindParam(":id", $prodID);
        $query->execute();

        // Delete the corresponding product
        $queryString = "DELETE 
                            FROM 
                                product
                                WHERE product.id=:id";

        $query = $conn->prepare($queryString);
        $query->bindParam(":id", $prodID);
        $query->execute();

    }

    public function fetchGrowerProducts($uid){
        // Fetch all products from a given grower

        $conn = $this->getConnection();

        // Fetch the existing items list
        $queryString = "SELECT
                            product_image.url AS url,  
                            product.*
                            FROM 
                                product LEFT OUTER JOIN product_image ON product.id=product_image.productID
                                WHERE product.growerID=:uid
                                GROUP BY product.id";

        $query = $conn->prepare($queryString);
        $query->bindParam(":uid", $uid);
        $query->execute();
        //$resultSet = array();
        $resultSet["products"] = $query->fetchAll();
/*
        if($resultSet["products"]["numItems"]==0){
            // The join in the previous query didn't show any products
            // because there were no images

            $queryString = "SELECT
                            product.*
                            FROM 
                                product
                                WHERE product.growerID=:uid";

            $query = $conn->prepare($queryString);
            $query->bindParam(":uid", $uid);
            $query->execute();
            $resultSet["products"] = $query->fetchAll();


        }
*/

        $queryString = "SELECT
                            COUNT(product.id) AS numItems
                            FROM 
                                product 
                                WHERE product.growerID=:uid";

        $query = $conn->prepare($queryString);
        $query->bindParam(":uid", $uid);
        $query->execute();
        $result = $query->fetch();
        $resultSet["numItems"] = $result[0];

        return $resultSet;
    }

    public function fetchOrder($orderID){
        // Fetch the details of the order to display to the shopper's cart

        $conn = $this->getConnection();

        // Fetch the existing items list
        $queryString = "SELECT 
                            product_image.url AS url,  
                            product.name AS name, 
                            product.price AS price,
                            product.category AS category,
                            product.unit_type AS unit_type,
                            product.harvest_date AS harvest_date,
                            product.growerID AS growerID,
                            product.id AS prodID,
                            order_item.orderID AS orderID,
                            order_item.qty AS qty,
                            order_item.delivery_date AS delivery_date,
                            order_item.id AS orderItemID
                            FROM 
                                order_item JOIN product ON product.id=order_item.productID 
                                LEFT JOIN product_image ON product.id=product_image.productID
                                JOIN orders ON orders.id=order_item.orderID
                                WHERE orderID=:orderID and orders.status=:status
                                GROUP BY order_item.id;";

        $query = $conn->prepare($queryString);

        $status = "pending";

        $query->bindParam(":status", $status);
        $query->bindParam(":orderID", $orderID);

        $query->execute();

        $resultSet = $query->fetchAll();

        $orderDetails = array();
        $orderDetails["results"] = $resultSet;

        $queryString = "SELECT COUNT(ID) AS numItems FROM (SELECT 
                            order_item.id AS ID
                            FROM 
                                order_item JOIN product ON product.id=order_item.productID 
                                LEFT JOIN product_image ON product.id=product_image.productID
                                JOIN orders ON orders.id=order_item.orderID
                                WHERE orderID=:orderID and orders.status=:status
                                GROUP BY order_item.id) AS Q;";

        $query = $conn->prepare($queryString);

        $status = "pending";

        $query->bindParam(":status", $status);
        $query->bindParam(":orderID", $orderID);

        $query->execute();

        $resultSet = $query->fetchAll();

        $orderDetails["numItems"] = $resultSet[0]["numItems"];

        return $orderDetails;
    }

    public function fetchShopperOrders($userID){

        $conn = $this->getConnection();

        $queryString = "SELECT 
                            product_image.url as url,  
                            product.name as name, 
                            product.price as price,
                            product.unit_type as unit_type,
                            product.harvest_date as harvest_date,
                            product.growerID as growerID,
                            product.stage as growthStage,
                            user.username as growerName,
                            product.id as prodID,
                            order_item.orderID as orderID,
                            order_item.qty as qty,
                            order_item.delivery_date as delivery_date,
                            order_item.id as orderItemID,
                            order_item.status as order_item_status,
                            orders.custID as custID,
                            orders.order_date as orderDate,
                            orders.status AS orderStatus
                            FROM 
                                order_item join product on product.id=order_item.productID 
                                join product_image on product.id=product_image.productID
                                join orders on order_item.orderID=orders.id
                                join user on product.growerID=user.id
                                WHERE orders.custID=:custID and orders.status=:status 
                                GROUP BY order_item.id
                                ORDER BY orders.order_date ASC";

        $query = $conn->prepare($queryString);

        $status = "ordered";

        $query->bindParam(":custID", $userID);
        $query->bindParam(":status", $status);

        $query->execute();

        $resultSet = $query->fetchAll();

        return $resultSet;

    }

    public function fetchPendingOrder($id){
        // Fetch the pending order for the shopper (for a persistent shopping cart)
        $conn = $this->getConnection();

        $queryString = "SELECT 
                            orders.id AS orderID
                            FROM orders
                            WHERE custID=:id AND status=:status";

        $query = $conn->prepare($queryString);

        $status = "pending";

        $query->bindParam(":id", $id);
        $query->bindParam(":status", $status);

        $query->execute();

        $resultSet = $query->fetchAll();

        return $resultSet[0]["orderID"];
    }

    public function fetchNumOrderItems($orderID){

        $conn = $this->getConnection();

        $queryString = "SELECT COUNT(ID) AS numItems FROM (SELECT 
                        order_item.id AS ID
                        FROM 
                            order_item JOIN product ON product.id=order_item.productID 
                            LEFT JOIN product_image ON product.id=product_image.productID
                            JOIN orders ON orders.id=order_item.orderID
                            WHERE orderID=:orderID and orders.status=:status
                            GROUP BY order_item.id) AS Q;";

        $query = $conn->prepare($queryString);

        $status = "pending";

        $query->bindParam(":orderID", $orderID);
        $query->bindParam(":status", $status);

        $query->execute();

        $resultSet = $query->fetchAll();

        $orderDetails["numItems"] = $resultSet[0]["numItems"];

        return $orderDetails;
    }


    public function updateOrderItems($orderID, $itemName){
        $conn = $this->getConnection();

        // Fetch the existing items list
        $queryString = "SELECT items FROM orders WHERE id=:orderID";

        $query = $conn->prepare($queryString);
        $query->bindParam(":orderID", $orderID);

        $query->execute();

        $resultSet = $query->fetchAll();

        $items = $resultSet[0]["items"].", ".$itemName;

        // Update the order Items
        $queryString = "UPDATE orders SET items=:items WHERE id=:orderID";

        $query = $conn->prepare($queryString);
        $query->bindParam(":orderID", $orderID);
        $query->bindParam(":items", $items);

        $rtn = $query->execute();

        return $rtn;
    }

    public function createOrder($param){
        // Generate a new order row
        $conn = $this->getConnection();

        $queryString = "INSERT INTO orders (custID, items, order_date, status) 
                        VALUES (:custID, :items, :order_date, :status)";

        $query = $conn->prepare($queryString);

        $status = "pending";

        $query->bindParam(":status", $status);
        $query->bindParam(":custID", $param["custID"]);
        $query->bindParam(":items", $param["items"]);
        $query->bindParam(":order_date", $param["order_date"]);

        $rtn = array();
        $rtn["status"] = $query->execute();

        // Get the last ID inserted into the table from this connection
        $id = $conn->query("SELECT @@IDENTITY");
        $rtn["orderID"] = $id->fetchAll();

        return $rtn;
    }

    public function addOrderItem($param){

        $conn = $this->getConnection();

        $queryString = "INSERT INTO order_item (orderID, qty, delivery_date, unitPrice, productID, status) 
                        VALUES (:orderID, :qty, :delivery_date, :unitPrice, :productID, :status)";

        $query = $conn->prepare($queryString);

        $query->bindParam(":orderID", $param["orderID"]);
        $query->bindParam(":qty", $param["quantity"]);
        $query->bindParam(":delivery_date",$param["shipDate"] );
        $query->bindParam(":productID",$param["prodID"] );
        $query->bindParam(":unitPrice",$param["price"] );
        $query->bindParam(":status",$param["status"] );

        $rtn = $query->execute();

        // Update the itemsList in the order with this addition
        $queryString = "UPDATE orders (items) 
                        VALUES (:items)";

        $query = $conn->prepare($queryString);

        $query->bindParam(":items", $param["name"]);
        $query->bindParam(":orderID", $param["orderID"]);

        return $rtn;
    }

    public function addConfirm($username, $email){
        $key = $username . $email . date('mY');
        $key = hash("sha256",$key);

        // Fetch the userID for the new user
        $userID = $this->getConnection()->query("SELECT id from user where email = " . $email);

        $conn = $this->getConnection();

        $queryString = "INSERT INTO user_confirm (userID, confirmKey, email) VALUES (:userID, :confirmKey, :email)";

        $query = $conn->prepare($queryString);

        $query->bindParam(":userID", $userID[0]["id"]);
        $query->bindParam(":confirmKey", $key);
        $query->bindParam(":email", $email);

        $query->execute();

        // Shove the email and key into an array so it can be drafted into a confirmation email
        $rtn["email"] = $email;
        $rtn["key"] = $key;
        return $rtn;
    }


    public function getUsers(){
        return $this->getConnection()->query("SELECT * FROM user")->fetchAll();
    }

    public function getGrowers(){
        return $this->getConnection()->query("SELECT * FROM user WHERE usertype='grower'")->fetchAll();
    }

    public function getShoppers(){
        return $this->getConnection()->query("SELECT * FROM user WHERE usertype='shopper'")->fetchAll();
    }


    public function deleteProductPic($id) {

        // Get the connection
        $conn = $this->getConnection();

        // Fetch the url where the image is stored on the server
        $queryString = "SELECT url AS url from product_image 
                          WHERE id=:id";

        $query = $conn->prepare($queryString);

        $query->bindParam(":id", $id);

        $query->execute();

        $resp = $query->fetchAll();

        $queryString = "DELETE from product_image 
                          WHERE id=:id";

        $query = $conn->prepare($queryString);

        $query->bindParam(":id", $id);

        $query->execute();

        return $resp[0]["url"];
    }

    public function deleteUserPic($id) {

        // Get the connection
        $conn = $this->getConnection();

        // Fetch the url where the image is stored on the server
        $queryString = "SELECT url AS url from user_image 
                          WHERE id=:id";

        $query = $conn->prepare($queryString);

        $query->bindParam(":id", $id);

        $query->execute();

        $resp = $query->fetchAll();

        $queryString = "DELETE from user_image 
                          WHERE id=:id";

        $query = $conn->prepare($queryString);

        $query->bindParam(":id", $id);

        $query->execute();

        return $resp[0]["url"];
    }

    public function fetchUserPics($uid){

        // Get the connection
        $conn = $this->getConnection();

        $queryString = "SELECT 
                            COUNT(id) AS numImg
                          FROM user_image 
                          WHERE userID=:uid";

        $query = $conn->prepare($queryString);
        $query->bindParam(":uid", $uid);
        $query->execute();
        $result=$query->fetchAll();

        $resp["numImg"] = $result[0]["numImg"];


        $queryString = "SELECT 
                            url AS url,
                            id AS id
                          FROM user_image 
                          WHERE userID=:uid
                          ORDER BY id DESC";

        $query = $conn->prepare($queryString);

        $query->bindParam(":uid", $uid);

        $query->execute();

        $result = $query->fetchAll();

        if ($resp["numImg"]==0){
            return $resp;
        } else {
            $resp["userImages"] = $result;
            return $resp;
        }

    }

    public function fetchUser($params){

        // Get the connection
        $conn = $this->getConnection();

        // Get the params
        $usertype = $params["usertype"];
        $uid = $params["uid"];

        $queryString = "SELECT 
                            email AS email,
                            username AS username,
                            firstname AS firstname,                          
                            lastname AS lastname,       
                            username AS username,       
                            address AS address,
                            city AS city,
                            county AS county,
                            state AS state,
                            zip AS zip
                          FROM user 
                          WHERE id=:uid AND usertype=:usertype";

        $query = $conn->prepare($queryString);

        $query->bindParam(":uid", $uid);
        $query->bindParam(":usertype", $usertype);

        $query->execute();

        $result = $query->fetchAll();

        $result = $result[0];

        $result["about"] =  $this->checkExistingDescr($usertype,$uid);


        return $result;

    }


    public function addProduct($params){

        $conn = $this->getConnection();

        foreach($params as $param){
            if ($param==null){ return; }
        }

        $queryString =
            "INSERT INTO product 
                (unit_type, category, name, stage, harvest_date, shelf_life, yield, cost, price, growerID) 
                VALUES 
                (:unit_type, :category, :name, :stage, :harvest_date, :shelf_life, :yield, :cost, :price, :growerID)";

        $query = $conn->prepare($queryString);

        $query->bindParam(":unit_type", $params["unit_type"]);
        $query->bindParam(":category", $params["category"]);
        $query->bindParam(":name", $params["name"]);
        $query->bindParam(":stage", $params["stage"]);
        $query->bindParam(":harvest_date", $params["harvest_date"]);
        $query->bindParam(":shelf_life", $params["shelf_life"]);
        $query->bindParam(":yield", $params["yield"]);
        $query->bindParam(":cost", $params["cost"]);
        $query->bindParam(":price", $params["price"]);
        $query->bindParam(":growerID", $params["growerID"]);

        $query->execute();

        // Get the last ID inserted into the table from this connection
        $id = $conn->query("SELECT @@IDENTITY");

        return $id->fetchAll();
    }

    public function addProfileImage($uid,$url){

        $conn = $this->getConnection();

        $queryString =
            "INSERT INTO user_image 
                (userID, url) 
                VALUES 
                (:uid, :url)";

        $query = $conn->prepare($queryString);

        $query->bindParam(":uid", $uid);
        $query->bindParam(":url", $url);

        $query->execute();

    }

    public function addProductImage($params){

        $conn = $this->getConnection();

        $queryString =
            "INSERT INTO product_image 
                (productID, url) 
                VALUES 
                (:product_id, :url)";

        $query = $conn->prepare($queryString);

        $query->bindParam(":product_id", $params["product_id"]);
        $query->bindParam(":url", $params["url"]);

        $query->execute();

    }
/*
    public function getUsers($email, $username){

        $conn = $this->getConnection();
        $queryString = "SELECT * from user where email = :email";
        $query = $conn->prepare($queryString);
        $query->bindParam(":email", $email);
        $query->execute();

        $result = $query->fetchAll();

        $taken["emailTaken"] = sizeof($result)==0 ? false : true;

        $queryString = "SELECT * from user where username = :username";
        $query = $conn->prepare($queryString);
        $query->bindParam(":username", $username);
        $query->execute();

        $result = $query->fetchAll();

        $taken["usernameTaken"] = sizeof($result)==0 ? false : true;

        return $taken;
    }
*/

   // public function registerUser ($email, $password, $firstname, $lastname, $username, $usertype, $address,
     //                             $direction, $street, $street_type, $city, $county, $state, $zip)

    public function registerUser ($email, $password, $username, $usertype) {
        $conn = $this->getConnection();

        $queryString = "SELECT COUNT(id) AS num_type FROM user WHERE email=:email AND usertype=:usertype";

        $query = $conn->prepare($queryString);
        $query->bindParam(":email", $email);
        $query->bindParam(":usertype", $usertype);
        $query->execute();

        $result = $query->fetchAll();

        $resp = array();

        if($result[0]["num_type"]>0){
            if($usertype=="grower"){
                $resp["error_type"] = "already_grower";
            } else {
                $resp["error_type"] = "already_shopper";
            }

            return $resp;

        } else {
            $resp["error_type"] = "none";
        }

        $registerQuery =
            "INSERT INTO user 
                 (email, password, username, usertype)
                  VALUES
                  (:email, :password, :username, :usertype)";

        // Salt and hash the password
        $passwordHashed = hash("sha256",$password.$_SESSION["salt"]);

        // Prepare the statement
        $query = $conn->prepare($registerQuery);

        // Bind the parameters
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $passwordHashed);
        $query->bindParam(":username", $username);
        $query->bindParam(":usertype", $usertype);

        $query->execute();

        // Get the last ID inserted into the table from this connection
        $id = $conn->query("SELECT @@IDENTITY");
        $id = $id->fetch();

        $resp["uid"] = $id[0];

        //return $query->fetchAll();
        return $resp;//$id[0];
    }

    public function finishProfile($params){
        // Finish the rest of the user profile that was started during registration

        $conn = $this->getConnection();

        $registerQuery =
            "UPDATE user SET
                 firstname=:firstName,
                 lastname=:lastName,
                 address=:address,
                 city=:city,
                 county=:county,
                 state=:state,
                 zip=:zip
                 WHERE user.id=:userID";

        // Prepare the statement
        $query = $conn->prepare($registerQuery);

        // Bind the parameters
        $query->bindParam(":userID", $params["uid"]);
        $query->bindParam(":firstName", $params["firstName"]);
        $query->bindParam(":lastName", $params["lastName"]);
        $query->bindParam(":address", $params["address"]);
        $query->bindParam(":city", $params["city"]);
        $query->bindParam(":county", $params["county"]);
        $query->bindParam(":state", $params["state"]);
        $query->bindParam(":zip", $params["zip"]);

        $resp = $query->execute();

        return $resp;

    }

public function updateOrderItemStatus($id,$status){
    if($id==null || $status==null){
        return;
    }

    $conn = $this->getConnection();

    $queryString = "UPDATE order_item 
                      SET status=:status 
                      WHERE order_item.id=:id";

    $query = $conn->prepare($queryString);

    $query->bindParam(":id", $id);
    $query->bindParam(":status", $status);

    $query->execute(); // Update this order item's status

    // Fetch the order_id that this order_item belongs to
    $queryString = "SELECT orderID 
                      FROM order_item 
                      WHERE order_item.id=:id";

    $query = $conn->prepare($queryString);

    $query->bindParam(":id", $id);

    $query->execute();

    $orderID = $query->fetch();

    $orderID = $orderID["orderID"];

    // Fetch the order_id that this order_item belongs to
    $queryString = "SELECT status 
                      FROM order_item 
                      WHERE order_item.orderID=:orderID";

    $query = $conn->prepare($queryString);

    $query->bindParam(":orderID", $orderID);

    $query->execute();

    $orderItems = $query->fetchAll();

    // Change order status -> Ordered, In progress or Completed
    $numOrdered = 0;
    $numShipped = 0;
    $numCanceled = 0;
    foreach($orderItems as $orderItem){
        if($orderItem["status"]=="ordered"){
            $numOrdered++;
        }
        if($orderItem["status"]=="shipped"){
            $numShipped++;
        }
        if($orderItem["status"]=="canceled"){
            $numCanceled++;
        }
    }

    $orderStatus = "";

    if($numOrdered==sizeof($orderItems)){
        $orderStatus = "ordered";
    } else if($numShipped==sizeof($orderItems)) {
        $orderStatus = "completed";
    } else if($numCanceled==sizeof($orderItems)) {
        $orderStatus = "canceled";
    } else {
        $orderStatus = "in progress";
    }

    $queryString = "UPDATE orders 
                      SET status=:orderStatus 
                      WHERE orders.id=:orderID";

    $query = $conn->prepare($queryString);

    $query->bindParam(":orderID", $orderID);
    $query->bindParam(":orderStatus", $orderStatus);

    $query->execute(); // Update this order's status
}

public function fetchGrowerID($growerName){

    if($growerName==null){
        return;
    }

    $conn = $this->getConnection();

    $queryString = "SELECT id as id 
                      FROM user 
                      WHERE user.username=:growerName";

    $query = $conn->prepare($queryString);

    $query->bindParam(":growerName", $growerName);

    $query->execute(); // Update this order item's status

    $id = $query->fetch();

    return $id[0];

}

public function fetchGrowerOrders($id){

    // Finish the rest of the user profile that was started during registration
    $conn = $this->getConnection();

    $queryString =
        "SELECT 
            product.price AS revenue,
            product.cost  AS cost,
            product.id AS prodID,
            product.name AS prodName,
            product.harvest_date AS harvest_date,
            product.stage AS stage,
            product.unit_type AS unit_type,
            product_image.url AS url,
            order_item.id AS orderItemID,
            order_item.qty AS qty,
            order_item.delivery_date AS delivery_date,
            order_item.status AS item_status,
            orders.order_date AS order_date,
            orders.status AS order_status,
            orders.id AS order_id,
            user.firstname AS firstname,
            user.lastname AS lastname,
            user.address AS address,
            user.city AS city,
            user.state AS state,
            user.zip AS zip
            FROM 
            product JOIN order_item ON product.id=order_item.productID
            LEFT JOIN product_image ON product_image.productID=product.id
            JOIN orders ON order_item.orderID=orders.id
            JOIN user ON orders.custID=user.id
            WHERE product.growerID=:id";

    // Prepare the statement
    $query = $conn->prepare($queryString);

    // Bind the parameters
    $query->bindParam(":id", $id);

    $query->execute();
    $resp = array();
    $resp["orders"] = $query->fetchAll();
    $resp["numOrders"] = sizeof($resp["orders"]);

    return $resp;
}

/* -- DB Schema -- For Reference

CREATE TABLE IF NOT EXISTS user (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(64) NOT NULL,
    password VARCHAR(64) NOT NULL,
    firstname VARCHAR(64),
    lastname VARCHAR(64),
    username VARCHAR(64),
    usertype TINYINT,
    address INT(10) UNSIGNED,
    direction VARCHAR(1),
    street VARCHAR(20),
    street_type VARCHAR(20),
    city VARCHAR(20),
    county VARCHAR(20),
    zip INT(5)
);

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    custID INT(10) UNSIGNED NOT NULL,
    items VARCHAR(2000) NOT NULL,
    delivery_date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS order_item (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    orderID BIGINT UNSIGNED NOT NULL,
    productID BIGINT UNSIGNED NOT NULL,
    unitPrice DECIMAL(13 , 4 ),
    qty INT(10) UNSIGNED NOT NULL
);

CREATE TABLE IF NOT EXISTS product (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    has_image BOOLEAN NOT NULL,
    unit_type VARCHAR(20) NOT NULL,
    category VARCHAR(30) NOT NULL,
    name VARCHAR(30) NOT NULL,
    stage VARCHAR(30) NOT NULL,
    harvest_date DATE NOT NULL,
    shelf_life DATE,
    yield INT UNSIGNED,
    cost DECIMAL(13 , 4 ),
    price DECIMAL(13 , 4 ),
    growerID INT(10) UNSIGNED NOT NULL
);

CREATE TABLE IF NOT EXISTS product_image (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    productID BIGINT UNSIGNED NOT NULL,
    url VARCHAR(64)
);

-- Alter the tables to add foreign key constraints
alter table product
    add constraint product_FK0 foreign key ( growerID ) references user (id);

alter table product_image
    add constraint product_image_FK0 foreign key ( productID ) references product (id);

alter table orders
    add constraint order_FK1 foreign key ( custID ) references user (id);

alter table order_item
    add constraint order_item_FK0 foreign key ( orderID ) references orders (id),
	add constraint order_item_FK1 foreign key ( productID ) references product (id);






 */



}