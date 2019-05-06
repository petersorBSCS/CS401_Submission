<?php
session_start();
if(!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]!="grower")){
    include("invalidSessionWarning.php");
} else {

    ?>

    <!doctype html>
    <html>
    <head>
        <script src="scripts/users.js" type="text/javascript"></script>
        <title><?php echo $_SESSION["username"] ?>'s Homepage</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="styles.css" rel="stylesheet" type="text/css">
        <link rel="icon" type="image/png" href="icons/leaf.png">
        <!-- Load JQuery and the forms javascript -->
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>-->
        <script src="scripts/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="scripts/jstz.js" type="text/javascript"></script>
        <script src="scripts/forms.js" type="text/javascript"></script>
        <script src="scripts/ui.js" type="text/javascript"></script>
    </head>
    <?php

    require_once('Banner.php');
    require_once ('php/Dao.php');
    $dao = new Dao();
    $banner = new \Banner\Banner();
    $banner->print_banner(false, $dao);

    $_SESSION["this_page"] = 'manageOrders.php';

    // Check to see if this page is already in the nav array
    $pageInNav = false;

    foreach($_SESSION["path"] as $page){
        if (in_array("manageOrders.php",$page)) {
            $pageInNav = true;
        }
    }

    // Only add to the path array if this page isn't already on it
    if (!$pageInNav) {
        $lastPage = array_pop($_SESSION["path"]);
        if ($lastPage[0] != "manageOrders.php") {
            array_push($_SESSION["path"], $lastPage);
        }
        array_push($_SESSION["path"], array("manageOrders.php", $_SESSION["username"]."'s Orders"));

    }
    $_SESSION["this_page"] = 'manageOrders.php';

    include("navigation.php");

    echo "<div id='growerOrders'>";

    include("php/generateGrowerOrders.php");
    echo generateGrowerOrders();
    echo "</div>";
    ?>

    <!-- Edit products in this modal -->
    <div class="userFormContainer">
        <div class="userForm"  id = "productDetails" hidden="true">
            <span >


            </span>
        </div>
    </div>

    <!-- Prompt to make sure the user wants to remove the order item from their order -->
    <div class="userFormContainer">
        <div class="userForm"  id = "confirmRemoveProduct" hidden="true">
            <span>
                <h2></h2>
                <div class="userFormButtons">
                    <button type="button" class="generalButton" id="confirmRemoveProductButton">Yes</button>
                    <button type="button" class="generalButton" id="cancelRemoveProductButton">No</button>
                </div>
            </span>
        </div>
    </div>

    <!--<button type="button" id="userLogoutButton"></button> -->
    <footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>
    </body>
    </html>

<?php }?>


