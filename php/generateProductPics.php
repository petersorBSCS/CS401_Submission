<?php

if(!isset($_SESSION)){
    session_start();
}

require_once ("Dao.php");

$prodID=null;
if(isset($_POST["prodID"])){
    $prodID = $_POST["prodID"];
    if (isset($_POST["ajax"]) && $_POST["ajax"]=="true") {
        echo generatePics($prodID);
    }
}

function generatePics($prodID) {

    $dao = new Dao();
    // Fetch this grower's user images

    $prodImages = array();
    if($prodID!=null) { $prodImages = $dao->getProductPics($prodID); }
    /*
                echo "<pre>";
                echo print_r($userImages);
                echo "</pre>";
    */


    $html_out = "";
/*
    $html_out .= "<pre>";
    $html_out .= print_r($prodImages);
    $html_out .= "</pre>";

    return $html_out;
*/
    if ((sizeof($prodImages)==0) || $prodID==null){

        $html_out .= "<div id='imgContainer'>";

        $html_out .= "<h3>(Default Product Picture)</h3>";

        // If they haven't selected an image yet, use VeggieTales
        $html_out .= "<div id=\"prodImgContainer\">";
        $html_out .= "<img class=\"displayImg\"  src=\"..\img\sample\VeggieCharacters.jpg\">";
        $html_out .= "</div>";

        if($prodID!=null){ // If we're not adding a new product
            $html_out .="<div id='addRemoveImg'>";
            $html_out .= "<form id=\"productPicAddForm\" method=\"POST\" enctype=\"multipart/form-data\" action=\"php/addProductPic.php\">";
            $html_out .= "<button type=\"button\"class=\"generalButton\" id=\"addProductPicButton\">Add Picture</button>";
            $html_out .= "<input class=\"fileUpload\" type=\"file\" id=\"addProductPic\" name=\"addProductPic\">";
            $html_out .= "<input type=\"hidden\" name=\"prodID\" value=\"$prodID\">";
            $html_out .= "</form>";
            $html_out .="</div>";
        }
        $html_out .= "</div>";

    } else {

        $html_out .= "<div id='imgContainer'>";

        // Grab all the image url's
        $Images = array();
        foreach($prodImages as $img) {
            array_push($Images, $img);
        }

        // Display the first image
        $prodImg = array_pop($Images);
        $html_out .= "<div id=\"prodImgContainer\">";
        $html_out .= "<img class=\"displayImg\"  src=\"..\img\items\\".$prodImg["url"]."\" id=\"prodImg_0\">";
        $html_out .= "<input type=\"hidden\" id=\"prodImgID_0_".$prodImg["id"]."\">";


        // Add the rest of the images (if any left)
        $prodImgIdx = 1;

        foreach($Images as $prodImage) {
            $html_out .= "<img src=\"..\img\items\\".$prodImage["url"]."\" id=\"prodImg_".$prodImgIdx."\" >";
            $html_out .= "<input type=\"hidden\" id=\"prodImgID_".$prodImgIdx."_".$prodImage["id"]."\">";
            $prodImgIdx++;
        }

        // To Cycle through the pictures

        $html_out .= "</div>";
        if(sizeof($prodImages)>1) {
            $html_out .= "<div>";
            $html_out .= "<button class=\"generalButton\" id=\"prevProdPic\"><</button>";
            $html_out .= "<button class=\"generalButton\" id=\"nextProdPic\">></button>";
            $html_out .= "</div>";
        }

        $html_out .="<div id='addRemoveImg'>";

        if(sizeof($prodImages)>0) {
            $html_out .= "<form id=\"productPicAddForm\" method=\"POST\" enctype=\"multipart/form-data\" action=\"php/addProductPic.php\">";
            $html_out .= "<button type=\"button\"  class=\"generalButton\" id=\"addProductPicButton\">Add Picture</button>";
            $html_out .= "<input class=\"fileUpload\" type=\"file\" id=\"addProductPic\" name=\"addProductPic\" multiple>";
            $html_out .= "<input type=\"hidden\" name=\"prodID\" value=\"$prodID\">";
            $html_out .= "</form>";
            $html_out .= "<button type=\"button\"  class=\"generalButton\" id=\"removeProductPicButton\">Remove Picture</button>";
        }
        $html_out .="</div>";
        $html_out .= "</div>";

    }

    return $html_out;
}