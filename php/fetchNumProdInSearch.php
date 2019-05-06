<?php

session_start();

if(isset($_SESSION["numProdInSearch"])){

    $num = $_SESSION["numProdInSearch"];

    $plural = "";
    if ($num>1 || $num==0){
        $plural = "s";
    }

    $html_out =  number_format($_SESSION["numProdInSearch"],0)." result".$plural;

    $prodSearchIndexes = $_SESSION["prodSearchIndexes"];
    $currBatchIdx = $_SESSION["currProdBatchIdx"];


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

    echo $html_out."##prevNextIdx##".$prev_start."_".$prev_end."_".$next_start."_".$next_end;

} else {
    echo "I'm shopping for...";
}