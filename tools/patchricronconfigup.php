<?php
$community = "snmpreadpass";
$tftpserver ="serverIPv4address";
$configuptime = date('YmdGis');
$hostdir = '/var/www/html/hostdir';

define('SETSTAT9449','.1.3.6.1.4.1.9.9.96.1.1.1.1.14.9449');
define('SETSTAT7506','.1.3.6.1.4.1.9.9.96.1.1.1.1.14.7506');
define('HOSTDIR','/var/www/html/hostdir');

if ($hostdir = opendir("$hostdir")){
	while (false !== ($hostlist =readdir($hostdir))){
		if($hostlist !="." && $hostlist !=".."){

		$erase_9449 = snmpset("$hostlist", "$community", SETSTAT9449, 'i', '6');
		$erase_7506 = snmpset("$hostlist", "$community", SETSTAT7506, 'i', '6');

		///wait
		sleep(1);
		$results_1 = exec("snmpset -On -v 2c -c"." $community". " $hostlist". " .1.3.6.1.4.1.9.9.96.1.1.1.1.3.9449 integer 4 .1.3.6.1.4.1.9.9.96.1.1.1.1.4.9449 integer 3 .1.3.6.1.4.1.9.9.96.1.1.1.1.14.9449 integer 4", $output );

		///wait
		sleep(1);
		$results_2 = exec("snmpset -v 1 -c"." $community". " $hostlist". " .1.3.6.1.4.1.9.9.96.1.1.1.1.2.7506 integer 1 .1.3.6.1.4.1.9.9.96.1.1.1.1.3.7506 integer 3 .1.3.6.1.4.1.9.9.96.1.1.1.1.4.7506 integer 1 .1.3.6.1.4.1.9.9.96.1.1.1.1.5.7506 a ". "$tftpserver"." .1.3.6.1.4.1.9.9.96.1.1.1.1.6.7506 s ". "$hostlist/$hostlist"."-$configuptime"."_snmp.txt .1.3.6.1.4.1.9.9.96.1.1.1.1.14.7506 integer 4",$outputconfigup);

		echo "$hostlist upload sccess \n";
		///wait
		sleep(1);
		if($results_2 = TRUE){
			unlink(HOSTDIR."/$hostlist");
			}
		}
	}
}

///for debug
//print_r($output);
//echo "\n";
//print_r($outputconfigup);

closedir($hostdir);

?>
