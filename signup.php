<!doctype html>
<html>
<head>
    <link href="styles.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="icons/leaf.png">

    <!-- Load JQuery and the forms javascript -->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>-->
    <script src="scripts/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script src="scripts/forms.js" type="text/javascript"></script>
    <title>signup</title>
</head>

<body>

<?php
    require_once('Banner.php');
    $banner = new \Banner\Banner();
    $banner->print_banner(false);
?>


<p>
    <a href="index.php">&nbsp;home&nbsp;</a>&gt
    <a href="signup.php">&nbsp;signup&nbsp;</a>&gt
</p>

<button type="button" id="showRegistrationForm">Signup</button>
<button type="button" id="showLoginForm">Login</button>

    <div class="userFormContainer">
        <div class="userForm" id="registrationForm" hidden="true">
            <span>
                <fieldset about="Create Account" id="registerFieldSet"><legend>Create Account</legend>
                    <form class="userForm" method="post" id="registrationForm" name="registrationForm">
                        <div>
                            <span class="userFormRow">
                                <label for="email">email</label>
                                <input type="email" name="email" id="email" onfocus="this.value=''"
                                       value="e.g. 'someone@com.com'" required="required"
                                       pattern="[a-zA-Z0-9._]+@[a-zA-Z0-9._]+.[a-zA-Z]+"
                                >
                            </span>

                        </div>
                        <div>
                            <span class="userFormRow">
                                <label for="userName">Username</label>
                                <input type="text" name="username" id="userName"
                                       onfocus="this.value=''" value="e.g. 'Doofy' "
                                       required="required" pattern="[a-zA-Z0-9._]+"
                                >
                            </span>
                        </div>
                        <div>
                            <span class="userFormRow">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" value=""
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
                            <button type="button" id="submitRegistration">Submit</button>
                            <button type="button" id="cancelRegistration">Cancel</button>
                        </div>
                    </form>
            </fieldset>
        </span>
    </div>
    </div>

    <div class="userFormContainer">
        <div class="userForm" id="userLoginForm" hidden="true">
            <span>
                <fieldset about="Log into your account" id="loginFieldSet"><legend>Account Login</legend>
                    <form method="post" id="loginForm" name="registrationForm">
                        <div>
                            <span class="userFormRow">
                                <label for="login">email</label>
                                <input type="text" name="login" id="login" onfocus="this.value=''" value="e.g. 'someone@com.com'">
                            </span>
                        </div>
                        <div>
                            <span class="userFormRow">
                                <label for="passwordLogin">Username</label>
                                <input type="password" name="passwordLogin" id="passwordLogin" onfocus="this.value=''">
                            </span>
                        </div>
                        <div class="userFormButtons" id="submitLogin">
                            <button type="button" id="submitLogin">Sign In</button>
                            <button type="button" id="cancelLogin">Cancel</button>
                        </div>
                    </form>
                </fieldset>
            </span>
        </div>
    </div>

<footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>
</body>
</html>