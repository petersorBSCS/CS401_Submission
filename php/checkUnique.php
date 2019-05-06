<?php

/* Just for doing Ajax fetches */

    require_once('Dao.php');
    $dao = new Dao();
    session_start();

    $email = (isset($_POST["email"])) ? $_POST["email"] : "";
    $username = (isset($_POST["username"])) ? $_POST["username"] : "";

    // Check to see if the username or email is already in the system
    $result = $dao->checkUnique($email, $username);
    echo $result[0]["C"];

    $_SESSION["emailTaken"] = $result[0]["C"] ? true : false;
    //$_SESSION["usernameTaken"] = $result["usernameTaken"];






