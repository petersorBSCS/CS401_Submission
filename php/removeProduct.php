<?php


$prodID = isset($_POST["prodID"]) ? $_POST["prodID"] : null;


require_once ('Dao.php');

$dao = new Dao();


// Remove this product and all associated order_items, and product images
$resp = $dao->removeProduct($prodID);

