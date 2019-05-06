<?php

// Generate the shopping cart items for this user
error_reporting(E_ERROR | E_PARSE);
session_start();

$userID = $_SESSION["userID"];
$userName = $_SESSION["username"];
require_once ("php/Dao.php");

$dao = new Dao();

$resp = $dao->fetchShopperOrders($userID);

?>

<div>
    <h1><?php echo $userName."'s Orders"; ?></h1>
    <hr>

    <?php
    /*
        echo "<pre>";
        echo print_r($resp);
        echo "</pre>";
    */
    ?>
</div>

<div id="orderItems">

    <!-- Get the orders collection -->
    <?php
    $items = $resp["results"];

    $orderTotal = 0;

    $itemizedItems = array();

    $itemIdx = 0;

    $orderID=null;

    if(sizeof($resp)>0) {

        foreach ($resp as $order_item) {

            // Check to see if this is a new order
            if($order_item["orderID"]!=$orderID){
                $orderID = $order_item["orderID"];

                if($itemIdx!=0) {
                    echo "<h1>Order Total: $" . number_format($orderTotal, 2) . "</h1>";
                    echo "</div>";
                    echo "<hr>";
                }

                echo "<div class='orderLineItem'>";
                echo "<h1>Order #".$orderID." [".$order_item["orderStatus"]."]</h1>";
                $orderTotal=0;
            }

            //echo "<div class='orderLineItem'>";
            echo "<span class='orderLineItem'>";
            echo "<span><img src=\"img/items/" . $order_item["url"] . "\"/></span>";
            echo "<h2>" . $order_item["name"] . "</h2>";
            echo "<span>";
            echo "<div>";
            echo "<table>";
            echo "<tr>";
            echo "<td><strong>Grower: </strong></td>";
            echo "<a><a href=\"#\" id=\"growerID_".$order_item["growerID"]."\"/>".$order_item["growerName"]."</a></td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>Harvest Date: </strong></td>";
            echo "<td>" . $order_item["harvest_date"] . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>Ship Date: </strong></td>";
            echo "<td>" . $order_item["delivery_date"] . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>Price: </strong></td>";
            echo "<td>$" . number_format($order_item["price"], 2) . "/ " . $order_item["unit_type"] . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>QTY: </strong></td>";
            echo "<td>" . $order_item["qty"] . " " . $order_item["unit_type"];
            if ($order_item["qty"] > 1) {
                echo "s";
            };
            echo "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>Plant Stage: </strong></td>";
            echo "<td>".$order_item["growthStage"]."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>Status: </strong></td>";
            echo "<td>".$order_item["order_item_status"]."</td>";
            echo "</tr>";
            echo "</table>";
            echo "</div>";
            echo "</span>";
            echo "</span>";
            //echo "</div>";
            $orderTotal += $order_item["qty"] * $order_item["price"];
            $itemIdx++;
        }
        echo "<h1>Order Total: $".number_format($orderTotal,2)."</h1>";
    }
    ?>
</div>



<?php exit; ?>


<div id="orderSummary">
    <div>
        <h1>Order Summary</h1>
    </div>
    <div>
        <table style="width:100%">
            <?php
            $idx=1;
            $underline="";
            foreach($itemizedItems as $itemizedItem){

                if ($idx==sizeof($itemizedItems)) {$underline="underline";}
                echo "<tr id=\"".$underline."\"><td><Strong>".$itemizedItem["name"];
                echo "</Strong></td><td>$".number_format($itemizedItem["line"],2)."</td></tr>";
                $idx++;
            }
            ?>
            <tr><td><Strong>Order Total:</Strong></td><td>$<?php echo number_format($orderTotal,2); ?></td></tr>
        </table>
    </div>
    <div>
        <?php
        if($resp["numItems"]>0) {
            echo "<button class=\"generalButton\" type = \"button\" id=\"submitOrderButton\"> Submit Order </button>";
            echo "<button class=\"generalButton\" type = \"button\" id=\"cancelOrderButton\"> Cancel Order </button >";
        }
        ?>
    </div>

    <input type="hidden" name="orderID" value="<?php echo $orderID ?>">
</div>

<!-- Prompt to make sure the user wants to remove the order item from their order -->
<div class="userFormContainer">
    <div class="userForm"  id = "confirmRemoveOrderItem" hidden="true">
            <span>
                <h2> Confirm item removal</h2>
                <p id="confirmItemRemovalSummary"></p> <!-- Order specifics goes here -->
                <div class="userFormButtons">
                    <button type="button" class="generalButton" id="confirmRemoveOrderItemButton">Confirm</button>
                    <button type="button" class="generalButton" id="cancelRemoveOrderItemButton">Cancel</button>
                </div>
            </span>
    </div>
</div>

<!-- Edit the order item in this modal -->
<div class="userFormContainer">
    <div class="userForm"  id = "editOrderItem" hidden="true">
        <span id="editOrderItemContainer">
                <h2> Edit order item</h2>
                <form id='editOrderForm' name='editOrderForm' action='editOrderItem.php' method='post'>
                    <div>
                        <span>
                            <label for="quantity"></label>
                            <input type="text" name="quantity" id="editOrderQuantity" size="4">
                        </span>
                        <span>
                            <label for="shipDate">Ship Date</label>
                            <input type="date" name="shipDate" form="editOrderForm">
                        </span>
                    </div>
                    <div class="userFormButtons">
                        <button type="button" class="generalButton" id="submitEditOrderItemButton">Submit</button>
                        <button type="button" class="generalButton" id="cancelEditOrderItemButton">Cancel</button>
                    </div>
                    <input type='hidden' name='order_itemID'>
                </form>

        </span>
    </div>
</div>

<!-- Prompt to make sure the user wants to remove the order item from their order -->
<div class="userFormContainer">
    <div class="userForm"  id = "confirmCancelOrder" hidden="true">
            <span>
                <h2>Are you sure you want to cancel your order?</h2>
                <div class="userFormButtons">
                    <button type="button" class="generalButton" id="confirmCancelOrderButton">Yes</button>
                    <button type="button" class="generalButton" id="cancelCancelOrderButton">No</button>
                </div>
            </span>
    </div>
</div>

<!-- Notify the user the order has been cancelled -->
<div class="userFormContainer">
    <div class="userForm"  id = "orderCancelled" hidden="true">
            <span>
                <h2>Your order has been cancelled</h2>
                <form method="get" action="userHome.php">
                    <div class="userFormButtons">
                        <button type="submit" class="generalButton" >OK</button>
                    </div>
                </form>
            </span>
    </div>
</div>

<!-- Notify the user the order has been placed -->
<div class="userFormContainer">
    <div class="userForm"  id = "orderSubmitted" hidden="true">
            <span>
                <h2>Your order has been submitted</h2>
                <form method="get" action="userHome.php">
                    <div class="userFormButtons">
                        <button type="submit" class="generalButton" >OK</button>
                    </div>
                </form>
            </span>
    </div>
</div>



