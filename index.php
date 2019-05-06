<?php

    // Start the session
    if(isset($_SESSION)){
        session_destroy();
        session_regenerate_id(TRUE);
    }

    session_start();

    // Set the the "this page" var
    $_SESSION["this_page"] = "Landing";

    // Add this path to the path, wipe everything else and make sure they log out
    $path["ref"] = array("php/userLogout.php", "Home");
    $_SESSION["path"] = $path;

    // For setting the user's home page
//    $path["ref"] = array("index.php", "Home");
//    $_SESSION["path"] = $path;

    // For future pages
//    array_push($_SESSION["path"], array("something.php","something"));

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Neighbors Crop</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="styles.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/png" href="icons/leaf.png">
<!--
    <script src="scripts/jquery-3.3.1.min.js"/>
    <script src="scripts/jquery-ui.js"/>

 -->

    <!-- Load JQuery and the forms javascript -->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>-->
    <script src="scripts/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script src="scripts/jstz.js" type="text/javascript"></script>
    <script src="scripts/forms.js" type="text/javascript"></script>
    <script src="scripts/ui.js" type="text/javascript"></script>

</head>

    <body>

    <?php
        require_once('Banner.php');
        $banner = new \Banner\Banner();
        $banner->print_banner(true);
    ?>

    <!-- About us, yada yada -->

        </div>

            <div class=homeScrollerImg id="backyardGarden">
                <p class=homeScrollerText>
                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
            </div>

            <div class=homeScrollerImg id="FarmersMarket">
                <p class=homeScrollerText>
                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
            </div>

            <div class=homeScrollerImg id="beekeeper">
                <p class=homeScrollerText>
                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
            </div>

    <!-- Register DIV -->
    <div class="userFormContainer">
        <div class="userForm" id="userRegistrationForm" hidden="true">
            <span id="createAccount">
                <fieldset about="Create Account" id="registerFieldSet"><legend>Create Account</legend>
                    <form method="post" class="userForm" id="registrationForm" name="registrationForm" action="php/registerUser.php">
                        <div>
                            <span class="userFormRow">
                                <label for="email">email</label>
                                <input type="email" name="email" id="email"
                                       placeholder="e.g. 'someone@com.com'" required="required"
                                       pattern="[a-zA-Z0-9._]+@[a-zA-Z0-9._]+.[a-zA-Z]+"
                                >
                            </span>

                        </div>
                        <div>
                            <span class="userFormRow">
                                <label for="userName">Username</label>
                                <input type="text" name="username" id="username"
                                       placeholder="e.g. 'Doofy' "
                                       required="required" pattern="[a-zA-Z0-9._]+"
                                >
                            </span>
                        </div>
                        <div>
                            <span class="userFormRow">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password"
                                       required="required" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})"
                                       title="Password Requirements: At least 1 lowercase letter, at least 1 uppercase letter,
                                              at least 1 number and at least 1 special character (!,@,#,$,%,^,&, or *"
                                >
                            </span>
                        </div>
                        <div>
                            <span class="userFormRow">
                                <label for="password_confirm">Re-type Password</label>
                                <input type="password" name="password_confirm" id="password_confirm"
                                       required="required"
                                >
                            </span>
                        </div>
                        <div class="userFormRow">
                            <fieldset id="accountType">
                                <legend>Account type</legend>
                                <input type="radio" name="usertype" value="grower">I'm a Gardener
                                <input type="radio" name="usertype" value="shopper">I'm just shopping
                            </fieldset>
                        </div>
                        <div class="userFormButtons">
                            <button type="button" class="generalButton" id="submitRegistration">Submit</button>
                            <button type="button" class="generalButton" id="cancelRegistration">Cancel</button>
                        </div>
                    </form>
            </fieldset>
        </span>
        </div>
    </div>

    <!-- Tell the user to check their email FIXME , bag this for now -->
    <!--
    <div class="userFormContainer">
        <div class="userForm"  id = "checkEmailPrompt" hidden="true">
            <span>
                <h3> Thank you for joining us!</h3>
                <h5> Please check your email to continue registering.</h5>
                <div class="userFormButtons">
                    <button type="button" class="generalButton"  id="checkEmailPromptButton">OK</button>
                </div>
            </span>
        </div>
    </div>
-->
    <!-- Bad Registration Attempt -->
    <div class="userFormContainer">
        <div class="userForm"  id = "invalidRegister" hidden="true">
            <span>
                <h3> </h3>
                <div class="userFormButtons">
                    <button type="button" class="generalButton" id="invalidRegisterButton">OK</button>
                </div>
            </span>
        </div>
    </div>

    <!-- Bad Registration Attempt -->
    <div class="userFormContainer">
        <div class="userForm"  id = "goodReg" hidden="true">
            <span>
                <h3>Registration Successful!</h3>
                <h3>Login to continue.</h3>
            </span>
        </div>
    </div>

    <!-- Select which session type -->
    <div class="userFormContainer">
        <div class="userForm"  id = "selectSessionType" hidden="true">
            <span>
                <h3> Please select your session type: </h3>

                <div class="userFormButtons">
                    <button type="button" class="generalButton" id="choseGrowerSession">Gardener</button>
                    <button type="button" class="generalButton" id="choseShopperSession">Shopper</button>
                </div>
            </span>
        </div>
    </div>

    <!-- Login DIV -->
    <div class="userFormContainer">
        <div class="userForm" id="userLoginForm" hidden="true">
            <span id="accountLogin">
                <fieldset about="Log into your account" id="loginFieldSet"><legend>Account Login</legend>
                    <form  method="post"  id="loginForm" name="registrationForm" action="#">
                        <div>
                            <span class="userFormRow">
                                <label for="login">email</label>
                                <input type="text" name="login" id="login" placeholder="e.g. 'someone@com.com'"
                                <?php
                                    if(isset($_SESSION["login_preset"])){
                                ?>
                                        value="<?php echo $_SESSION["login_preset"]?>"
                               <?php
                                    }
                                ?>
                                >
                            </span>
                        </div>
                        <div>
                            <span class="userFormRow">
                                <label for="passwordLogin">Password</label>
                                <input type="password" name="passwordLogin" id="passwordLogin">
                            </span>
                        </div>
                        <div class="userFormButtons" id="submitLogin">
                            <button type="button" class="generalButton" id="submitLogin">Sign In</button>
                            <button type="button" class="generalButton" id="cancelLogin">Cancel</button>
                        </div>
                    </form>
                </fieldset>
                    <span hidden="true" id="invalidLogin">
                        <h3>Invalid Credentials Entered.</h3>
                    </span>
            </span>
        </div>
    </div>

            <footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>

    </body>
</html>