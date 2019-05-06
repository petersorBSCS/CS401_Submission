<!doctype html>
<html>
<head>
    <link href="styles.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="icons/leaf.png">
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
    <a href="login.php">&nbsp;signup&nbsp;</a>&gt
</p>
<div>
    <h1>Login to your account</h1>
    <form action="php/userLogin.php" method="post">
        <ul>
            <li><input type="email" name="email"/>email</li>
            <li><input type="password" name="password"/>Password</li>
            <li><a href="forgot.html">Forgot username or password?</a></li>
            <li>
                <!--<a href="editProfile.html" class="btn-link">Login</a>-->
                <input type="submit" value="Login">
                <a href="index.php" class="btn-link">Cancel</a>
            </li>
        </ul>
    </form>
</div>
<footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>
</body>
</html>