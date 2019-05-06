<?php

require_once ("Dao.php");

ini_set('allow_url_fopen','true');

function storeAbout($about=null,$type=null,$id=null){

    // If this is a shopper, this will be blank
    if($about==null){
        return 0;
    }

    $dao = new Dao();

    // Check to see if there's already a descr on file
    $existingFile = $dao->checkExistingDescr($type,$id);

    // Otherwise, create a new text file and point to it in the database

    $DescrPath = "../descr/".$type."/";

    $descrFile = "";

    if ($existingFile==null){
        $DescrFiles = scandir($DescrPath);
        $numDescr = sizeof($DescrFiles) - 2 + 1; // exclude (.) and (..)
        $targetFileName = $DescrPath . $numDescr . ".txt";
        $descrFile = $numDescr . ".txt";
    } else {
        $targetFileName = $DescrPath . $existingFile;
        $descrFile = $existingFile;
    }

    echo $targetFileName;

    $targetFile = fopen($targetFileName, "w") or die("Error Opening File");

    $about = nl2br(str_replace(" ", " &nbsp",$about));

    fwrite($targetFile, $about);

    fclose($targetFile);

    // Add this description to the DB
    if($existingFile==null) {
        $dao->addDescr($type, $id, $descrFile);
    }

    return 0;
}
