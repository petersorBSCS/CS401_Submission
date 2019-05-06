<?php
if(/*!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]=="grower")*/false){
    // FIXME -- Show a different warning page if they are a grower
    // Direct them to sign in as a shopper
    include("invalidSessionWarning.php");
} else {

    // Get the order ID from the POST request

    $orderID = $_POST["orderID"];

    require_once ("Dao.php");

    $dao = new Dao();

    session_start();

    $_SESSION["numCartItems"] = 0;

    $_SESSION["orderID"] = null;

    $dao->orderCheckout($orderID);

}