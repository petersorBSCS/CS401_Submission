<?php
session_start();
if(!isset($_SESSION["validLogin"]) || ($_SESSION["usertype"]=="grower")){
    // FIXME -- Show a different warning page if they are a grower
    // Direct them to sign in as a shopper
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

    $_SESSION["this_page"] = 'viewCart.php';

    // Check to see if this page is already in the nav array
    $pageInNav = false;

    foreach($_SESSION["path"] as $page){
        if (in_array("viewCart.php",$page)) {
            $pageInNav = true;
        }
    }

    if (!$pageInNav) {
        $lastPage = array_pop($_SESSION["path"]);
        if ($lastPage[0] != "viewCart.php") {
            array_push($_SESSION["path"], $lastPage);
        }

        array_push($_SESSION["path"], array("viewCart.php", $_SESSION["username"] . '\'s Cart'));
    }


    include("navigation.php");

    // Show the shopping homepage
    echo "<div id=\"shoppingCart\">";
    include("cartItems.php");
    echo "<div>";

    ?>


    <div class="userFormContainer">
        <div class="userForm" id="shopperCompleteProfilePrompt" hidden="true">
            <span>
                <h2>Please complete your profile.</h2>
                <form id="shopperCompleteProfile">
                    <table>
                        <tr>
                            <td>
                                <label for="firstname">First Name</label>
                            </td>
                            <td>
                                <input type="text" name="firstname" id="firstname"
                                       placeholder="e.g. 'Billy-Bob'" required="required"
                                       pattern="[a-zA-Z\-]"
                                >
                            </td>
                            <td>
                                <label for="lastname">Last Name</label>
                            </td>
                            <td>
                                <input type="text" name="lastname" id="lastname"
                                       placeholder="e.g. 'Smith'" required="required"
                                       pattern="[a-zA-Z\-]+"
                                >
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <label for="address">Address</label>
                            </td>
                            <td colspan="3">
                                <input type="text" name="address" id="address"
                                       placeholder="e.g. '12345' Sesame St."
                                       required="required" pattern="[0-9]+"
                                       size="63"
                                >
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="zip">Zip</label>
                            </td>
                            <td>
                                <input type="text" name="zip" id="zip"
                                       placeholder="e.g. '99999' "
                                       required="required" pattern="[0-0]{5}"
                                >
                            </td>
                            <td>
                                <label for="county">County</label>
                            </td>
                            <td>
                                <input type="text" name="county" id="county"
                                       placeholder="e.g. 'Skokie' "
                                       required="required" pattern="[a-zA-Z]+"
                                >
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="city">City</label>
                            </td>
                            <td>
                                <input type="text" name="city" id="city"
                                       placeholder="e.g. 'Whoville' "
                                       required="required" pattern="[a-zA-Z]+"
                                >
                            </td>
                            <td></td>
                            <td>
                            <label for="state">State</label>
                            <select name="state" id="state">
                                <?php
                                    $states = file_get_contents("states.csv");
                                    $states = explode(",",$states);
                                    array_reverse($states);
                                    array_pop($states);
                                    array_pop($states);
                                    sort($states);
                                    $idx=0;
                                    foreach ($states as $state){

                                            $state = preg_split('/\r\n|\r|\n/', $state);
                                            echo "<option value=\"" . $state[0] . "\">" . $state[0] . "</option>";

                                        $idx++;
                                    }
                                ?>
                            </select>
                            </td>
                        </tr>
                    </table>
                    <div class="userFormButtons">
                        <button type="button" class="generalButton" id="submitFinishShopperRegistration">Submit</button>
                        <button type="button" class="generalButton" id="cancelFinishShopperRegistration">Cancel</button>
                    </div>
                </form>
            </span>
        </div>
    </div>

    <!--<button type="button" id="userLogoutButton"></button> -->
    <footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>
    </body>
    </html>

<?php } ?>