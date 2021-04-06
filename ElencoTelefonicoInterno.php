<!DOCTYPE html>
<html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="custom_fonts.css" />
<link rel="stylesheet" type="text/css" href="styles.css" />

<script src="jQuery-3.3.1/jquery-3.3.1.min.js" type="text/javascript"></script>
<script src="script.js" type="text/javascript"></script>
<title>ATER di Udine - Elenco Telefonico Interno</title>

<body lang=IT>

<?php
require_once(__DIR__.'/ater-phplibs/ater_ldap.php');
require_once(__DIR__.'/ater-phplibs/ater_utils.php');

function print_error($error)
{
	echo $error;
}

function print_table_header_location($location)
{
	echo "<div class=\"column\">";
	echo "$location";
	echo "</div>";
}


class HTMLTable {
	public function __construct() {
		echo "<table class=\"directory\">";
		echo "<tr>";
		echo "<th>Nome</th><th class=\"initials\">Sigla</th><th class=\"phone\">Int.</th><th class=\"mobile\">Cell.</th><th class=\"mail\">E-mail</th><th class=\"department\">Ufficio</th>";
		echo "</tr>";
		echo "\n";
	}

	public function __destruct() {
		echo "</table>";
		echo "\n";
	}

	public function AddRow($name, $initials, $phone, $mobile, $mail, $pager, $department) {
		echo "<tr>";
		echo "<td class=\"name\">$name</td>";
		echo "<td class=\"initials\">$initials</td>";
		echo "<td class=\"phone\">$phone</td>";
		echo "<td class=\"mobile\">$mobile</td>";
		echo "<td class=\"mail\">$mail</td>";
		echo "<td class=\"department\">$department</td>";
		echo "</tr>";
		echo "\n";
	}
}


class HTMLDivElement {
	public function __construct() {
		echo "<div class=\"column\">";
		echo "\n";
	}
	public function __destruct() {
		echo "</div>";
		echo "\n";
	}
}


function add_mailto($string)
{
	$result = "";
	if ($string)
		$result="<a href='mailto:{$string}'>$string</a>";
	return $result;
}


echo "<div id=\"options\" class=\"noPrint\">";
echo "<label><input type=\"checkbox\" id=\"show_mail\" unchecked />E-Mail</label>\n";
echo "<label><input type=\"checkbox\" id=\"show_mobile\" unchecked>Cellulare</label>\n";
echo "<label><input type=\"checkbox\" id=\"show_department\" unchecked>Ufficio</label>\n";
echo "</div>";


$ldap = new \ATERUD\LDAP();
if ($ldap) {
	// Recupera gli utenti da LDAP
	
	$fields = array("cn", "givenName", "sn", "initials", "mail", "telephoneNumber", "pager",
                 "facsimileTelephoneNumber", "mobile", "department", "otherTelephone");
	// (userAccountControl:1.2.840.113556.1.4.803:=2 sono gli utenti disabilitati
	

	// Cambiarlo a 2 il 01/01/2020
	$maxIterazioni = 2;

	for ($iterazione = 0; $iterazione < $maxIterazioni; $iterazione++) {
		if ($iterazione == 0)
			$filter = "(&(objectClass=user)(telephoneNumber=*)(!(userAccountControl:1.2.840.113556.1.4.803:=2))(!(physicalDeliveryOfficeName=Tolmezzo)))";
		else
			$filter = "(&(objectClass=user)(telephoneNumber=*)(!(userAccountControl:1.2.840.113556.1.4.803:=2))(physicalDeliveryOfficeName=Tolmezzo))";
	
		$info = $ldap->GetUsers($filter, $fields, 'sn');

		$numEntries = $info["count"]; 
		$entriesPerColumn = ceil($numEntries / 3) ;
		if ($iterazione == 0)
			print_table_header_location("Udine");
		else
			print_table_header_location("Tolmezzo");
	
		$div = new HTMLDivElement();
			
		$table = new HTMLTable();
		for ($i = 0; $i < $numEntries; $i++) {
			$phone = $info[$i]["telephonenumber"][0];
			if ($phone != "") {
				$givenName=$info[$i]["givenname"][0];
				$sName=$info[$i]["sn"][0];
				$initials = "";
				if (!empty($info[$i]["initials"][0]))
					$initials=$info[$i]["initials"][0];
				$phoneNumber=$info[$i]["telephonenumber"][0];
				$mobile="";
				$department="";
				if (!empty($info[$i]["mobile"]))
					$mobile=$info[$i]["mobile"][0];
				$mobile=ater_format_telephone_number($mobile);
				$otherTelephone="";
				if (!empty($info[$i]["othertelephone"]))
					$otherTelephone=$info[$i]["othertelephone"][0];
				$internalNumber=ater_get_internal_number($phoneNumber);
				if ($otherTelephone != "")
					$internalNumber = $internalNumber . "/" . ater_get_internal_number($otherTelephone);
				if (!empty($info[$i]["mail"]))
					$mail=add_mailto($info[$i]["mail"][0]);
				else
					$mail="";
				if (!empty($info[$i]["department"])) {
					$department=$info[$i]["department"][0];
				}
				$table->AddRow($sName . ' ' . $givenName, $initials, $internalNumber, $mobile, $mail, $internalNumber, $department);
		
				if ($i != 0 && (($i + 1) % $entriesPerColumn == 0) && ($i < $numEntries - 1)) {
					$table = null;
					$div = null;
					$div = new HTMLDivElement();
					$table = new HTMLTable();
				}
			}
		}
		$table = null;
		$div = null;
	}	

	// Recupera i "contatti" da LDAP
	$info = $ldap->GetUsers("(&(objectClass=contact)(telephoneNumber=*))", $fields, 'cn');
	$ldap = null;
	
	print_table_header_location("Altri");
	$div = new HTMLDivElement();

	$table = new HTMLTable();
	for ($i = 0; $i < $info["count"]; $i++) {
		if (!empty($info[$i]["sn"]))
			$sName=$info[$i]["sn"][0];
		if (!empty($info[$i]["telephonenumber"]))
			$telephoneNumber=ater_format_telephone_number($info[$i]["telephonenumber"][0]);
		if (!empty($info[$i]["pager"]))
			$pager=$info[$i]["pager"][0];
		else
			$pager=NULL;
		$phone = '';
		if ($pager)
			$phone=$pager;
		else
			$phone=$telephoneNumber;

		$table->AddRow($sName, ' ', $phone, '', '', '', '');
	}
	$table = null;
	$div = null;
	
	#echo "<div id=\"header\">ATER di Udine - Elenco Telefonico Interno</div>\n";	
	#echo "<div id=\"footer\">$numEntries voci, $entriesPerColumn per colonna</div>\n";
}
?>


</body>
</html>
