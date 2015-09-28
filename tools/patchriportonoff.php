<html>
<head><title>patchriportonoff.html</title></head>
<body>

<?php
session_start();
$username = $_SESSION["username"];

if  ($username != NULL){

$URL = $_SERVER["QUERY_STRING"];
list( , $hostid, $ifIndexid ,$ifDescr ,$ifadmin ) = explode( ":", $URL);

$host = "$hostid";
$community = "snmpwritepass";
$hostdir = '/var/www/html/hostdir';

define('IFADMIN_G','.1.3.6.1.2.1.2.2.1.7.');
define('IFDESCR_G','.1.3.6.1.2.1.31.1.1.1.18.');

////debug
///$hoge  = explode( ":", $URL);
///for debug start
///var_dump($hoge). "<br>";
///end of debug

$txt_def = array("host_id"=>"$hostid",
		"ifIndex_id"=>"$ifIndexid",
		"ifDescr_id"=>"$ifDescr",
		"ifadmin_id"=>"$ifadmin");

require_once 'HTML/QuickForm.php';

$form = new HTML_QuickForm('myform');

///for debug
///$form->addElement('header','Header', '受け取った値を設定する' );

$form->addElement('hidden', 'host_id');
$form->addElement('hidden', 'ifIndex_id' );
$form->addElement('hidden', 'ifDescr_id' );


$fixArr = array("1"=>"UP",
                "2"=>"DOWN");
$form->addElement('select', 'ifadmin_id', 'PORT STATUS : ', $fixArr);

$form->addElement('submit', 'submitbutton', 'OK');

$form->addRule('ifadmin_id', 'plzinsertvalue', 'required');

$form->setDefaults($txt_def);


$hostida = $_POST["host_id"];
$ifIndexida = $_POST["ifIndex_id"];
$ifDescrida = $_POST["ifDescr_id"];
$ifadminida = $_POST["ifadmin_id"];


$host_id = $hostida ;
$ifIndex_id = $ifIndexida ;
$ifDescr_id = $ifDescrida ;
$ifadmin_id = $ifadminida ;

echo " <a href=patchripolling.php?action=host_id=$hostid$host_id target=\"right\">Back to the portlist</a><br><br>";

echo '<p>';
echo 'HOSTNAME : ' . "$host_id". "$hostid" . "<br>";
echo 'INTERFACE NAME :  ' . "$ifDescr_id". " $ifDescr" . "<br>";
echo '</p>';


if ($form->validate()){
	if ($form->getSubmitValue('status') == 'confirm')
		{
		///configure port on off
		$data_a = snmpset("$host_id", "$community",IFADMIN_G."$ifIndex_id", "i", "$ifadmin_id");
		///show port status
		$data_d = snmpget("$host_id", "$community",IFADMIN_G."$ifIndex_id");
                ///LOGGING
                $configdate = date('r');
                $logdata = "$configdate, hostname;$host_id, ifindexid;$ifIndex_id, ifDescrid;$ifDescr_id, ifadminid;$ifadmin_id";
                $savefile = '/var/www/html/tools/log.txt';
                ///configup
                $configuphost = "$hostdir/$host_id";
                ///logging file
                if (file_exists($savefile)) {
                         $fp = fopen("$savefile", 'a+');
                         fwrite($fp, "$logdata \n");
                         }
                ///config up
                if ( !file_exists($configuphost)){
                        touch($configuphost );
                        }
        	///describe port up or down
         	if ($data_d == "1" ){
		 	echo  "PORT IS UP";
		 	}else{
		 	echo  "PORT IS DOWN";
		 	}
		}else{
		$form->addElement('hidden', 'status', 'confirm');
		$form->freeze();
		}
	}

if ($form->getSubmitValue('status') != 'confirm'){
	$form->display();
	}
}
?>

</body>
</html>
