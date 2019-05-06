<?php
if(/*!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]=="grower")*/false){
    // FIXME -- Show a different warning page if they are a grower
    // Direct them to sign in as a shopper
    include("invalidSessionWarning.php");
} else {

    // Get the order ID from the POST request

    session_start();

    $order_itemID = $_POST["order_itemID"];
    $qty = $_POST["quantity"];
    $ship_date = $_POST["shipDate"];

    require_once ("Dao.php");

    $dao = new Dao();

    $dao->updateOrderItem($order_itemID, $qty, $ship_date);



}