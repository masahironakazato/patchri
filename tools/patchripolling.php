PATCH Reassign interpriter (PATCH ri):
<br><br>


<?php
session_start();
$username = $_SESSION["username"];

if  ($username != NULL){

include "header.html";

$URL = $_SERVER["QUERY_STRING"];
list( , , $hostid) = explode( "=", $URL);

$host = "$hostid";
$community = "snmpreadpass";


define('IFINDEX_W','.1.3.6.1.2.1.2.2.1.1');
define('IFNAME_W','.1.3.6.1.2.1.2.2.1.2');
define('IFDESCR_W','.1.3.6.1.2.1.31.1.1.1.18');
define('VLANDB_W','.1.3.6.1.2.1.47.1.2.1.1.2');
define('IFADMIN_W','.1.3.6.1.2.1.2.2.1.7');
define('IFSTATUS_W','.1.3.6.1.2.1.2.2.1.8');
define('IFLASTC_W','.1.3.6.1.2.1.2.2.1.9'); //last configured date

define('HOSTNAME_G','1.3.6.1.2.1.1.1.0');
define('UPTIME_G','.1.3.6.1.2.1.1.3.0');
define('SYSTEMINFO_G','.1.3.6.1.2.1.1.1.0');

define('IFDATAVLAN_G','.1.3.6.1.4.1.9.9.68.1.2.2.1.2.');
define('IFVOICEVLAN_G','.1.3.6.1.4.1.9.9.68.1.5.1.1.1.');

///snmp-type
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
$ifIndex = snmpwalk("$host","$community",IFINDEX_W);
$ifName = snmpwalk("$host","$community",IFNAME_W);
$ifdescription = snmpwalk("$host","$community",IFDESCR_W);
$vlandb = snmpwalk("$host","$community",VLANDB_W);
$ifOperStatus = snmpwalk("$host","$community",IFSTATUS_W);
$ifAdminStatus = snmpwalk("$host","$community",IFADMIN_W);
///snmp-type
snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
$uptime = snmpget("$host","$community",UPTIME_G);
$sysDescr = snmpget("$host","$community",SYSTEMINFO_G);
$ifLastChange = snmpwalk("$host","$community",IFLASTC_W);

if ($host !=NULL){
print "<table border=1 bgcolor=#ffffff><tr><td>$host</td></tr></table><br>";
print "<table border=1 bgcolor=#ffffff><tr><td>$uptime</td></tr></table><br>";
print "<table border=1 bgcolor=#ffffff><tr><td>$sysDescr</td></tr></table><br>";
print "<table border=1 bgcolor=#ffffff>";
for ($j=0; $j<count($vlandb); $j++) {
	if ($vlandb[$j] != vlan1 &&
	$vlandb[$j] != vlan1002 &&
	$vlandb[$j] != vlan1003 &&
	$vlandb[$j] != vlan1004 &&
	$vlandb[$j] != vlan1005){
	$vlannum = subStr($vlandb[$j],4);
	echo "<td>$vlannum</td>";
		}
	}

print "</table>";

print "<table border=1 bgcolor=#ffffff><br>";

print "<tr>
        <td>ifIndex</td>
        <td>ifName</td>
        <td>ifAdminStatus</td>
        <td>ifOperStatus</td>
        <td>ifLastChange</td>
        <td>ifdatavlan</td>
        <td>ifvoicevlan</td>
        <td>ifdescription</td>
        </tr>";


for ($i=0; $i<count($ifIndex); $i++) {
	$vlanintid = $ifIndex[$i] ;
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$ifdatavlan = snmpget("$host","$community",IFDATAVLAN_G."$vlanintid");
         if ($ifdatavlan == 1 ){
                 $ifdatavlan = "none";
                 }
	$ifvoicevlan = snmpget("$host","$community",IFVOICEVLAN_G."$vlanintid");
	if ($ifvoicevlan == 4096 ){
		$ifvoicevlan = "none";
		}
	$action_id = "action-id
		:$host
		:$ifIndex[$i]
		:$ifName[$i]
		:$ifdatavlan
		:$ifvoicevlan
		:$ifdescription[$i] \">$ifIndex[$i]";

	//up or down
	if ($ifAdminStatus[$i] == "1" )	{
		$IFADMIN = "UP";
		}else{
		$IFADMIN = "DOWN";
		}
	//up or down
        if ($ifOperStatus[$i] == "1" ) {
                $IFSTATUS = "UP";
        	}else{
                $IFSTATUS = "DOWN";
        	}

        print "<tr>";
	if ( $vlanintid > 9999 &&
	$vlanintid != 12001 &&
	$vlanintid != 12002 &&
	$vlanintid != 10501){
		if ( $ifdatavlan != NULL ){
			print "<td><a href=\"patchriconfig.php?action-id
			:$host
			:$ifIndex[$i]
			:$ifName[$i]
			:$ifdatavlan
			:$ifvoicevlan \">$ifIndex[$i]</a></td>";
			}else{
			print "<td>$ifIndex[$i]</td>";
			}
		print "<td>$ifName[$i]</td>";
                 if ( $ifdatavlan != NULL ){
			print "<td><a href=\"patchriportonoff.php?action-id
			:$host
			:$ifIndex[$i]
			:$ifName[$i]
			:$ifAdminStatus[$i] \">$IFADMIN</a></td>";
			}else{
			print "<td>$IFADMIN</td>";
			}
		print "<td>$IFSTATUS</td>";
		list( , ,$IFLASTC_A, $IFLASTC_B,$IFLASTC_C ) = explode( " ", $ifLastChange[$i]);
		print "<td>$IFLASTC_A$IFLASTC_B / $IFLASTC_C</td>";
                 if ( $ifdatavlan != NULL ){
			print "<td>$ifdatavlan</td>";
			}else{
			print "<td>TRUNK</td>";
			}
                 if ( $ifdatavlan != NULL ){
			print "<td>$ifvoicevlan</td>";
			}else{
			print "<td>TRUNK</td>";
			}
		print "<td>$ifdescription[$i]</td>";
		print "</tr>";
		}

	}
print "</table>";
}
}
?>
