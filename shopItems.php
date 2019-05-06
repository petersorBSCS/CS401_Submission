<?php
//session_start();
if(!isset($_SESSION["validLogin"])){
    include("invalidSessionWarning.php");
} else {
?>

<div id="shopContainer">
    <div id="itemsListContainer">

        <!-- 1st part of the product search form here -->
        <div id="numProdInSearch"><p>I'm shopping for...</p></div>
        <div id="itemsList">
            <form action="#" form="productSearchForm">
                <?php

                    require_once ("php/Dao.php");
                    $dao = new Dao();

                    // Get the list of known products
                    $products = $dao->getProducts();
                    print "<ul>";
                    foreach($products as $product){
                        print "<li><input class=\"foodList\" name=\"".$product["name"]."\" type=\"checkbox\"><label for='foodList'>".$product["name"]."</label></li>";
                    }
                    print "</ul>";
                ?>

            </form>
        </div>
    </div>

    <div id="productsContainer">
        <div id="productSearchForm">
            <span>
                <label for="milesRadius">Within</label>
                <select name="milesRadius" form="productSearchForm">
                    <option value="any">any</option>
                    <option value="5">5</option>
                    <option value="10">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                </select>
            </span>
            <span>
                miles of
            </span>
            <span>
                <label for="zip">ZIP</label>
                <input type="text" maxlength="5" size="5" name="zip" form="productSearchForm">
            </span>
            <span>
                <ul>
                    <li><input type="radio" name="pastFutureHarvest" value="past" form="productSearchForm">Harvested Before</li>
                    <li><input type="radio" name="pastFutureHarvest" value="future" form="productSearchForm">Harvested After</li>
                </ul>
            </span>
            <span>
                <label for="date">Harvest Date</label>
                <input type="date" name="date" form="productSearchForm">
            </span>
            <span>
                <label for="grower">Grower ID</label>
                <input type="text" name="grower" form="productSearchForm">
            </span>
            <span>
                <button type="button" class="generalButton" form="productSearchForm" id="productSearchFormButton">Apply Filter</button>
                <!--<input type="hidden" value="Unfiltered">--> <!-- Change this value to "filtered" once they touch the form -->
            </span>
        </div>
        <div id="productResults">
            <?php $_SESSION["firstTimeProdLoad"]=true; include("php/generateItems.php"); ?>
        </div>
    </div>
</div>

<div class="userFormContainer">
    <div class="userForm" id="productDetails" hidden="true">
        <span>

        </span>
    </div>
</div>

<div class="userFormContainer">
    <div class="userForm" id="guestSignUpPrompt" hidden="true">
    <span>
        <button class='X' id='closeGuestSignUpPrompt' type='button'></button>
        <h1>You must be a registered user to place an order.</h1>
        <h2>Please visit the <a href="index.php">home</a> page to register.</h2>
    </span>
    </div>
</div>

<div class="userFormContainer">
    <div class="userForm" id="confirmOrder" hidden="true">
    <span>
        <h2>Please confirm your order.</h2>
        <p id="confirmOrderDetails"></p>
        <div>
            <button class='generalButton' type='button' id="confirmOrderButton">Confirm</button>
            <button class='generalButton' type='button' id="cancelOrderButton">Cancel</button>
        </div>
    </span>
    </div>
</div>

<div class="userFormContainer">
    <div class="userForm" id="itemsAddedToCart" hidden="true">
        <span>
            <h2>Item added to <a href="viewCart.php">cart</a>.</h2>
            <div>
                <button class='generalButton' type='button' id="okItemAdded">OK</button>
            </div>
        </span>
    </div>
</div>

<div class="userFormContainer">
    <div class="userForm" id="Loading" hidden="true">
    <span>
        <h2>We're Loading those Results ...</h2>
        <img src="img/sample/benStein.jpg">
    </span>
    </div>
</div>

<?php } ?>