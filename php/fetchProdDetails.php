<?php
require_once ("Dao.php");

session_start();
$dao = new Dao();


// Grab the product details
$prodID = $_POST["prodID"];

$resultSet = $dao->getProductDetails($prodID);

// Fetch the valus from the resultSet
$name = htmlentities($resultSet["prodDetails"][0]["name"]);
$unit_type = htmlentities($resultSet["prodDetails"][0]["unit_type"]);
$category = htmlentities($resultSet["prodDetails"][0]["category"]);
$stage = htmlentities($resultSet["prodDetails"][0]["stage"]);
$harvest_date = htmlentities($resultSet["prodDetails"][0]["harvest_date"]);
$shelf_life = htmlentities($resultSet["prodDetails"][0]["shelf_life"]);
$yield = htmlentities($resultSet["prodDetails"][0]["yield"]);
$cost = htmlentities(round($resultSet["prodDetails"][0]["cost"],2));
$price = htmlentities(round($resultSet["prodDetails"][0]["price"],2));
$growerID = htmlentities($resultSet["prodDetails"][0]["growerID"]);
$growerName = htmlentities($resultSet["prodDetails"][0]["growerName"]);
$zip = htmlentities($resultSet["prodDetails"][0]["zip"]);

// Use this in our session
$_SESSION["unit_type"] = $unit_type;

// Grab all the image url's
$prodImages = array();
foreach($resultSet["prodImages"] as $url) {
    array_push($prodImages, htmlentities($url["url"]));
}
$numProdImgs = sizeof($prodImages);

$html_out = "<button class='X' id='closeProdDetails' type='button'></button>"; //<img class="displayImg X" src='../img/icons/red-x.png' >
$html_out .= "<h1>".$name."</h1>";

$html_out .= "<div id='imgContainer'>";
if($numProdImgs==0){
    $html_out .= "<img class=\"displayImg\"  src=\"..\img\sample\VeggieCharacters.jpg\" alt=\"".$name."\" id=\"prodImg_0\">";
} else {
    $html_out .= "<img class=\"displayImg\"  src=\"..\img\items\\".array_pop($prodImages)."\" alt=\"".$name."\" id=\"prodImg_0\">";
}
// Add the rest of the images (if any left)
$prodImgIdx = 1;
foreach($prodImages as $prodImage) {
    $html_out .= "<img src=\"..\img\items\\".array_pop($prodImages)."\" alt=\"".$name."\" id=\"prodImg_".$prodImgIdx."\" >";
    $prodImgIdx++;
}

$html_out .= "</div>";

if ($numProdImgs>1){
    $html_out .= "<button class=\"generalButton\" type=\"button\" id=\"prevImg\" title=\"previous image\"> < </button>";
    $html_out .= "<button class=\"generalButton\" type=\"button\" id=\"nextImg\" title=\"next image\"> > </button>";
}


$_SESSION["productGrowerID"] = $growerID;

// Put product details in a table
$html_out .= "<div><table>";
$html_out .= "<th>Product Details: </th>";
$html_out .= "<tr>";
$html_out .= "<td><strong>Grower: </strong></td>";
$html_out .= "<td><a href='viewGrower.php?growerName=".$growerName."'</a>".$growerName."</td>";
$html_out .= "<td><strong>Category: </strong></td>";
$html_out .= "<td>".$category."</td>";
$html_out .= "</tr>";
$html_out .= "<tr>";
$html_out .= "<td><strong>Growth Stage: </strong></td>";
$html_out .= "<td>".$stage."</td>";
$html_out .= "<td><strong>Harvest Date: </strong></td>";
$html_out .= "<td>".$harvest_date."</td>";
$html_out .= "</tr>";
$html_out .= "<tr>";
$html_out .= "<td><strong>Shelf Life: </strong></td>";
$html_out .= "<td>".$shelf_life." days</td>";
$html_out .= "<td><strong>Price: </strong></td>";
$html_out .= "<td>$".$price."/".$unit_type."</td>";
$html_out .= "<tr>";
$html_out .= "<td><strong>Expected Yield: </strong></td>";
$html_out .= "<td>".$yield." ".$unit_type."s</td>";
$html_out .= "</tr>";
$html_out .= "</table></div>";

$html_out .= "<div id=\"placeOrderFormContainer\">";
$html_out .= "<fieldset about=\"Place your order\" id=\"placeOrderFieldSet\" hidden=\"true\"><legend>Order Details</legend>";
$html_out .= "<form id='placeOrderForm' name='placeOrderForm' action='addOrderItem.php' method='post'>";
$html_out .= "<div>";
$html_out .= "<span>";
$html_out .= "<label for=\"quantity\">Quantity [ ".$unit_type."(s) ]</label>";
$html_out .= "<input type=\"text\" name=\"quantity\" id=\"orderQuantity\" size=\"4\">";
$html_out .= "</span>";
$html_out .= "<span>";
$html_out .= "<label for=\"shipDate\">Ship Date</label>";
$html_out .= "<input type=\"date\" name=\"shipDate\" form=\"placeOrderForm\">";
$html_out .= "</span>";
$html_out .= "</div>";
// Hidden input fields
$html_out .= "<input type='hidden' name='name' value='".$name."'>";
$html_out .= "<input type='hidden' name='prodID' value='".$prodID."'>";
$html_out .= "<input type='hidden' name='unitPrice' value='".$price."'>";
$html_out .= "<input type='hidden' name='unit_type' value='".$unit_type."'>";
$html_out .= "<input type='hidden' name='grower_name' value='".$growerName."'>";
$html_out .= "<input type='hidden' name='status' value='ordered'>";

$html_out .= "</form>";
$html_out .= "</fieldset>";
$html_out .= "</div>";

// Buttons for ordering or visiting the Grower's page
$html_out .= "<button class=\"generalButton\" type=\"button\" id=\"placeOrder\">Place Order</button>";

echo $html_out;
/*
 echo "<pre>";
 echo print_r($resultSet);
 echo "</pre>";
*/
//echo $resp;

exit;