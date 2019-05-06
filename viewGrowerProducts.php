<?php
session_start();
if(!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]=="grower")){
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

    $thisPage = 'viewGrowerProducts.php?growerName='.$_SESSION["viewGrowerName"];

    $growerName = $_SESSION["viewGrowerName"];

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

        if ($pagePrefix[0]=="viewGrowerProducts.php"){
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

        array_push($_SESSION["path"], array($thisPage, $_SESSION["viewGrowerName"] . '\'s Products'));
    }

    include("navigation.php");

    echo "<div id='growerInventory'>";

    include("php/generateGrowerInventory.php");

    echo "</div>";
    ?>

    <!-- Select which session type -->
    <div class="userFormContainer">
        <div class="userForm"  id = "addToCart" hidden="true">
            <span>
                <h3>  </h3>

                <form>

                    <label for="qty"></label> <!-- Fill this with the units in js -->
                    <input type="number" min="0" name="qty">

                    <label for="date">Ship Date</label>
                    <input type="date" name="date">

                </form>

                <div class="userFormButtons">
                    <button type="button" class="generalButton" id="submitAddToCart">Submit</button>
                    <button type="button" class="generalButton" id="cancelAddToCart">Cancel</button>
                </div>
            </span>
        </div>
    </div>


    <!--<button type="button" id="userLogoutButton"></button> -->
    <footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>
    </body>
    </html>

<?php }?>


