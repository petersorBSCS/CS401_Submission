<?php

    require_once ("Dao.php");

    $dao = new Dao();

    if (!isset($_SESSION)) {
        session_start();
    }

    // Will need a
        // 1. Picture File
        // 2. Harvest Date
        // 3. Grower username

    $filters = null;


    if (isset($_POST)) {
        $filters = array();
        // Populate the names array for product names

        if( isset($_POST["names"])){
            $filters["names"] = explode(",", $_POST["names"]);
            /*
            echo "<pre>";
            echo print_r($filters["names"]);
            echo "</pre>";
            */
        }

        if( isset($_POST["distance"]) && isset($_POST["zip"])){
            //$filters["distance"] = $_POST["distance"];
            //$filters["zip"] = $_POST["zip"];

            $zipCodes = getZipList($_POST["zip"],$_POST["distance"]);

            $filters["zips"] = $zipCodes;
            /*
            echo "<h1>ZIP Codes</h1>";
            echo "<pre>";
            echo print_r($zipCodes);
            echo "</pre>";
            */

        }

        if (isset($_POST["beforeAfter"]) && isset($_POST["date"])){
            $filters["beforeAfter"] = $_POST["beforeAfter"];
            $filters["date"] = $_POST["date"];
        }

        if (isset($_POST["grower"])){
            $filters["grower"] = $_POST["grower"];
        }
/*
        echo "Filters: </br>";
        foreach($filters as $key => $value){
            echo $key.": ".$value."</br>";
        }
*/



    }

/*
echo "POST:</br>";
echo "<pre>";
echo print_r($_POST);
echo "</pre>";
*/

// Get the zipCode list



//exit;





    $products = array();

    if ($filters==null){
        // Fetch all the products
        $products = $dao->getProductThumbs();
        /*
        echo "No filters!";
        */
    } else {
        $products = $dao->getProductThumbs($filters);
/*
        echo "Filters!</br>";
        echo "<pre>";
        echo print_r($filters);
        echo "</pre>";
*/
    }
/*
    echo "Results: </br>";
echo "<pre>";
echo print_r($products);
echo "</pre>";
    exit;
*/
    /*
 *
    echo "<pre>";
    echo $products;
    echo "</pre>";
*/
    //exit;

    //array_reverse($products);


/*
    echo "<pre>";
    echo print_r($products);
    echo "</pre>";
*/
//    exit;

    $itemImgPath = "img\items";

$pids = array();
// Loop through the resultset

// Build up an array of the products for this search
$prodSearchResults = array();



$numProdPerPage = 20;
$startProdIdx=0;
$endProdIdx=0;

$prodIdx = 1;

$prodSearchIndexes = array();

$displayed_results = false;

$html_out = "";

foreach($products as $product) {
    if (!in_array($product["prodID"],$pids)) { // Ignore duplicates .. just making a thumbnail
        //date("Y-m-d",rand(1262055681,1262155681)), $growerID[$idx%3]);
        $elem_out = "<div class=\"product " . $product["prodID"] . "\">";
        $elem_out .= "<p ><strong>" . $product["prodName"] . "</strong></p>";
        if($product["prodURL"]==null){
            $product["prodURL"] = "../sample/VeggieCharacters.jpg";
        }
        $elem_out .= "<div class=\"prodThumbnail\"><img src=\"" . $itemImgPath . "/" . $product["prodURL"] . "\"/></div>";
        $elem_out .= "<p><strong>Harvest Date: </strong>" . $product["prodHarvest"] . "</p>";
        $elem_out .= "<p><strong>Grower ID: </strong><a href='viewGrower.php?growerName=".$product["growerName"];
        $elem_out .= "' class='growerID_".$product["growerID"]."'></a>" . $product["growerName"] . "</p>";
        $elem_out .= "<p><strong>Zip : </strong>".$product["zip"]."</p>";
        //        $html_out .= "<a href=\"#\">order item</a>";
        $elem_out .= "<input type=\"hidden\" value=\"" . $product["prodID"] . "\"/>"; // Couldn't find this elem in JQuery!!
        $elem_out .= "</div>";
        array_push($pids,$product["prodID"]);
        array_push($prodSearchResults,$elem_out);
//            echo $html_out;

        if($prodIdx==$numProdPerPage){
            array_push($prodSearchIndexes,array($startProdIdx,$endProdIdx));
            $startProdIdx += $prodIdx;
            $prodIdx=0;
            if (!$displayed_results) {
                echo "<div id='productResultsSpan'>";
                $idx = $prodSearchIndexes[0][0];
                $thisProd = null;
                while ($idx <= $prodSearchIndexes[0][1]) {
                    $html_out .= $prodSearchResults[$idx];
                    $idx++;
                }
                // Store the current product batch Index to the session
                $_SESSION["currProdBatchIdx"] = 0;
                echo $html_out;
                echo "</div>";
                $displayed_results = true;
                // If this isn't a POST request, just return here (for faster loading)
                if ($_SESSION["firstTimeProdLoad"]){
                    $_SESSION["firstTimeProdLoad"] = false;
                }
            }
        }

        $prodIdx++;
        $endProdIdx++;

    }
}

