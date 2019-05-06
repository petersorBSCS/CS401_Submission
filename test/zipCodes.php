<?php

//echo print_r(fetchZips(83713,50));

function fetchZips($zip, $radius)
{

    // Fetch a new AppKey from the API vendor
    $htmlLine = preg_split('/\r\n|\r|\n/',file_get_contents("http://zipcodeapi.com/API#radius"));

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
    $units = "mile";
    $apiURL = "https://www.zipcodeapi.com/rest/";

    $getString = $apiURL . $appKey . "/radius." . $format . "/" . $zip . "/" . $radius . "/" . $units;

    $zipCodeList = file_get_contents($getString);

    $rows = preg_split('/\r\n|\r|\n/', $zipCodeList);

    $zipList = array();
    $idx = 0;
    foreach ($rows as $row) {
        if ($idx != 0) {
            $rowVals = explode(",", $row);
            if (strlen($rowVals[0]) == 5) {
                array_push($zipList, $rowVals[0]);
            }
        }
        $idx++;
    }

    return $zipList;
}
?>