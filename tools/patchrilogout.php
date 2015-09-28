 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php

require_once("Auth/Auth.php");

$auth = new Auth("DB");

if ($auth->getAuth()) {
    $auth->logout();
//		print("<p>Logout Complete<br />
		print("
		<a href=\"patchrilogin.php\"  target=_top>Back to login form</a></p>\n");
	}else{
		print("<p>Please Close browser <br />
		<a href=\"patchrilogin.php\"  target=_top>Back to login form</a></p>\n");
	}

?>
