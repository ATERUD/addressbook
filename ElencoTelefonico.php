<!DOCTYPE html>
<html>
<meta charset="utf-8"/>

<link rel="stylesheet" type="text/css" href="datatables.min.css"/>

<script type="text/javascript" src="datatables.min.js"></script>

<script type="text/javascript" charset="utf8" src="/script_table.js"></script>

<title>ATER di Udine - Elenco Telefonico Interno</title>

<body lang=IT>

<?php

require_once(__DIR__.'/ater-phplibs/ater_ldap.php');
require_once(__DIR__.'/ater-phplibs/ater_utils.php');


function emit_table_header()
{
	echo "<thead>";
	echo "<tr>";
	echo "<th>Cognome</th><th>Nome</th><th class=\"phone\">Int.</th>";
	echo "<th class=\"phone\">Est.</th><th class=\"mobile\">Cell.</th>";
	echo "<th class=\"mail\">E-mail</th><th class=\"department\">Ufficio</th><th class=\"location\">Sede</th>";
	echo "</tr>";
	echo "</thead>";
	echo "\n";
}

function emit_table_close()
{
	echo "</table>";
	echo "\n";
}

function emit_table_row($name, $cognome, $phoneInt, $phoneExt, $mobile, $mail, $pager, $department, $location)
{
	echo "<tr>";
	echo "<td class=\"cognome\">$cognome</td>";
	echo "<td>$name</td>";
	echo "<td class=\"phone\">$phoneInt</td>";
	echo "<td class=\"phone\">$phoneExt</td>";
	echo "<td class=\"mobile\">$mobile</td>";
	echo "<td class=\"mail\">$mail</td>";
	echo "<td class=\"department\">$department</td>";
	echo "<td class=\"location\">$location</td>";
	echo "</tr>";
	echo "\n";
}


function add_mailto($string)
{
	$result = "";
	if ($string)
		$result="<a href='mailto:{$string}'>$string</a>";
	return $result;
}


$ldapConnection = ater_get_ldap_connection();
if ($ldapConnection) {
	// leggi gli utenti

	// (userAccountControl:1.2.840.113556.1.4.803:=2 sono gli utenti disabilitati
	$fields = array("cn", "givenName", "sn", "mail", "telephoneNumber", "pager",
                 "facsimileTelephoneNumber", "mobile", "department", "physicalDeliveryOfficeName");
	$info = ater_get_ldap_users($ldapConnection,
		"(&(|(objectClass=user)(objectClass=contact))(telephoneNumber=*)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))", $fields);

	ater_sort_ldap_array($info, 'cn');
	ldap_unbind($ldapConnection);	
	
	$numEntries = $info["count"]; 
	$entriesPerColumn = ceil($numEntries / 3) ;
	echo "<table id=\"dir\" class=\"display compact\">";
	
	emit_table_header();
	echo "<tbody>";
	for ($i = 0; $i < $numEntries; $i++) {
		$phone = $info[$i]["telephonenumber"][0];
		if ($phone != "") {
			$givenName=$info[$i]["givenname"][0];
			$sName=$info[$i]["sn"][0];
			$phoneNumber = ater_format_telephone_number($info[$i]["telephonenumber"][0]);
			$mobile="";
			$department="";
			$internalNumber='';
			if (!empty($info[$i]["department"])) {
				$department=$info[$i]["department"][0];
				$internalNumber=ater_get_internal_number($info[$i]["telephonenumber"][0]);
			}
			if (!empty($info[$i]["mobile"]))
				$mobile=ater_format_telephone_number($info[$i]["mobile"][0]);
			if (!empty($info[$i]["mail"]))
				$mail=add_mailto($info[$i]["mail"][0]);
			else
				$mail="";
			$physicalDeliveryOfficeName = "Udine";
			if (!empty($info[$i]["physicaldeliveryofficename"][0]))
				$physicalDeliveryOfficeName = $info[$i]["physicaldeliveryofficename"][0];
			
    		emit_table_row($givenName, $sName, $internalNumber, $phoneNumber, $mobile, $mail, $internalNumber, $department, $physicalDeliveryOfficeName);
		}
	}
	echo "</tbody>";
	emit_table_close();
}

?>


</body>
</html>
