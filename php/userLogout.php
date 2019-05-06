<?php

    session_start();

    // Check to see if there is a pending order and destroy it
    require_once ("Dao.php");

    $dao = new Dao();

    if (isset($_SESSION["orderID"])) {
     //   $dao->removeOrder($_SESSION["orderID"]);
    }

    session_destroy();
    header("location:../index.php");