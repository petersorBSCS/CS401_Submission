<?php
session_start();

$force = true;

if($force || isset($_SESSION["validLogin"]) && $_SESSION["validLogin"]==true){

    // Fetch a new AppKey from the API vendor
    $htmlLine = preg_split('/\r\n|\r|\n/',file_get_contents("http://zipcodeapi.com/API#zipToLoc"));

    $appKey = "";

    foreach($htmlLine as $ht){
        if(preg_match("/name=\"api_key\"/",$ht)){

            $appKeyString = explode("value",$ht);
            $appKeyString = explode("\"",$appKeyString[1]);
            $appKey = $appKeyString[1];
            break;
        }
    }

    $format = "csv";
    $zip = $_POST["zip"];
    $apiURL = "https://www.zipcodeapi.com/rest/";

    $getString = $apiURL . $appKey . "/info." . $format . "/" . $zip . "/degrees";

    $cityState = file_get_contents($getString);

    $cityState = preg_split('/\r\n|\r|\n/', $cityState);

    $cityState = $cityState[1];

    $cityState = explode(",",$cityState);

    $city = $cityState[3];
    $State = $cityState[4];

    if (strlen($State)!=2){
        echo "error";
    }

    // Get the full state name
    $statesAbbrv = file_get_contents("..\statesAbbr.csv");
    $statesAbbrv = preg_split('/\r\n|\r|\n/', $statesAbbrv);
    $statesMap = array();
    foreach($statesAbbrv as $state){
        $state = explode(" ,",$state);
        if (sizeof($state)==2) {
            $statesMap[$state[1]] = $state[0];
        }
    }

    echo "___".$city."_".$statesMap[$State];

}