<!DOCTYPE html>
<html lang="de">
<head>
    <title>Login</title>
</head>
<body>
<form method="post" action="RegisterPage.php" id="register">
    <label for="fUsername">Username:</label><br>
    <input id="fUsername" name = "fUsername" type="text" placeholder="Username"><br>

    <label for="fPassword">Password:</label><br>
    <input id="fPassword" name = "fPassword" type="password" placeholder="Password"><br>

    <label for="fEmail">E-Mail:</label><br>
    <input id="fEmail" name = "fEmail" type="text" placeholder="E-Mail"><br>

    <input type="submit" value="Register" id="registerButton">
</form>

</body>

</html>

<?php
include("SignIn.php");

$username = $_POST["fUsername"];
$password = $_POST["fPassword"];
$email = $_POST["fEmail"];

register($username, $password, $email);