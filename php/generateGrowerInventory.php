<?php

// Generate the shopping cart items for this user
error_reporting(E_ERROR | E_PARSE);
session_start();

if($_SESSION["usertype"]=="grower"){
    $userID = $_SESSION["userID"];
} else {
    $userID = $_SESSION["viewGrowerID"];
}


// If this is an AJAX call or the shopper's view, then the the relative path changes
if(isset($_GET)){
    require_once ("Dao.php");
} else {
    require_once ("php/Dao.php");
}

$dao = new Dao();

$resp = $dao->fetchGrowerProducts($userID);
/*
echo "<pre>";
echo print_r($resp);
echo "</pre>";
*/

$html_out = "";

    $html_out.="<div id=\"cartItems\">";
    $html_out .="<div id='growerProductInfo'>";
    $html_out.="<h2>".$resp["numItems"]." Product";
    if($resp["numItems"]>1 || $resp["numItems"]==0) { $html_out.="s";}
    $html_out.="</h2>";
    if($_SESSION["usertype"]=="grower"){
        $html_out .="<button type=\"button\" class=\"generalButton\" id=\"addProductButton\">Add Product</button>";
    }
    $html_out .="</div>";
    $html_out .= "    <hr>";

    // Get the items collection
    $itemizedItems = array();

    $itemIdx = 0;

    foreach($resp["products"] as $item) {
        $html_out .= "<div>";
        $html_out .= "<h1>(Product #" . $item["id"] . "): " . $item["name"] . "</h1>";
        $html_out .= "<span>";
        if (isset($item["url"]) == null) {
            $html_out .= "<img src=\"img/sample/VeggieCharacters.jpg\"/>";
        } else {
            $html_out .= "<img src=\"img/items/" . $item["url"] . "\"/>";
        }
        $html_out .= "</span>";
        $html_out .= "<span>";
        $html_out .= "<table";
        if($_SESSION["usertype"]!="grower"){
            $html_out .= " class=\"shopperProductViewTable\"";
        }
        $html_out .= ">";
        $html_out .= "<tr>";
        $html_out .= "<td><strong>Harvest Date: </strong></td>";
        $html_out .= "<td>" . $item["harvest_date"] . "</td>";
        $html_out .= "<td><strong>Stage: </strong></td>";
        $html_out .= "<td>" . $item["stage"] . "</td>";
        $html_out .= "</tr>";
    if ($_SESSION["usertype"] == "grower") {
        $html_out .= "<tr>";
        $html_out .= "<td><strong>Unit Type: </strong></td>";
        if ($item["unit_type"] == "unit") {
            $html_out .= "<td>" . $item["name"] . "</td>";
        } else {
            $html_out .= "<td>" . $item["unit_type"] . "</td>";
        }
        $html_out .= "<td><strong>Category: </strong></td>";
        $html_out .= "<td>" . $item["category"] . "</td>";
        $html_out .= "</tr>";
    }
        $html_out .= "<tr>";
    if ($_SESSION["usertype"] == "grower") {
        $html_out .= "<td><strong>Cost: </strong></td>";
        $html_out .= "<td>$" . number_format($item["cost"], 2) . "/ " . $item["unit_type"] . "</td>";
    } else {
        $html_out .= "<td><strong>Category: </strong></td>";
        $html_out .= "<td>" . $item["category"] . "</td>";
    }
        $html_out .= "<td><strong>Price: </strong></td>";
        $html_out .= "<td>$".number_format($item["price"],2)."/ ".$item["unit_type"]."</td>";
        $html_out .= "</tr>";
        $html_out .= "<tr>";
        $html_out .= "<td><strong>Expected Yield: </strong></td>";
        $html_out .= "<td>".$item["yield"]." ";
        if($item["unit_type"]=="unit"){
            $html_out .= $item["name"];
        } else {
            $html_out .= $item["unit_type"];
        }
        if ($item["yield"]>1 || $item["yield"]==0) { $html_out .= "s";};
        $html_out .= "</td>";
        $html_out .= "<td><strong>Shelf Life: </strong></td>";
        $html_out .= "<td>".$item["shelf_life"]." days</td>";
        $html_out .= "</tr>";
        $html_out .= "</table>";
        if($_SESSION["usertype"]=="grower"){
            $html_out .= "<button class=\"generalButton editOrderItem\" type=\"button\" id=\"editProduct_".$item["id"]."\">Edit</button>";
            $html_out .= "<button class=\"generalButton removeOrderItem\" type=\"button\" id=\"removeProduct_".$item["id"]."\">Remove</button>";
        } else {
            $html_out .= "<button class=\"generalButton addProductToCart\"";
            $html_out .= "type=\"button\" id=\"".$item["price"]."_".$item["name"]."_".$item["id"]."_".$item["unit_type"]."\">Add to Cart</button>";
        }
        $html_out .= "</span>";
        $html_out .= "<hr>";
        $html_out .= "</div>";
        $itemIdx++;
    }




    $html_out .= "</div>";

// Prompt to make sure the user wants to remove the order item from their order
$html_out .= "<div class=\"userFormContainer\">";
$html_out .= "<div class=\"userForm\"  id = \"confirmRemoveOrderItem\" hidden=\"true\">";
$html_out .= "<span>";
$html_out .= "<h2> Confirm item removal</h2>";
$html_out .= "<p id=\"confirmItemRemovalSummary\"></p>"; // Order specifics goes here
$html_out .= "<div class=\"userFormButtons\">";
$html_out .= "<button type=\"button\" class=\"generalButton\" id=\"confirmRemoveOrderItemButton\">Confirm</button>";
$html_out .= "<button type=\"button\" class=\"generalButton\" id=\"cancelRemoveOrderItemButton\">Cancel</button>";
$html_out .= "</div>";
$html_out .= "</span>";
$html_out .= "</div>";
$html_out .= "</div>";

echo $html_out;





