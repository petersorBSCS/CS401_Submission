<?php
session_start();
$_SESSION["username"] = "guest";
$_SESSION["usertype"] = "guest";
$_SESSION["validLogin"] = true;
header("location:../userHome.php");