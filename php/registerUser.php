<?php

require_once ('Dao.php');
session_start();
$dao = new Dao();


// Grab the POST data
$email = (isset($_POST["email"]) ? $_POST["email"]: "");
$password = (isset($_POST["password"]) ? $_POST["password"]: "");
$username = (isset($_POST["username"]) ? $_POST["username"]: "");
$usertype = (isset($_POST["usertype"]) ? $_POST["usertype"]: "");

$_SESSION["email"] = $email;
$_SESSION["username"] = $username;

$resp = $dao->registerUser($email, $password, $username, $usertype);

// If it didn't go through, which type of error was it?
if ($resp["error_type"]!="none") {
    echo $resp["error_type"];
}

exit;
//header("location:../index.php");


$emailValidate = $dao->addConfirm($username, $email);

// Send the confirmation email
sendConfirmationEmail($emailValidate);

exit;

exit;

function sendConfirmationEmail($emailValidate){

    $email = $emailValidate["email"];
    $key = $emailValidate["key"];

    $to = $email;
    $subject = "My Neighbor's Garden email confirmation";


    $header = "Recipient: ".$email. "\r\n";
    $header .= "Sender: admin1@myneighborsgarden.com\r\n";
    $header .= "From: admin2@myneighborsgarden.com\r\n";
    $header .= "Recipient: ".$email."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";

    $message = "<p>Welcome to My Neighbor's Garden!</p>";
    $message .= "<p>Please click on the following link to activate your account:</p>";
    $message .= "<a href=http://localhost/validateEmail.php?email=".$email."&key=".$key."/>Validate Email</a>";

    // Make sure the mail server is configured properly
//    ini_set("SMTP","127.0.0.1");
//    ini_set("sendmail_from","admin1@myneighborsgarden.com");
//    ini_set("sendmail_to",$email);
//    ini_set("mail.log" ,"C:\Users\Rob\Documents\infinite-woodland-34036\logs\mail_log.txt");

    $rtn = mail($to,$subject,$message,$header);

    if ($rtn == true) {
        echo "Success";
    } else {
        echo "Failure";
    }
}


// Server-Side validation

// Capture all the keys and values
$val = [];  // Boolean array for storing if each field is correct
$keys = [];
$values = [];
foreach($_POST as $key=>$value){
    array_push($val,false);
    array_push($keys, $key);
    array_push($keys, $value);
}

// Setup the regex array for this form
$regex = array(
    "email"             => "/^[a-zA-Z_\-]+@(([a-zA-Z_\-])+\.)+[a-zA-Z]{2,4}$/",
    "password"          => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/",
    "password_confirm"  => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/",
    "firstname"         => "/^[a-zA-Z\-\']+$/",
    "lastname"          => "/^[a-zA-Z\-\']+$/",
    "username"          => "/^[a-zA-Z0-9_\-\']+$/",
    "usertype"          => "/^Grower|Buyer$/",
    "address"           => "/^[0-9]+$/",
    "direction"         => "/^N|NW|W|SW|S|SE|E|NE$/",
    "street"            => "/^[a-zA-Z\-\']+$/",
    "street_type"       => "/^DR|CT|CR|PL|WAY$/",
    "city"              => "/^[a-zA-Z\-\']+$/",
    "county"            => "/^[a-zA-Z\-\']+$/",
    "state"             => "/^[A-Z]{2}$/",
    "zip"               => "/^[0-9]{5}$/"
);

$val_idx = 0;
// Validate the email
$numVal = 0;
foreach($keys as $thisKey){

    // Check if the post element matches its regex
    if( preg_match($regex[$thisKey],$_POST[$thisKey])){
        $val[$val_idx] = true;
        $numVal++;
    }
    $val_idx++;
}

// See if we passed all the regex tests
if($numVal==sizeof($val)){
    header("location:../signup.php");
    exit(1);
}

echo '<pre>';
print_r($_POST);
echo '</pre>';


$dao->registerUser($email, $password, $firstname, $lastname, $username, $usertype, $address,
    $direction, $street, $street_type, $city, $county, $state, $zip);

$result = $dao->getUsers();

echo "<table>";
echo "<tr>";
echo "<th>id</th>";
echo "<th>user_id</th>";
echo "<th>email</th>";
echo "<th>username</th>";
echo "<th>password</th>";
echo "</tr>";
foreach($result as $r) {
    echo "<tr>";
/*
    foreach($r as $v){
        echo "<td>" . $v . "</td>";
    }
*/
    echo "<td>" . $r["email"]. "</td>";
    echo "<td>" . $r["username"]. "</td>";
    echo "<td>" . $r["password"]. "</td>";

    echo "</tr>";
}
echo "</table>";

// Get the location of this file
$loc = __DIR__;

//header("location:../manageInventory.html");
