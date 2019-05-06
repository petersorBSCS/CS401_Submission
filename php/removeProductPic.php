<?php
if (!isset($_SESSION)){
    session_start();
}

require_once ("Dao.php");

$dao = new Dao();

$picID = $_POST["id"];

$url = $dao->deleteProductPic($picID);

// Delete the image from the server
$productImgPath = "../img/items";
$file = $productImgPath."/".$url;
if(!unlink($file)){
    echo "Error deleting ";
} else {
    echo "File deleted";
}