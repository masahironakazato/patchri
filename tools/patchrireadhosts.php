<html><head>
<body bgcolor="#FFFFFF">
<p>
<a href=patchrilogout.php  target=_top\">//// LOGOUT ////</a><br/>
<br>
//// SWITCH LIST ////

</p>

<?php
session_start();
$username = $_SESSION["username"];

if  ($username != NULL){


exec("awk '{print $2}' /etc/hosts | grep -E 'PSW|ASW|gw|GW'| grep -vE 'console|vpn|border|ocn|internal|aoyama|vip' | sort -r", $array_c );
$number = count($array_c);

echo '//// SWITCH IS ' . $number .  " //// <br>";
echo '<br>';

for ($i=0; $i<count($array_c); $i++)
	{
	echo '<html>';
        echo '<body>';
        echo "<a href=\"patchripolling.php?action=host_id=$array_c[$i] \"target=\"right\"\>$array_c[$i] </a><br>";

/// debug
///        echo "<a href=\"urlget.php?action=host_id=$showhost\">$showhost</a>  <br>";
        echo '</body>';
        echo '</html>';
	}

}
?>

</body>
</html>
