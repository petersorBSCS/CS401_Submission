<?php
// Shopper's view of a grower's page

session_start();

require_once("php/Dao.php");

if(!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]=="grower") || (!isset($_GET["growerName"]))) {
    // FIXME -- Show a different warning page if they are a grower
    // Direct them to sign in as a shopper
    include("invalidSessionWarning.php");
} else {

    $growerName = $_GET["growerName"];

    $_SESSION["viewGrowerName"] = $growerName;

    $dao = new Dao();

    $growerID = $dao->fetchGrowerID($growerName);

    $_SESSION["viewGrowerID"] = $growerID;

    ?>

    <!doctype html>
    <html>
    <head>
        <script src="scripts/users.js" type="text/javascript"></script>
        <title>Gardener <?php echo $growerName ?>'s Profile Page</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="styles.css" rel="stylesheet" type="text/css">
        <link rel="icon" type="image/png" href="icons/leaf.png">
        <!-- Load JQuery and the forms javascript -->
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>-->
        <script src="scripts/jstz.js" type="text/javascript"></script>
        <script src="scripts/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="scripts/forms.js" type="text/javascript"></script>
        <script src="scripts/ui.js" type="text/javascript"></script>
    </head>
    <body>

    <?php

    require_once('Banner.php');
    require_once ('php/Dao.php');
    $dao = new Dao();
    $banner = new \Banner\Banner();
    $banner->print_banner(false,$dao);

    $thisPage = 'viewGrower.php?growerName='.$growerName;

    $_SESSION["this_page"] = $thisPage;

    // Check to see if this page is already in the nav array
    $pageInNav = false;

    $navIdx=0;
    $delNavElem = null;
    foreach($_SESSION["path"] as $page){
        if (in_array($thisPage,$page)) {
            $pageInNav = true;
        }

        // Remove other grower's profile page from the nav array here
        $pagePrefix = explode("?growerName=",$page[0]);

        if ($pagePrefix[0]=="viewGrower.php"){
            if($pagePrefix[1]!=$growerName){
                // Mark this element in the nav for deletion
                $delNavElem = $navIdx;
            }
        }
        $navIdx++;
    }

    if($delNavElem!=null){
        array_splice($_SESSION["path"],$delNavElem,1);
    }

    if (!$pageInNav) {
        $lastPage = array_pop($_SESSION["path"]);
        if ($lastPage[0] != $thisPage) {
            array_push($_SESSION["path"], $lastPage);
        }

        array_push($_SESSION["path"], array($thisPage, $_SESSION["viewGrowerName"] . '\'s Profile'));
    }

    include("navigation.php");

    ?>

    <div class="growerOptions">

        <span class="growerOptions"><h1>Gardener <?php echo $growerName ?>'s Profile Page</h1></span>
        <span class="growerOptions">
            <button class="generalButton" id="viewGrowerProductsButton">
                <a href="viewGrowerProducts.php?growerName=<?php echo $_SESSION["viewGrowerName"];?>">View Products</a>
            </button>
        </span>
        <hr>

    </div>
    <div id="growerProfile">
        <div id="growerImgContainer">
        <span style="width:200px; background-color: white;">
            <?php
                include("php/generateProfilePics.php");
            ?>
        </span>
        </div>
        <div id="growerInfoContainer">
        <span>
            <?php
                include("php/fetchGrowerInfo.php");
            ?>
        </span>
        </div>

    </div>



    <!--<button type="button" id="userLogoutButton"></button> -->
    <footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>
    </body>
    </html>

<?php }?>