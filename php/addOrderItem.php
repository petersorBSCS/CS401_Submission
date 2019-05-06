<?php

session_start();

require_once ("Dao.php");

$dao = new Dao();

// Fetch the POST data
$param = array();
$quantity = $_POST["quantity"]; $param["quantity"] = $quantity;
$shipDate = $_POST["shipDate"]; $param["shipDate"] = $shipDate;
$prodID = $_POST["prodID"]; $param["prodID"] = $prodID;
$price = $_POST["unitPrice"]; $param["price"] = $price;
$status = $_POST["status"]; $param["status"] = $status;
$name = $_POST["name"]; $param["name"] = $name;

// Validate

// Sanitize

// DAO

date_default_timezone_set($_SESSION["clientTimezone"]);

if(!isset($_SESSION["orderID"])){
    $date = date("Y-m-d H:i:s");
    $custID = $_SESSION["userID"];
    $param["custID"] = $custID;
    $param["items"] = $name." (".$quantity." ".$_SESSION["unit_type"]."s)";
    $param["order_date"] = $date;
    $resp = $dao->createOrder($param);
    $param["orderID"] = $resp["orderID"][0]["@@IDENTITY"];
    $_SESSION["orderID"] = $param["orderID"];
    $_SESSION["numCartItems"] = 0;
} else {
    // Fetch the orderID for this order
    $param["orderID"] = $_SESSION["orderID"];

    // Update the list of items in the order
    $dao->updateOrderItems($_SESSION["orderID"], $name." (".$quantity." ".$_SESSION["unit_type"]."s)");
}

$dao->addOrderItem($param);
$numCartItems = $_SESSION["numCartItems"]+1;
$_SESSION["numCartItems"] = $numCartItems;
echo $numCartItems;
