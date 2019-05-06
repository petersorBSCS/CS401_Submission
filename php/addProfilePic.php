<?php

session_start();

require_once ("Dao.php");

ini_set('file_uploads','On');

if (count($_FILES)>0){

        foreach($_FILES as $file) {

            if ($file["error"]>0){
                throw new Exception("Error: ". $file["error"]);
            } else {

                // Make sure it's really an image file
                $uploadOk = true;

                $check = getimagesize($file["tmp_name"]);
                if ($check !== false) {
                    // It's an image
                } else {
                    echo "Sorry, that's not an image file";
                    $uploadOk = false;
                }

                // Make sure it's not too big
                $uploadLimit = 5242880; // 5MB limit
                if ($file["size"] > $uploadLimit) {
                    echo "Sorry, that file is too large.";
                    $uploadOk = false;
                }

                // Restrict the file types
                $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                $allowed = array("jpg", "jpeg", "gif", "bmp", "png", "tiff");
                if (!in_array($imageFileType, $allowed)) {
                    echo "Sorry, only [" . implode(",", $allowed) . "] file types are allowed.";
                    $uploadOk = false;
                }

                // Calculate the filename
                $usrImgPath = "../img/users";
                $usrImgFiles = scandir($usrImgPath);
                $numUsrImg = sizeof($usrImgFiles) - 2 + 1; // exclude (.) and (..)
                $targetFile = $numUsrImg . "." . $imageFileType;

                if ($uploadOk) {
                    if (move_uploaded_file($file["tmp_name"], $usrImgPath . "/" . $targetFile)) {
                        echo "Upload Successful";

                        // Now, add the filename to the database
                        $dao = new Dao();

                        $dao->addProfileImage($_SESSION["userID"], $targetFile);

                    } else {
                        echo "Upload Error.";
                    }
                }
            }
        }
}