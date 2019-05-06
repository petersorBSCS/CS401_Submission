<?php

    if(!isset($_SESSION)){
        session_start();
    }

    // If this is a POST request, then use the grower's ID from the POST (shopper's view of grower's page)
    if(!($_SESSION["usertype"]=="grower")){
        $growerID = $_SESSION["viewGrowerID"];
    } else {
        $growerID = $_SESSION["userID"];
    }

    require_once ("Dao.php");
    $dao = new Dao();

    // Fetch this grower's user images
    $userImages = $dao->fetchUserPics($growerID);
    /*
                echo "<pre>";
                echo print_r($userImages);
                echo "</pre>";
    */


    $html_out = "";


    if(!($_SESSION["usertype"]=="grower")){
        $name = $_SESSION["viewGrowerName"];
    } else {
        $name = $_SESSION["username"];
    }

    if ($userImages["numImg"]==0){

        if( !($_SESSION["usertype"]=="grower") ) {
            $html_out .= "<h1>Default Profile Picture</h1>";
        }

        $html_out .= "<div id='imgContainer'>";

        // If they haven't selected an image yet, use Spongebob
        $html_out .= "<img class=\"displayImg\"  src=\"..\img\users\spongebob.png\">";
        $html_out .= "</div>";

        if( ($_SESSION["usertype"]=="grower") ) {
            $html_out .="<div>";
            $html_out .= "<form id=\"profilePicAddForm\" method=\"POST\" enctype=\"multipart/form-data\" action=\"php/addProfilePic.php\">";
            $html_out .= "<button type=\"button\"class=\"generalButton\" id=\"addProfilePicButton\">Add Picture</button>";
            $html_out .= "<input class=\"fileUpload\" type=\"file\" id=\"addProfilePic\" name=\"addProfilePic\">";
            $html_out .= "</form>";
            $html_out .="</div>";
        }

    } else {

        if( $_SESSION["usertype"]=="grower" ) {
            $html_out .= "<h1>Profile Picture</h1>";
        } else {
            $html_out .= "<h1>".$_SESSION["viewGrowerName"]."'s Pictures</h1>";
        }

        $html_out .= "<div id='imgContainer'>";

        // Grab all the image url's
        $Images = array();
        foreach($userImages["userImages"] as $img) {
            array_push($Images, $img);
        }

        // Display the first image
        $userImg = array_pop($Images);
        $html_out .= "<img class=\"displayImg\"  src=\"..\img\users\\".$userImg["url"]."\" alt=\"".$name."\" id=\"userImg_0\">";
        $html_out .= "<input type=\"hidden\" id=\"userImgID_0_".$userImg["id"]."\">";


        // Add the rest of the images (if any left)
        $userImgIdx = 1;

        foreach($Images as $userImage) {
            //$img = array_pop($Images);
            $html_out .= "<img src=\"..\img\users\\".$userImage["url"]."\" alt=\"".$name."\" id=\"userImg_".$userImgIdx."\" >";
            $html_out .= "<input type=\"hidden\" id=\"userImgID_".$userImgIdx."_".$userImage["id"]."\">";
            $userImgIdx++;
        }

        // Cycle through the pictures

        $html_out .= "</div>";
        if($userImages["numImg"]>1) {
            $html_out .= "<div>";
            $html_out .= "<button class=\"generalButton\" id=\"prevProfilePic\"><</button>";
            $html_out .= "<button class=\"generalButton\" id=\"nextProfilePic\">></button>";
            $html_out .= "</div>";
        }

        $html_out .="<div>";

        if($userImages["numImg"]>0 && ($_SESSION["usertype"]=="grower") ) {
            $html_out .= "<form id=\"profilePicAddForm\" method=\"POST\" enctype=\"multipart/form-data\" action=\"php/addProfilePic.php\">";
            $html_out .= "<button type=\"button\"  class=\"generalButton\" id=\"addProfilePicButton\">Add Picture</button>";
            $html_out .= "<input class=\"fileUpload\" type=\"file\" id=\"addProfilePic\" name=\"addProfilePic\" multiple>";
            $html_out .= "<input type=\"hidden\" name=\"something\" value='\"Something\"'>";
            $html_out .= "</form>";
            $html_out .= "<button type=\"button\"  class=\"generalButton\" id=\"removeProfilePicButton\">Remove Picture</button>";
        }
        $html_out .="</div>";
    }
    echo $html_out;