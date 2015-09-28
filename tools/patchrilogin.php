<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<head><title>PATCH Ri  </title></head>

<style>

body {
filter: alpha(opacity=100);
-moz-opacity: 1;
opacity: 1;
background-image: url("patchri.jpg");
background-repeat: no-repeat;
background-position: 50% 50%;
background-color:rgb(255,255,240);
background-size:1000px;
background-attachment:fixed;
}
</style>

<?php
require_once "Auth.php";

function loginFormHtml($username = null, $status = null)
{
require_once('patchriloginform.php');
}
$params = array(
    'cryptType'=>'md5',
    'dsn' =>'mysql://sqluser:sqlpassword@127.0.0.1/cacti',
    'table' => 'user_auth',
    'usernamecol' => 'username',
    'passwordcol' => 'password'
	);

$auth = new Auth("DB", $params, "loginFormHtml");
$auth->start();
if ($auth->checkAuth()){
$display_name = $auth->getUsername();
	require('patchriindex.php');
	session_start();
	$_SESSION["username"] = "$display_name";}
?>
</html>
