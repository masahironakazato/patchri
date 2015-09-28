<html>
<head><title>patchriconfig.html</title></head>
<body>

<?php
session_start();
$username = $_SESSION["username"];

if  ($username != NULL){


$URL = $_SERVER["QUERY_STRING"];
list( , $hostid, $ifIndexid ,$ifDescr ,$ifdatavlanid ,$ifvoicevlanid ) = explode( ":", $URL);

$host = "$hostid";
$community = "snmpwritepass";
$hostdir = '/var/www/html/hostdir';

define('VLANDB_W','.1.3.6.1.2.1.47.1.2.1.1.2');

define('IFDESCR_G','.1.3.6.1.2.1.31.1.1.1.18.');
define('IFDATAVLAN_G','.1.3.6.1.4.1.9.9.68.1.2.2.1.2.');
define('IFVOICEVLAN_G','.1.3.6.1.4.1.9.9.68.1.5.1.1.1.');

snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
$ifdescriptionid = snmpget("$host","$community",IFDESCR_G."$ifIndexid");
$vlandb = snmpwalk("$host","$community",VLANDB_W);

////debug
///$hoge  = explode( ":", $URL);

$txt_def = array("host_id"=>"$hostid",
		"ifIndex_id"=>"$ifIndexid",
		"ifDescr_id"=>"$ifDescr",
		"ifdatavlan_id"=>"$ifdatavlanid",
		"ifvoicevlan_id"=>"$ifvoicevlanid",
		"ifdescription_id"=>"$ifdescriptionid");

///end of debug

require_once 'HTML/QuickForm.php';

$form = new HTML_QuickForm('myform');

$form->addElement('header','Header', '受け取った値を設定する' );

$form->addElement('hidden', 'host_id');
$form->addElement('hidden', 'ifIndex_id' );
$form->addElement('hidden', 'ifDescr_id' );

$form->addElement('text', 'ifdatavlan_id','DATAVLAN' );
$form->addElement('text', 'ifvoicevlan_id','VOICEVLAN' );
$form->addElement('text', 'ifdescription_id','DESCRIPTION' );

$form->addElement('submit', 'submitbutton', 'OK');

$form->addRule('ifdatavlan_id', 'plzinsertvalue', 'required');

$form->setDefaults($txt_def);


$hostida = $_POST["host_id"];
$ifIndexida = $_POST["ifIndex_id"];
$ifDescrida = $_POST["ifDescr_id"];
$ifdatavlanida = $_POST["ifdatavlan_id"];
$ifvoicevlanida = $_POST["ifvoicevlan_id"];
$ifdescriptionida = $_POST["ifdescription_id"];


$host_id = $hostida ;
$ifIndex_id = $ifIndexida ;
$ifDescr_id = $ifDescrida ;
$ifdatavlan_id = $ifdatavlanida ;
$ifvoicevlan_id = $ifvoicevlanida ;
$ifdescription_id = $ifdescriptionida ;

$showvlanlist = '1';

echo " <a href=patchripolling.php?action=host_id=$hostid$host_id target=\"right\">Back to the portlist</a><br><br>";


echo '<p>';
echo 'HOSTNAME : ' . "$host_id". "$hostid" . "<br>";
echo 'INTERFACE NAME :  ' . "$ifDescr_id". " $ifDescr" . "<br>";
echo 'DATA VLAN :  ' . "$ifdatavlan_id". " $ifdatavlanid" . "<br>";
echo 'VOICE VLAN :  ' . "$ifvoicevlan_id". " $ifvoicevlanid" . "<br>";
echo '</p>';


if ($form->validate()){
	if ($form->getSubmitValue('status') == 'confirm'){
		echo "$host_id" . ' Configured DATAVLAN,VOICEVLAN,DESCRIPTION <br>';
		///configure data vlan
                 if ($ifdatavlan_id ==none){
                        $data_a = snmpset("$host_id", "$community",IFDATAVLAN_G."$ifIndex_id", "i", "1");
                        }else{
			$data_a = snmpset("$host_id", "$community",IFDATAVLAN_G."$ifIndex_id", "i", "$ifdatavlan_id");
			}
		///configure voice vlan
		if ($ifvoicevlan_id == none){
			$data_b = snmpset("$host_id", "$community",IFVOICEVLAN_G."$ifIndex_id", "i", "4096");
			}else{
			$data_b = snmpset("$host_id", "$community",IFVOICEVLAN_G."$ifIndex_id", "i", "$ifvoicevlan_id");
			}
		///configure description
		$data_c = snmpset("$host_id", "$community",IFDESCR_G."$ifIndex_id", "s", "$ifdescription_id");

		$showvlanlist = '2';

		///LOGGING
		$configdate = date('r');
		$logdata = "$configdate, hostname;$host_id, ifindexid;$ifIndex_id, datavlan;$ifdatavlan_id, voicevlan;$ifvoicevlanid, description;$ifdescription_id, changed_config";
		$savefile = "/var/www/html/tools/log.txt";
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
		echo $logdata;
		}else{
		$form->addElement('hidden', 'status', 'confirm');
		$form->freeze();

		$showvlanlist = '2';
		}
	}

if ($form->getSubmitValue('status') != 'confirm'){
	$form->display();
	}


if ($showvlanlist == '1'){
	print "<table border=1 bgcolor=#ffffff><br>";
	print "<td>AVALLABLE VLAN No =></td>";

	for ($j=0; $j<count($vlandb); $j++) {
		if ($vlandb[$j] != vlan1 &&
		$vlandb[$j] != vlan1002 &&
		$vlandb[$j] != vlan1003 &&
		$vlandb[$j] != vlan1004 &&
		$vlandb[$j] != vlan1005)
		{
	        $vlannum = subStr($vlandb[$j],4);
        	echo "<td>$vlannum</td>";
		}
		}
	print "</table>";
	}

}
?>

</body>
</html>
