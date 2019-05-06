<?php
if(/*!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]=="grower")*/false){
    // FIXME -- Show a different warning page if they are a grower
    // Direct them to sign in as a shopper
include("invalidSessionWarning.php");
} else {

    // Get the order ID from the POST request

    session_start();

    $order_itemID = $_POST["order_itemID"];

    require_once ("Dao.php");

    $dao = new Dao();

    $dao->removeOrderItem($order_itemID);

    $numItemsInCart = $_SESSION["numCartItems"] - 1;
    $_SESSION["numCartItems"] = $numItemsInCart;

    echo $_SESSION["numCartItems"];

}