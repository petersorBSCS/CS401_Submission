<?php
session_start();

// Set the session type for users with more than 1 type of account

$_SESSION["usertype] = $_POST["usertype"];