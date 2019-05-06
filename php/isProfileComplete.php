<?php


session_start();


$completeProf;
if(!isset($_SESSION["profileComplete"])){
    $completeProf = "false";
} else {
    $completeProf = $_SESSION["profileComplete"];
}

echo $completeProf;