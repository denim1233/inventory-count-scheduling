<?php
 echo 'BRCRYT encryption ' .password_hash("password1ahaha", PASSWORD_BCRYPT);
 echo "<br>";

$input_password = 'Password';
echo 'sha256 with salt encryption ' .hash('sha256', $input_password. $salt);


 echo "<br>";

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// $_SERVER['HTTP_REFERER']
 echo "<br>";

echo parse_url($actual_link, PHP_URL_HOST);
 echo "<br>";
//echo parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)
session_start();
echo $_SESSION['A31axwebtokenLo0192s'];
echo "new";
echo "tae";
?>