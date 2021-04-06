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
	
		$info = $ldap->GetUsersExt($filter, $fields, 'sn');

		$numEntries = count($info); 
		$entriesPerColumn = ceil($numEntries / 3) ;
		if ($iterazione == 0)
			print_table_header_location("Udine");
		else
			print_table_header_location("Tolmezzo");
	
		$div = new HTMLDivElement();
			
		$table = new HTMLTable();
		for ($i = 0; $i < $numEntries; $i++) {
			$user = $info[$i];
			$table->AddRow($user->surname . ' ' . $user->givenname, $user->initials,
				$user->internalnumber, $user->mobile, $user->mail, $user->internalnumber, $user->department);
		
			if ($i != 0 && (($i + 1) % $entriesPerColumn == 0) && ($i < $numEntries - 1)) {
				$table = null;
				$div = null;
				$div = new HTMLDivElement();
				$table = new HTMLTable();
			}
		}
		$table = null;
		$div = null;
	}	
	$info = null;

	// Recupera i "contatti" da LDAP
	$info = $ldap->GetUsersExt("(&(objectClass=contact)(telephoneNumber=*))", $fields, 'cn');
	$ldap = null;
	
	print_table_header_location("Altri");
	$div = new HTMLDivElement();

	$table = new HTMLTable();
	for ($i = 0; $i < count($info); $i++) {
		$user = $info[$i];
		$phone = '';
		if ($user->pager != '')
			$phone=$user->pager;
		else
			$phone=$user->phone;

		$table->AddRow($user->surname, ' ', $phone, '', '', '', '');
	}
	$table = null;
	$div = null;
	
	#echo "<div id=\"header\">ATER di Udine - Elenco Telefonico Interno</div>\n";	
	#echo "<div id=\"footer\">$numEntries voci, $entriesPerColumn per colonna</div>\n";
}
?>


</body>
</html>
