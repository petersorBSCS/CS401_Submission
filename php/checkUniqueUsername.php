<?php

/* AJAX fetch to see if the username is already taken */

    require_once('Dao.php');
    $dao = new Dao();
    session_start();

    if (!isset($_POST["username"])) {
        echo 0;
        $_SESSION["emailTaken"] = false;
    } else {
        // Check to see if the username or email is already in the system
        $result = $dao->checkUniqueUsername($_POST["username"]);
        $_SESSION["emailTaken"] = $result[0]["C"] ? true : false;
        echo $result[0]["C"];
    }
