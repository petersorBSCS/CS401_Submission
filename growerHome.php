<?php

require_once("php/Dao.php");

$dao = new Dao();

?>
<div class="growerOptions">

    <span class="growerOptions"><h1><?php echo $_SESSION["username"]; ?>'s Home Page</h1></span>
    <span class="growerOptions"><button class="generalButton" id="manageInventoryButton">Manage Inventory</button></span>
    <span class="growerOptions"><button class="generalButton" id="manageOrdersButton">Manage Orders</button></span>
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

<!-- Alert the user there was a problem uploading the file -->
<div class="userFormContainer">
    <div class="userForm"  id = "profilePicUploadError" hidden="true">
            <span>

            </span>
    </div>
</div>