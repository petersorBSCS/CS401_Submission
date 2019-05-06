<?php

if(!isset($_SESSION)){
    session_start();
}

    require_once ("Dao.php");
    $dao = new Dao();

    $editProfile = false;
    if(isset($_POST["editProfile"])){
        $editProfile = ($_POST["editProfile"]=="true") ? true : false;
    }

    $params = array();
    if($_SESSION["usertype"]!="grower"){
        $editProfile = false;
        // Fetch all the info for this grower
        $params["uid"] = $_SESSION["viewGrowerID"];
        $params["usertype"] = "grower";
    } else {
        // Fetch all the info for the user
        $params["uid"] = $_SESSION["userID"];
        $params["usertype"] = $_SESSION["usertype"];
    }

    $userInfo = $dao->fetchUser($params);
    $username = (isset($userInfo["username"])) ? $userInfo["username"] : "<em>blank</em>";
    $username = (isset($userInfo["username"])) ? $userInfo["username"] : "<em>blank</em>";
    $firstname = (isset($userInfo["firstname"])) ? $userInfo["firstname"] : "<em>blank</em>";
    $firstnameInput = (isset($userInfo["firstname"])) ? $userInfo["firstname"] : "";
    $lastname = (isset($userInfo["lastname"])) ? $userInfo["lastname"] : "<em>blank</em>";
    $lastnameInput = (isset($userInfo["lastname"])) ? $userInfo["lastname"] : "";
    $address = (isset($userInfo["address"])) ? $userInfo["address"] : "<em>blank</em>";
    $addressInput = (isset($userInfo["address"])) ? $userInfo["address"] : "";
    $city = (isset($userInfo["city"])) ? $userInfo["city"] :  "<em>blank</em>";
    $cityInput = (isset($userInfo["city"])) ? $userInfo["city"] :  "";
    $county = (isset($userInfo["county"])) ? $userInfo["county"] :  "<em>blank</em>";
    $countyInput = (isset($userInfo["county"])) ? $userInfo["county"] :  "";
    $state = (isset($userInfo["state"])) ? $userInfo["state"] :  "<em>blank</em>";
    $zip = (isset($userInfo["zip"])) ? $userInfo["zip"] : "<em>blank</em>";
    $zipInput = (isset($userInfo["zip"])) ? $userInfo["zip"] : "";
    $aboutFile = (isset($userInfo["about"])) ? $userInfo["about"] : null;

?>

