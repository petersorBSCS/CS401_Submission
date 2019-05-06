<?php

/* AJAX fetch to see if the email is already taken */

    require_once('Dao.php');
    $dao = new Dao();
    session_start();

    if (!isset($_POST["email"])) {
        $_SESSION["emailTaken"] = false;
        echo 0;
    } else {
        // Check to see if the username or email is already in the system
        $result = $dao->checkUniqueEmail($_POST["email"]);
        $_SESSION["emailTaken"] = $result[0]["C"] ? true : false;
        echo $result[0]["C"];
    }
