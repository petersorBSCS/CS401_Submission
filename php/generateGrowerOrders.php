<?php

// Generate the shopping cart items for this user
error_reporting(E_ERROR | E_PARSE);
session_start();

if(isset($_POST)){
    require_once ("Dao.php");
} else {
    require_once ("php/Dao.php");
}

// AJAX version
if(isset($_POST["regenerate"]) && $_POST["regenerate"]=="true"){
    generateGrowerOrders();
}

function generateGrowerOrders() {


    $userID = $_SESSION["userID"];
    $userName = $_SESSION["username"];

    $dao = new Dao();

    $resp = $dao->fetchGrowerOrders($userID);


    $html_out = "";

    $html_out .= "<div>";
    $html_out .= "<h1>".$userName."'s Orders</h1>";
    $html_out .= "<hr>";


/*
    echo "<pre>";
    echo print_r($resp);
    echo "</pre>";
    echo $html_out;
    return;
*/
    $html_out .= "</div>";

    $html_out .= "<div id=\"orderItems\">";

        $items = $resp["orders"];

        $numOrder = $resp["numOrders"];

        $orderTotal = 0;

        $itemizedItems = array();

        $itemIdx = 0;

        $orderID=null;

        if(sizeof($resp)>0) {

            foreach ($items as $order_item) {

                // Check to see if this is a new order
                if($order_item["order_id"]!=$orderID){
                    $orderID = $order_item["order_id"];

                    if($itemIdx!=0) {
                        $html_out .=  "<h1>Total Revenue: $".number_format($orderTotal,2);
                        $html_out .= "  ($".number_format(abs($orderTotal-$costTotal),2);
                        if(($orderTotal-$costTotal)>=0) {
                            $html_out .= " profit)</h1>";
                        } else {
                            $html_out .= " loss)</h1>";
                        }
                        $html_out .= "</div>";
                        $html_out .= "<hr>";
                    }

                    $custName = $order_item["firstname"]." ".$order_item["lastname"];
                    $custAddr1 = $order_item["address"];
                    $custAddr2 = $order_item["city"].", ".$order_item["state"].", ".$order_item["zip"];

                    $html_out .= "<div class='orderLineItem'>";
                    $html_out .= "<h1>Order #".$orderID." [".$order_item["order_status"]."]";
                    $html_out .= "<h2>Ship to:</h2>";
                    $html_out .= "<h3><p>".$custName."</p>";
                    $html_out .= "<p>".$custAddr1."</p>";
                    $html_out .= "<p>".$custAddr2."</p></h3>";
                    $orderTotal=0;
                    $costTotal=0;
                }

                //echo "<div class='orderLineItem'>";
                $html_out .= "<span class='orderLineItem'>";
                if($order_item["url"]==null){
                    $html_out .= "<span><img src=\"img/sample/VeggieCharacters.jpg\"/></span>";
                } else {
                    $html_out .= "<span><img src=\"img/items/" . $order_item["url"] . "\"/></span>";
                }
                $html_out .= "<h2>" . $order_item["prodName"] . "</h2>";
                $html_out .= "<span>";
                $html_out .= "<div>";
                $html_out .= "<table>";
                $html_out .= "<tr>";
                $html_out .= "<td><strong>Harvest Date: </strong></td>";
                $html_out .= "<td>".$order_item["harvest_date"]."</td>";
                $html_out .= "</tr>";
                $html_out .= "<tr>";
                $html_out .= "<td><strong>Ship Date: </strong></td>";
                $html_out .= "<td>".$order_item["delivery_date"]."</td>";
                $html_out .= "</tr>";
                $html_out .= "<tr>";
                $html_out .= "<td><strong>Stage: </strong></td>";
                $html_out .= "<td>" . $order_item["stage"] . "</td>";
                $html_out .= "</tr>";
                $html_out .= "<tr>";
                $html_out .= "<td><strong>Price: </strong></td>";
                $html_out .= "<td>$" . number_format($order_item["revenue"], 2) . "/ " . $order_item["unit_type"] . "</td>";
                $html_out .= "</tr>";
                $html_out .= "<tr>";
                $html_out .= "<td><strong>Cost: </strong></td>";
                $html_out .= "<td>$" . number_format($order_item["cost"], 2) . "/ " . $order_item["unit_type"] . "</td>";
                $html_out .= "</tr>";
                $html_out .= "<tr>";
                $html_out .= "<td><strong>QTY: </strong></td>";
                $html_out .= "<td>" . $order_item["qty"] . " " . $order_item["unit_type"];
                if ($order_item["qty"] > 1) {
                    $html_out .= "s";
                };
                $html_out .= "</td>";
                $html_out .= "</tr>";
                $html_out .= "<tr>";
                $html_out .= "<td><strong>Status: </strong></td>";
                $html_out .= "<td>".$order_item["item_status"]."</td>";
                $html_out .= "</tr>";
                $html_out .= "</table>";
                $html_out .= "<span class='changeOrderItemStatus'>";
                $html_out .= "<button type='button' class='generalButton changeOrderItemStatusButton' id='".$order_item["orderItemID"]."'>Update Status</button>";
                $html_out .= "<select class='changeOrderItemStatusSelect'>";
                $html_out .= "<option value='null'>--Select--</option>";
                $html_out .= "<option value='ordered'>Ordered</option>";
                $html_out .= "<option value='processing'>Processing</option>";
                $html_out .= "<option value='shipped'>Shipped</option>";
                $html_out .= "<option value='canceled'>Canceled</option>";
                $html_out .= "</select>";
                $html_out .= "</h1></span>";
                $html_out .= "</div>";
                $html_out .= "</span>";
                $html_out .= "</span>";
                //echo "</div>";
                if($order_item["item_status"]!="canceled"){
                    $orderTotal += $order_item["qty"] * $order_item["revenue"];
                    $costTotal += $order_item["qty"] * $order_item["cost"];
                }
                $itemIdx++;
            }
            $html_out .=  "<h1>Total Revenue: $".number_format($orderTotal,2);
            $html_out .= "  ($".number_format(abs($orderTotal-$costTotal),2);
            if(($orderTotal-$costTotal)>=0) {
                $html_out .= " profit)</h1>";
            } else {
                $html_out .= " loss)</h1>";
            }
        }

    $html_out .= "</div>";

    echo $html_out;
}

