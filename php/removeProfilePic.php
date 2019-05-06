<?php
if (!isset($_SESSION)){
    session_start();
}

require_once ("Dao.php");

$dao = new Dao();

$picID = $_POST["id"];


$url = $dao->deleteUserPic($picID);

// Delete the image from the server
$usrImgPath = "../img/users";
$file = $usrImgPath."/".$url;
if(!unlink($file)){
    echo "Error deleting ";
} else {
    echo "File deleted";
}