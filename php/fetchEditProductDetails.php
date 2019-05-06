<?php
require_once ("Dao.php");

session_start();
$dao = new Dao();


// Grab the product details
$prodID = isset($_POST["prodID"]) ? $_POST["prodID"] : null ;

$addProduct = false;

$resultSet = null;

if ($prodID==null){
    $addProduct = true;
} else {
    $resultSet = $dao->getProductDetails($prodID);
}

$html_out = "";
// Fetch the values from the resultSet
$name = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["name"]) : "";
$unit_type = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["unit_type"]) : "";
$category = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["category"]) : "";
$stage = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["stage"]) : "";
$harvest_date = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["harvest_date"]) : "";
$shelf_life = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["shelf_life"]) : "";
$yield = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["yield"]) : "";
$cost = (!$addProduct) ? htmlentities(round($resultSet["prodDetails"][0]["cost"],2)) : "";
$price = (!$addProduct) ? htmlentities(round($resultSet["prodDetails"][0]["price"],2)) : "";
$growerID = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["growerID"]) : "";
$growerName = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["growerName"]) : "";
$zip = (!$addProduct) ? htmlentities($resultSet["prodDetails"][0]["zip"]) : "";

// Use this in our session
$_SESSION["unit_type"] = $unit_type;

// Grab all the image url's
$prodImages = array();
if(!$addProduct){
    foreach($resultSet["prodImages"] as $url) {
        array_push($prodImages, htmlentities($url["url"]));
    }
    // Put the default image in here if there aren't any in the DB
    if(sizeof($resultSet["prodImages"])==0){
        array_push($prodImages, "../sample/VeggieCharacters.jpg");
    }
}
$numProdImgs = sizeof($prodImages);

$html_out = "<button class='X' id='closeProdDetails' type='button'></button>"; //<img class="displayImg X" src='../img/icons/red-x.png' >
if(!$addProduct){
    $html_out .= "<h1>".$name."</h1>";
} else {
    $html_out .= "<h1>Add Product</h1>";
}

//$html_out .= "<div id='imgContainer'>";

include("generateProductPics.php");
$html_out .= generatePics($prodID);

/*

if(sizeof($resultSet["prodImages"])==0){
    $html_out .= "<h2>(Default Image)</h2>";
}
$html_out .= "<img class=\"displayImg\"  src=\"..\img\items\\".array_pop($prodImages)."\" alt=\"".$name."\" id=\"prodImg_0\">";

// Add the rest of the images (if any left)
$prodImgIdx = 1;
foreach($prodImages as $prodImage) {
    $html_out .= "<img src=\"..\img\items\\".array_pop($prodImages)."\" alt=\"".$name."\" id=\"prodImg_".$prodImgIdx."\" >";
    $prodImgIdx++;
}
*/
//$html_out .= "</div>";

$_SESSION["productGrowerID"] = $growerID;

// Put product details in a table
$html_out .= "<div><form id='editProductForm' action='#'><table id='editProductTable'>";
$html_out .= "<th>Product Details: </th>";
$html_out .= "<tr>";
$html_out .= "<td><label for='name'><strong>Name: </strong></label></td>";
$html_out .= "<td><input type='text' name='name' value='".$name."'/></td>";
$html_out .= "<td><label for='category'><strong>Category: </strong></label></td>";
$html_out .= "<td>";
$html_out .= "<select name=\"category\" id=\"category\">";

$cats = array("vegetable","fruit", "legume","root","nut","grain");

        foreach ($cats as $cat) {
                $html_out .= "<option value=\"".$cat."\"";
                if($cat==$category) {
                    $html_out .= " selected";
                }
                $html_out .= ">".$cat."</option>";
        }

$html_out .="</select></td>";



$html_out .= "<td><label for='unit_type'><strong>Unit Type: </strong></label></td>";
$html_out .= "<td>";
$html_out .= "<select name=\"unit_type\" id=\"unit_type\">";

$units = array("lb","unit","kg","gram","oz","gal","yard","metric ton");

foreach ($units as $unit) {
    $html_out .= "<option value=\"".$unit."\"";
    if($unit==$unit_type) {
        $html_out .= " selected";
    }
    $html_out .= ">".$unit."</option>";
}

$html_out .="</select></td>";


$html_out .= "</tr>";
$html_out .= "<tr>";

$html_out .= "<td><label for='stage'><strong>Growth Stage: </strong></label></td>";
$html_out .= "<td>";
$html_out .= "<select name=\"stage\" id=\"stage\">";

$stages = array("planned", "planted", "sprouting", "fruiting", "harvested", "infected", "spoiling");

foreach ($stages as $stageItem) {
    $html_out .= "<option value=\"".$stageItem."\"";
    if($stage==$stageItem) {
        $html_out .= " selected";
    }
    $html_out .= ">".$stageItem."</option>";
}

$html_out .="</select></td>";

$html_out .= "<td><strong><label for=\"date\">Harvest Date</label></strong></td>";
$html_out .= "<td><input type=\"date\" name=\"date\" value='".$harvest_date."'></td>";

$html_out .= "<td><label for = 'shelf_life'><strong>Shelf Life: </strong></label></td>";
$html_out .= "<td><input type='number' maxlength='3' size='3' min='0' max='365' name='shelf_life' value='".$shelf_life."'/> days</td>";
$html_out .= "</tr>";
$html_out .= "<tr>";
$html_out .= "<td><label for='cost'><strong>Cost($): </strong></label></td>";
$html_out .= "<td><input type='text' maxlength='6' size='6' name='cost' value='".$cost."'></td>";
$html_out .= "<td><label for<strong>Price($): </strong></td>";
$html_out .= "<td><input type='text' maxlength='6' size='6' name='price' value='".$price."'/></td>";
$html_out .= "<tr>";
$html_out .= "<td><label for='yield'><strong>Expected Yield: </strong></label></td>";
$html_out .= "<td><input type='text' maxlength='3' size='3' name='yield' value='".$yield."'/></td>";
$html_out .= "</tr>";
if(!$addProduct) {
    $html_out .= "<input type='hidden' name='prodID' value='" . $prodID . "'>";
} else {
    $html_out .= "<input type='hidden' name='growerID' value='" . $_SESSION["userID"]. "'>";
}
$html_out .= "</table></form></div>";

// Buttons for ordering or visiting the Grower's page
$html_out .= "<button class=\"generalButton\" type=\"button\" id='editProductSubmitButton'>Submit</button>";

echo $html_out;
/*
 echo "<pre>";
 echo print_r($resultSet);
 echo "</pre>";
*/
//echo $resp;

exit;