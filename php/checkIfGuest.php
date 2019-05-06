<?php

    session_start();

    if($_SESSION["usertype"]=="guest"){
        echo "guest";
    } elseif(/*$_SESSION["completeProfile"]=="false"*/false){
        echo "incompleteProfile";
    }
