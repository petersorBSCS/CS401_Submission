<?php

require_once ("../php/Dao.php");
$dao = new Dao();

// Generate some products

$itemImgPath = "..\img\sample";

$itemImages = scandir($itemImgPath);

// Remove (. and ..)
$itemImages = array_reverse($itemImages);
array_pop($itemImages);
array_pop($itemImages);

$idx = 0;
$tmp = array();
foreach($itemImages as $itemImage) {
    if (($itemImage != "spongebob.png") && ($itemImage != "benStein.jpg"))  {
        array_push($tmp, $itemImage);
    }
}

$itemImages = $tmp;

// Build up the item names array
$itemNames = array();
foreach($itemImages as $itemName){
    $name = $param['name'] = explode("_", $itemName)[0];
    if (!in_array($name,$itemNames)){
        array_push($itemNames, $name);
    }
}

//echo print_r($itemNames);
//exit;

$imgSize = sizeof($itemImages);

$idx = 0;

$items = array();
$growerID = array("JimBob", "CousinEarl", "BettyLou");

$users = $dao->getGrowers();
//echo print_r($users);

$stages = array("planned", "planted", "sprouting", "fruiting", "harvested");

$cats = array("vegetable","fruit", "legume","root");

$units = array("lb","unit","kg","gram","oz","gal","yard","metric ton");

//exit;
//$users = array($users[0]);
$idx = 0;
foreach($users as $user) {
//    echo $user["id"].": ".$user["username"].": ".explode(".",$itemImages[$idx%$imgSize])[0]."\n";

    // Give each user 1-5 products
    $numProd = rand(1, 10);

    for ($prodIdx=0;$prodIdx<$numProd;$prodIdx++) {

        // Fetch the product name
        $name = $itemNames[rand(0,sizeof($itemNames)-1)];

        $param = array();

        $param['has_image'] = (($idx%4)===0) ? "false" : "true";
        $param['unit_type'] = $units[rand(0,count($cats)-1)];

        $param['category'] = $cats[rand(0,count($cats)-1)];
        $param['stage'] = $stages[rand(0,count($stages)-1)];
        $param['harvest_date'] = date("Y-m-d", rand(1577900000, 1546400000));
        $param['name'] = $name;//explode(".", $itemImages[$idx % $imgSize])[0];
        $param['shelf_life'] = rand(5, 21);
        $param['yield'] = rand(1 * 100, 5 * 100) / 100;
        $param['cost'] = rand(1 * 100, 5 * 100) / 100;
        $param['price'] = rand(1 * 100, 10 * 100) / 100;
        $param['growerID'] = $user["id"];

        if ($param["unit_type"]=="unit") {$param["unit_type"]=$param["name"];}

        $prodID = $dao->addProduct($param);

        //echo print_r($prodID)."\n";

        if ($param["has_image"]=="true") {
            $numImg = rand(1, 4);

            // Fetch an array of images for this product
            $itemImgList = array();
            foreach($itemImages as $thisImg){
                if(explode("_",$thisImg)[0]==$name){
                    array_push($itemImgList,$thisImg);
                }
            }

            shuffle($itemImgList);

            for ($imgIdx = 0; $imgIdx < $numImg; $imgIdx++) {
                $img_param = array();
                $img_param['product_id'] = $prodID[0]["@@IDENTITY"];
                $img_param['url'] = "../sample/".$itemImgList[$imgIdx];//$itemImages[($idx + $imgIdx) % $imgSize];
                $dao->addProductImage($img_param);
            }
        } else {
            $img_param = array();
            $img_param['product_id'] = $prodID[0]["@@IDENTITY"];
            $img_param['url'] = "../sample/spongebob.png";
            $dao->addProductImage($img_param);
        }
    }
    //echo $prodID[0]["@@IDENTITY"].": ".$itemImages[$idx%$imgSize]."\n";
/*
    $queryString =
        "INSERT INTO product 
                (has_image,unit_type, category, name, stage, harvest_date, shelf_life, yield, cost, price, growerID) 
                VALUES 
                (".$param["has_image"].", ".$param["unit_type"].", ".$param["category"].", ".$param["name"].", ".$param["stage"].", ".$param["harvest_date"].", ".$param["shelf_life"].", ".$param["yield"].", ".$param["cost"].", ".$param["price"].", ".$param["growerID"].")";

    echo $queryString,"\n";
*/
    $idx++;
}

/*

        $query->bindParam(":has_image", $params["has_image"]);
        $query->bindParam(":unit_type", $params["unit_type"]);
        $query->bindParam(":category", $params["category"]);
        $query->bindParam(":name", $params["stage"]);
        $query->bindParam(":harvest_date", $params["harvest_date"]);
        $query->bindParam(":shelf_life", $params["shelf_life"]);
        $query->bindParam(":yield", $params["yield"]);
        $query->bindParam(":cost", $params["cost"]);
        $query->bindParam(":price", $params["price"]);
        $query->bindParam(":growerID", $params["growerID"]);

 */

//echo print_r($users);


/*
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
); */


/*
foreach($itemImages as $itemImage){
    $item = array($itemImgPath."/".$itemImage, date("Y-m-d",rand(1262055681,1262155681)), $growerID[$idx%3]);
    array_push($items, $item);
    $idx++;
}

// Loop through the resultset
echo "<div id='productResultsSpan'>";
foreach($items as $item) {
    $html_out = "<div class=\"product\">";
    $html_out .= "<img src=\"".$item[0]."\"/>";
    $html_out .= "<p>".$item[1]."</p>";
    $html_out .= "<p>".$item[2]."</p>";
    $html_out .= "<a href=\"#\">order item</a>";
    $html_out .= "</div>";
    echo $html_out;
}
echo "</div>";
*/



/*
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
); */