$endProdIdx--;

// If we didn't at least get a full 20 results ...

if (sizeof($prodSearchIndexes)==0){
    array_push($prodSearchIndexes,array($startProdIdx,$endProdIdx));
}

if (!$displayed_results){
    echo "<div id='productResultsSpan'>";
    $idx=$prodSearchIndexes[0][0];
    $thisProd = null;
    while( $idx <= $prodSearchIndexes[0][1]){
        $html_out .= $prodSearchResults[$idx];
        $idx++;
    }
    // Store the current product batch Index to the session
    $_SESSION["currProdBatchIdx"]=0;
    echo $html_out;
    echo "</div>";
}



// Push the last index tuple to the indexes array
array_push($prodSearchIndexes,array($startProdIdx,$endProdIdx));

// Use AJAX to fetch this number and display it
$_SESSION["numProdInSearch"] = sizeof($pids);
/*
echo "NumProds: ".sizeof($pids);
echo "<pre>";
echo print_r($pids);
echo "</pre>";
*/

// Use AJAX to fetch the next batch of products
$_SESSION["prodInSearch"] = $prodSearchResults;

// Store the (start,end) index tuples to the session
$_SESSION["prodSearchIndexes"] = $prodSearchIndexes;

/*
echo "<pre>";
echo print_r($prodSearchIndexes);
echo "</pre>";

exit;
*/




/*
echo "<div id='productResultsSpan'>";
    foreach($products as $product) {
        if (!in_array($product["prodID"],$pids)) { // Ignore duplicates .. just making a thumbnail
            //date("Y-m-d",rand(1262055681,1262155681)), $growerID[$idx%3]);
            $html_out .= "<div class=\"product " . $product["prodID"] . "\">";
            $html_out .= "<p ><strong>" . $product["prodName"] . "</strong></p>";
            $html_out .= "<div class=\"prodThumbnail\"><img src=\"" . $itemImgPath . "/" . $product["prodURL"] . "\"/></div>";
            $html_out .= "<p><strong>Harvest Date: </strong>" . $product["prodHarvest"] . "</p>";
            $html_out .= "<p><strong>Grower ID: </strong><a href='#' class='growerID_".$product["growerID"]."'>" . $product["growerName"] . "</a></p>";
            $html_out .= "<p><strong>Zip : </strong>".$product["zip"]."</p>";
    //        $html_out .= "<a href=\"#\">order item</a>";
            $html_out .= "<input type=\"hidden\" value=\"" . $product["prodID"] . "\"/>"; // Couldn't find this elem in JQuery!!
            $html_out .= "</div>";
            array_push($pids,$product["prodID"]);

//            echo $html_out;
        }
    }

// Use AJAX to fetch this number and display it
$_SESSION["numProdInSearch"] = sizeof($pids);

echo $html_out;
echo "</div>";
*/

    // Return a list of zip codes within the given miles radius
    function getZipList($zipCode=null, $radius=null) {

        // Return of no inputs given
        if ($zipCode==null || $radius==null ){ return;}

        // Fetch a new AppKey from the API vendor
        $html = preg_split('/\r\n|\r|\n/',file_get_contents("http://zipcodeapi.com/API#radius"));

        $appKey = "";

        foreach($html as $htmlLine){
            if(preg_match("/name=\"api_key\"/",$htmlLine)){

                $appKeyString = explode("value",$htmlLine);
                $appKeyString = explode("\"",$appKeyString[1]);
                $appKey = $appKeyString[1];
                break;
            }
        }

        $format = "csv";
        $zip = $zipCode;
        $radius = $radius;
        $units = "mile";
        $apiURL = "https://www.zipcodeapi.com/rest/";

        $getString = $apiURL.$appKey."/radius.".$format."/".$zip."/".$radius."/".$units;

        $zipCodeList = file_get_contents($getString);

        $rows = preg_split('/\r\n|\r|\n/',$zipCodeList);

        $zipList = array();
        $idx = 0;
        foreach ($rows as $row){
            if($idx!=0){
                $rowVals = explode(",",$row);
                if (strlen($rowVals[0])==5) {
                    array_push($zipList, $rowVals[0]);
                }
            }
            $idx++;
        }

        return $zipList;

    }
