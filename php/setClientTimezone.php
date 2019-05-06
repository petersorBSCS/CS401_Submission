<?php

session_start();

// Set the client's timezone
$_SESSION["clientTimezone"] = $_POST["tz"];