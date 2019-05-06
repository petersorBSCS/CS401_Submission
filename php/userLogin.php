<?php

    session_start();

    require_once ('Dao.php');

    $dao = new Dao();

    // Grab the POST data
    $login = (isset($_POST["login"]) ? $_POST["login"]: "");
    $password = (isset($_POST["passwordLogin"]) ? $_POST["passwordLogin"]: "");
    $userType = (isset($_POST["usertype"]) ? $_POST["usertype"]: "");

    if(isset($_POST["usertype"])) {
        $result = $dao->loginUser($login, $password, $userType);
    } else {
        $result = $dao->loginUser($login, $password);
    }

    if($result["num"]>1){
        // Need to prompt the user to select an account type
        echo "multiple";

    } elseif ($result["validLogin"] == "true" /* && $result["email_validated"] == "true")*/) {

            // Set the session cookie
            $expireTime = time() + 3600; // 1 hour
            setcookie("loggedIn","true",$expireTime);

            $_SESSION["username"] = $result["username"];
            $_SESSION["usertype"] = $result["usertype"];
            $_SESSION["validLogin"] = true;
            $_SESSION["userID"] = $result["id"];
            $_SESSION["numCartItems"] = 0;

            $completedProfile = false;

            // Check to make sure the user's information is completed
            if ($result["usertype"] != "guest") {
                $completedProfile = ($result["firstname"] != null) ? true : false;
                $completedProfile = ($result["lastname"] != null) ? $completedProfile : false;
                $completedProfile = ($result["username"] != null) ? $completedProfile : false;
                $completedProfile = ($result["usertype"] != null) ? $completedProfile : false;
                $completedProfile = ($result["address"] != null) ? $completedProfile : false;
                $completedProfile = ($result["city"] != null) ? $completedProfile : false;
                $completedProfile = ($result["county"] != null) ? $completedProfile : false;
                $completedProfile = ($result["state"] != null) ? $completedProfile : false;
                $completedProfile = ($result["zip"] != null) ? $completedProfile : false;
            }

            // Tell other pages that it's ok to place orders or manage inventory
            $_SESSION["profileComplete"] = ($completedProfile) ? "true" : "false";

            // Check for an existing orderID to maintain the shopping cart between shopper logins
            if($userType=="shopper"){
                $extOrderID= $dao->fetchPendingOrder($_SESSION["userID"]);

                if ($extOrderID!=null){
                    $_SESSION["orderID"] = $extOrderID;
                }
            }


        } else {
            // Return to the login form, and highlight errors
            $_SESSION["login_status"] = "Invalid login credentials";
            $_SESSION["login_preset"] = $login;
            echo "invalid";
        }