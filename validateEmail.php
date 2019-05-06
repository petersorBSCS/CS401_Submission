<?php

    require_once ('php/Dao.php');

    session_start();

    $dao = new Dao();

    // Grab the POST data
    $email = (isset($_GET["email"]) ? $_GET["email"]: "");
    $key = (isset($_GET["key"]) ? $_GET["key"]: "");

    echo "Email: ".$email,"</br>";
    echo "Key: ".$key;




