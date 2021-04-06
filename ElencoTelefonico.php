<!DOCTYPE html>

<?php

require_once(__DIR__.'/ater-phplibs/ater_ldap.php');
require_once(__DIR__.'/ater-phplibs/ater_utils.php');

$ldap = new ATERUD\LDAP();
if ($ldap) {
	// Recupera gli utenti da LDAP
	// (userAccountControl:1.2.840.113556.1.4.803:=2 sono gli utenti disabilitati
	$fields = array("cn", "givenName", "sn", "initials", "mail", "telephoneNumber", "pager",
                 "facsimileTelephoneNumber", "mobile", "department", "physicalDeliveryOfficeName");
	$info = $ldap->GetUsers(
		"(&(|(objectClass=user)(objectClass=contact))(telephoneNumber=*)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))", $fields);
	
	$ldap = null;
}
?>

<html>
<meta charset="utf-8"/> 
<head>
<link rel="stylesheet" type="text/css" href="datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="custom_fonts.css"/>
<link rel="stylesheet" type="text/css" href="custom_styles.css"/>

<script type="text/javascript">
	var gADEntries = <?php global $info; echo json_encode($info); ?>;
	var gADEntryCount = '<?php global $info; echo $info["count"]; ?>';
</script>
<script type="text/javascript" src="datatables.min.js"></script>
<script type="text/javascript" src="ater-jslibs/ater-format.js"></script>
<script type="text/javascript" src="onload.js"></script>


<title>ATER di Udine - Elenco Telefonico Interno</title>
</head>

<body lang=IT>
	<div id="container">
	<table id="dir" class="display" style="width:100%">
	<thead>
		<tr>
			<th>Cognome</th>
			<th>Nome</th>
			<th class=\"phone\">Int.</th>
			<th class=\"phone\">Est.</th>
			<th class=\"mobile\">Cell.</th>
			<th class=\"mail\">E-mail</th>
			<th class=\"initials\">Iniziali</th>
			<th class=\"department\">Ufficio</th>
			<th class=\"location\">Sede</th>
		</tr>
	</thead>
	</table>
</div>
</body>
</html>
