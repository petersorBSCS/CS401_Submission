<?php

// Generate the shopping cart items for this user
error_reporting(E_ERROR | E_PARSE);
session_start();

$userID = $_SESSION["userID"];
$orderID = $_SESSION["orderID"];
require_once ("php/Dao.php");

if(!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]!="shopper")){
    include("invalidSessionWarning.php");
} else {

$dao = new Dao();

$resp = $dao->fetchOrder($orderID);

?>

<div>
    <h1>Shopping Cart</h1>
</div>

<div id="cartItems">
    <h2><?php echo $resp["numItems"]; ?> Item<?php if($resp["numItems"]>1 || $resp["numItems"]==0) { echo "s";}?></h2>
    <hr>
    <!-- Get the items collection -->
    <?php
        $items = $resp["results"];
        $orderTotal = 0;
        $itemizedItems = array();
        $itemIdx = 0;
        foreach($items as $item){
            echo "<div>";
            echo "<h1>".$item["name"]."</h1>";
            if($item["url"]==null){
                echo "<span><img src=\"img/sample/VeggieCharacters.jpg\"></span>";
            } else {
                echo "<span><img src=\"img/items/" . $item["url"] . "\"/></span>";
            }
            echo "<span>";
            echo "<table>";
            echo "<tr>";
            echo "<td><strong>Harvest Date: </strong></td>";
            echo "<td>".$item["harvest_date"]."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>Ship Date: </strong></td>";
            echo "<td>".$item["delivery_date"]."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>Price: </strong></td>";
            echo "<td>$".number_format($item["price"],2)."/ ".$item["unit_type"]."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><strong>QTY: </strong></td>";
            echo "<td>".$item["qty"]." ".$item["unit_type"];
            if ($item["qty"]>1) { echo "s";};
            echo "</td>";
            echo "</table>";
            echo "<button class=\"generalButton editOrderItem\" type=\"button\" id=\"editOrderItem_".$item["orderItemID"]."\">Edit</button>";
            echo "<button class=\"generalButton removeOrderItem\" type=\"button\" id=\"removeOrderItem_".$item["orderItemID"]."\">Remove</button>";
            echo "</span>";
            echo "<hr>";
            echo "</div>";
            $orderTotal += $item["qty"]*$item["price"];
            $itemizedItems[$itemIdx]["name"] = $item["name"];
            $itemizedItems[$itemIdx]["line"] = $item["qty"]*$item["price"];
            $itemIdx++;
        }

    ?>

</div>

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
<?php } ?>