<?php if($_SESSION["usertype"]=="grower") { ?>
<form id="growerCompleteProfile">
<?php } else { echo "<div id=\"growerCompleteProfile\">"; }    ?>
    <div id="growerAbout">
        <?php
            $about = "";
            if($editProfile) { ?>
                <!-- Had to do this to get the DIR right, depending on the request context -->

        <input type="hidden" name="aboutDir"/>

        <label for="Description">About you: </label>

        <textarea id="growerAboutInput" rows="14" cols="79" name="growerAbout">
        </textarea>
        <?php } else {

            if ($_SESSION["usertype"]=="grower") {
                echo "<p>About you:<p>";
                echo "<p id=\"dispGrowerAbout\">".$about."</p>";
            } else {
                echo "<p>About ".$_SESSION["viewGrowerName"].":<p>";
                $about = fetchAboutGrower($aboutFile);
                echo "<p id=\"viewGrowerAbout\">".$about."</p>";
            }


        }
        ?>
    </div>
    <table>
        <tr>
            <td>
                <?php if($editProfile) { ?>
                    <label for="firstname">First Name: </label>
                <?php } else {
                        echo "First Name: ";
                    }
                ?>
            </td>

                <?php if($editProfile) { ?>
                    <td>
                        <input type="text" name="firstname" id="firstname"
                               placeholder="e.g. 'Billy-Bob'" required="required"
                               pattern="[a-zA-Z\-]"
                               value="<?php echo $firstnameInput ?>"
                        >
                    </td>
                <?php } else {
                    echo "<td class=\"dispGrowerInfo\">".$firstname."</td>";
                }
                ?>

            <td>
                <?php if($editProfile) { ?>
                    <label for="lastname">Last Name: </label>
                <?php } else {
                    echo "Last Name: ";
                }
                ?>
            </td>

                <?php if($editProfile) { ?>
                    <td>
                        <input type="text" name="lastname" id="lastname"
                               placeholder="e.g. 'Smith'" required="required"
                               pattern="[a-zA-Z\-]+"
                               value="<?php echo $lastnameInput ?>"
                        >
                    </td>
                <?php } else {
                    echo "<td class=\"dispGrowerInfo\">".$lastname."</td>";
                }
                ?>

        </tr>
        <?php if($_SESSION["usertype"]=="grower") {?>
        <tr >
            <td>
                <?php if($editProfile) { ?>
                    <label for="address">Address: </label>
                <?php } else {
                    echo "Address: ";
                }
                ?>
            </td>

                <?php if($editProfile) { ?>
                    <td colspan="3">
                        <input type="text" name="address" id="address"
                               placeholder="e.g. '12345' Sesame St."
                               required="required" pattern="[0-9]+"
                               size="62"
                               value="<?php echo $addressInput ?>"
                        >
                    </td>
                <?php } else {
                    echo "<td colspan=\"3\" class=\"dispGrowerInfo\">".$address."</td>";
                }
                ?>

        </tr>
        <?php } ?>
        <tr>
            <td>
                <?php if($editProfile) { ?>
                    <label for="zip">Zip: </label>
                <?php } else {
                    echo "Zip: ";
                }
                ?>

            </td>

                <?php if($editProfile) { ?>
                    <td>
                        <input type="text" name="zip" id="GrowerZip"
                               placeholder="e.g. '99999' "
                               required="required" pattern="[0-0]{5}"
                               value="<?php echo $zipInput ?>"
                        >
                    </td>
                <?php } else {
                    echo "<td class=\"dispGrowerInfo\">".$zip."</td>";
                }
                ?>


            <td>
                <?php if($editProfile) { ?>
                    <label for="county">County: </label>
                <?php } else {
                    echo "County: ";
                }
                ?>
            </td>

                <?php if($editProfile) { ?>
                    <td>
                        <input type="text" name="county" id="county"
                               placeholder="e.g. 'Skokie' "
                               required="required" pattern="[a-zA-Z]+"
                               value="<?php echo $countyInput ?>"
                        >
                    </td>
                <?php } else {
                    echo "<td class=\"dispGrowerInfo\">".$county."</td>";
                }
                ?>

        </tr>
        <tr>
            <td>
                <?php if($editProfile) { ?>
                    <label for="city">City: </label>
                <?php } else {
                    echo "City: ";
                }
                ?>
            </td>

                <?php if($editProfile) { ?>
                    <td>
                        <input type="text" name="city" id="GrowerCity"
                               placeholder="e.g. 'Whoville' "
                               required="required" pattern="[a-zA-Z]+"
                               value="<?php echo $cityInput ?>"
                        >
                    </td>
                <?php } else {
                    echo "<td class=\"dispGrowerInfo\">".$city."</td>";
                }
                ?>


                <?php
                    if($editProfile) {
                    echo "<td><label for=\"state\">State: </label></td>";
                    echo "<td><select name=\"state\" id=\"GrowerState\">";
                        $states = file_get_contents("../states.csv");
                        $states = explode(",",$states);
                        array_reverse($states);
                        array_pop($states);
                        array_pop($states);
                        sort($states);
                        $idx=0;
                        foreach ($states as $stateLoop){
                            $stateLoop = preg_split('/\r\n|\r|\n/', $stateLoop);
                            echo "<option value=\"".$stateLoop[0]."\"";
                            if($stateLoop[0]===$state){
                                echo " selected=\"selected\"";
                            }
                            echo ">".$stateLoop[0]."</option>";
                            $idx++;
                        }
                    echo "</select></td>";
                 } else {
                    echo "<td>State: </td><td class=\"dispGrowerInfo\">".$state."</td>";
                }
                ?>

        </tr>
    </table>
<?php if($_SESSION["usertype"]=="grower") { ?>

    <div class="userFormButtons">
        <?php if($editProfile) { ?>
            <button type="button" class="generalButton" id="submitEditGrowerProfile">Submit</button>
            <button type="button" class="generalButton" id="cancelEditGrowerProfile">Cancel</button>
            <?php
        } else {
            ?>
            <button type="button" class="generalButton" id="editGrowerProfile">Edit Profile Info</button>
        <?php } ?>
    </div>
</form>
    <?php } else {echo "</div>";}    ?>
<?php

    function fetchAboutGrower($aboutFile=null){
        $rtn = "";
        if ($aboutFile==null){
            $rtn = "This section is blank, ... write something!!";
        } else {

            $rtn = file_get_contents("descr/grower/".$aboutFile);

        }

        return $rtn;
    }

?>

