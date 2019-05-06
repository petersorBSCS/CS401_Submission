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

    $result = $dao->fetchOrderItem($order_itemID);

    $plural ="";
    if ($result["qty"] > 1){
        $plural = "s";
    }


    if (isset($_POST["type"]) && $_POST["type"]=="editItem"){
        $html_out = $result["qty"]." ".$result["ship_date"]." ".$result["unit_type"];
    } else {
        $html_out = "Remove " . $result["qty"] . " " . $result["unit_type"] . $plural;
        if ($result["unit_type"]!=$result["prodName"]){
            $html_out .= " of " . $result["prodName"].$plural;
        }
        $html_out.=" from your order?";
    }

    echo $html_out;

}