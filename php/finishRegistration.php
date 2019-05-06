<?php

require_once ('Dao.php');
session_start();
$dao = new Dao();

// Grab the POST data
$params = array();
$params["firstName"] = (isset($_POST["firstname"]) ? $_POST["firstname"]: "");
$params["lastName"] = (isset($_POST["lastname"]) ? $_POST["lastname"]: "");
$params["address"]= (isset($_POST["address"]) ? $_POST["address"]: "");
$params["zip"] = (isset($_POST["zip"]) ? $_POST["zip"]: "");
$params["county"] = (isset($_POST["county"]) ? $_POST["county"]: "");
$params["city"] = (isset($_POST["city"]) ? $_POST["city"]: "");
$params["state"] = (isset($_POST["state"]) ? $_POST["state"]: "");
$about = (isset($_POST["growerAbout"]) ? $_POST["growerAbout"]: "");

// Adding the "about me" stuff
require_once ('addAbout.php');

//$type = "grower";

$type = $_SESSION["usertype"];

if($type=="grower"){
    storeAbout($about,"grower",$_SESSION["userID"]);
    //storeAbout($about,"grower",2);
}

$params["uid"] = $_SESSION["userID"];

$resp = $dao->finishProfile($params);

$_SESSION["profileComplete"]="true";
