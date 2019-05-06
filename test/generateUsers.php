<?php
require_once ("../php/Dao.php");
require_once ("zipCodes.php");
$dao = new Dao();

// Generate random users
$idx = 0;

$directions = ["N","NE", "E", "SE", "S", "SW", "W", "NW"];
$street_types = ["DR","CT", "CR", "PL", "WY", "ST", "BLVD", " "];
$zips = fetchZips(83713,50);

$states = file_get_contents("states.csv");
$states = explode(",",$states);

$numUsers = 1000;

$startTime = milliseconds();

$outString = "";

for($idx=0;$idx<$numUsers;$idx++) {

    // Sleep to give the DB a chance to keep up
    usleep(5000);

    $email = substr(md5(rand()),0,rand(5,10))."@".substr(md5(rand()),0,rand(5,10)).".com";
    $username = substr(md5(rand()),0,rand(5,10));
    $usertype = ($idx%2==0) ? "grower" : "shopper";
    $password = substr(md5(rand()),0,rand(5,10));

    $basic = array();

    array_push($basic, $email);
    array_push($basic, $username);
    array_push($basic, $usertype);
    array_push($basic, $password);

    $uid = $dao->registerUser($email,$password,$username,$usertype);

    $outString .= "Created user[".$username."][".$usertype."]:"."\n"."Email=> ".$email."\n"."Password=> ".$password."\n\n";

    echo "Created basic user [id=".$uid."]\n";
    echo print_r($basic);

    // Generate the rest of the user profile data

    $param = array();

    $firstName = substr(md5(rand()),0,rand(5,10));
    $lastName = substr(md5(rand()),0,rand(5,10));
    $address = rand(1000,15000);
    $direction = $directions[rand(0,sizeof($directions)-1)];
    $street = substr(md5(rand()),0,rand(5,10));
    $street_type = $street_types[rand(0,sizeof($street_types)-1)];
    $city = substr(md5(rand()),0,rand(5,10));
    $county = substr(md5(rand()),0,rand(5,10));
    $state = $states[rand(0,sizeof($states)-1)];
    $state = preg_split('/\r\n|\r|\n/', $state);
    $state = $state[0];
    $zip = $zips[rand(0,sizeof($zips)-1)];

    $address = $address." ".$direction." ".$street." ".$street_type;

    $param["firstName"] = $firstName;
    $param["lastName"] = $lastName;
    $param["address"] = $address;
    $param["city"] = $city;
    $param["county"] = $county;
    $param["state"] = $state;
    $param["zip"] = $zip;
    $param["uid"] = $uid["uid"];

    $resp = $dao->finishProfile($param);

    //echo $resp;

    //echo "Extended basic user [id=".$uid."]\n";
    //echo print_r($param);

}

$file = fopen("users.txt","w");

fwrite($file,$outString);

fclose($file);

$endTime = milliseconds();

$time = $endTime-$startTime;

function milliseconds() {
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
}