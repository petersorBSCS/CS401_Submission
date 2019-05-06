<?php

session_start();

// Fetch the next/prev batch of products

$dir = $_POST["dir"]; // Next batch or previous batch?

$prodSearchResults = $_SESSION["prodInSearch"];
$prodSearchIndexes = $_SESSION["prodSearchIndexes"];
$currBatchIdx = $_SESSION["currProdBatchIdx"];

$html_out = "<div id='productResultsSpan'>";
$idx=0;
$thisProd = null;
$numProdPerPage = 20;

if($dir=="next") {
    if($currBatchIdx==(sizeof($prodSearchIndexes)-1)){
        $idx = $prodSearchIndexes[sizeof($prodSearchIndexes)-1][0];
        while( $idx <= $prodSearchIndexes[sizeof($prodSearchIndexes)-1][1]){
            $html_out .= $prodSearchResults[$idx];
            $idx++;
        }
    } else {
        $currBatchIdx++;
        $idx = $prodSearchIndexes[$currBatchIdx][0];
        while( $idx <= $prodSearchIndexes[$currBatchIdx][1]){
            $html_out .= $prodSearchResults[$idx];
            $idx++;
        }
    }

} else {
    if($currBatchIdx==0){
        $idx = $prodSearchIndexes[0][0];
        while( $idx <= $prodSearchIndexes[0][1]){
            $html_out .= $prodSearchResults[$idx];
            $idx++;
        }
    } else {
        $currBatchIdx--;
        $idx = $prodSearchIndexes[$currBatchIdx][0];
        while( $idx <= $prodSearchIndexes[$currBatchIdx][1]){
            $html_out .= $prodSearchResults[$idx];
            $idx++;
        }
    }
}

// Store the current product Index to the session
$_SESSION["currProdBatchIdx"]=$currBatchIdx;


if( sizeof($prodSearchIndexes)==1){

    // Fetch the "next" indexes
    $next_start = $prodSearchIndexes[0][0];
    $next_end = $prodSearchIndexes[0][1];
    // Fetch the "previous" indexes
    $prev_start = $prodSearchIndexes[0][0];
    $prev_end = $prodSearchIndexes[0][1];

} else {

    if ($currBatchIdx==0){

        // Fetch the "previous" indexes
        $prev_start = $prodSearchIndexes[$currBatchIdx][0];
        $prev_end = $prodSearchIndexes[$currBatchIdx][1];

        // Fetch the "next" indexes
        $next_start = $prodSearchIndexes[$currBatchIdx+1][0];
        $next_end = $prodSearchIndexes[$currBatchIdx+1][1];


    } elseif ($currBatchIdx==(sizeof($prodSearchIndexes)-1)){

        // Fetch the "next" indexes
        $next_start = $prodSearchIndexes[$currBatchIdx][0];
        $next_end = $prodSearchIndexes[$currBatchIdx][1];

        // Fetch the "previous" indexes
        $prev_start = $prodSearchIndexes[$currBatchIdx-1][0];
        $prev_end = $prodSearchIndexes[$currBatchIdx-1][1];

    } else {

        // Fetch the "next" indexes
        $next_start = $prodSearchIndexes[$currBatchIdx+1][0];
        $next_end = $prodSearchIndexes[$currBatchIdx+1][1];

        // Fetch the "previous" indexes
        $prev_start = $prodSearchIndexes[$currBatchIdx-1][0];
        $prev_end = $prodSearchIndexes[$currBatchIdx-1][1];

    }
}

// Fix up the indexing for display
$prev_end++;
$prev_start++;
$next_end++;
$next_start++;

$html_out .= "</div>";
echo $html_out."##prevNextIdx##".$prev_start."_".$prev_end."_".$next_start."_".$next_end;
