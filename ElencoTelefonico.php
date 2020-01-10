<!DOCTYPE html>

<?php

require_once(__DIR__.'/ater-phplibs/ater_ldap.php');
require_once(__DIR__.'/ater-phplibs/ater_utils.php');

$ldapConnection = ater_get_ldap_connection();
if ($ldapConnection) {
	// leggi gli utenti

	// (userAccountControl:1.2.840.113556.1.4.803:=2 sono gli utenti disabilitati
	$fields = array("cn", "givenName", "sn", "initials", "mail", "telephoneNumber", "pager",
                 "facsimileTelephoneNumber", "mobile", "department", "physicalDeliveryOfficeName");
	$info = ater_get_ldap_users($ldapConnection,
		"(&(|(objectClass=user)(objectClass=contact))(telephoneNumber=*)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))", $fields);

	ater_sort_ldap_array($info, 'cn');
	ldap_unbind($ldapConnection);	
	
	$numEntries = $info["count"]; 
	$entriesPerColumn = ceil($numEntries / 3) ;
}
?>

<html>
<meta charset="utf-8"/> 
<head>
<link rel="stylesheet" type="text/css" href="datatables.min.css"/>

<script type="text/javascript" src="datatables.min.js"></script>
<script type="text/javascript" src="ater-jslibs/ater-format.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		var ADEntries = <?php echo json_encode($info); ?>;
		var entryCount = "<?php echo $info["count"]; ?>";
		var table = $('#dir').DataTable( {
			paging: false,
                dom: 'Bfrtip',
                stateSave: true,
                buttons: [
                        {
                                extend: 'print',
                                text: '<em>S</em>tampa',
                                exportOptions: {
                    columns: ':visible'
                },
                                customize: function ( win ) {
                        $(win.document.body)
                        .css( 'font-size', '10pt' );
                $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', '8px' );
                }
                        },
                        {
                                extend: 'colvis',
                                text: '<em>C</em>olonne',
                        }
                ],
    	} );

		for (i = 0; i < entryCount; i++) {
			try {
				var cn = ADEntries[i]['cn'][0];
				var givenName = ADEntries[i]['givenname'][0];
				var sn = ADEntries[i]['sn'][0];
				var manager = '';
				try {
					manager = ADEntries[i]['manager'][0];
				} catch (error) {
    	        }
				var jobTitle = '';
				try {
					jobTitle = ADEntries[i]['title'][0];
				} catch (error) {
				}
				var initials = ADEntries[i]['initials'][0];
				var phoneNumber = ater_format_telephone_number(ADEntries[i]['telephonenumber'][0]);
                var internalNumber = ater_get_internal_number(phoneNumber);
                var department = '';
				try {
					department = ADEntries[i]['department'][0];
				} catch (error) {
				}
				//if (!empty($info[$i]["department"])) {
                //	$department=$info[$i]["department"][0];
                //	$internalNumber=ater_get_internal_number($info[$i]["telephonenumber"][0]);
                //}
                var mobile = '';
				try {
					mobile = ater_format_telephone_number(ADEntries[i]['mobile'][0]);
				} catch (error) {
				}
                var mail = ADEntries[i]['mail'][0];
				if (mail) {
					mail = add_mailto(mail);	
				}
                var physicalDeliveryOfficeName = '';
				try {
					physicalDeliveryOfficeName = ADEntries[i]['physicaldeliveryofficename'][0];
				} catch (Error) {	
					physicalDeliveryOfficeName = 'Udine';
				}
				table.row.add([givenName, sn, internalNumber, phoneNumber, mobile, mail, initials, department, physicalDeliveryOfficeName ]).draw(false);
			} catch (error) {
				
			}
		}
	});
</script>
<title>ATER di Udine - Elenco Telefonico Interno</title>
</head>

<body lang=IT>
	<div id="table_div">
	<table id="dir">
	<thead>
		<tr>
			<th>Cognome</th><th>Nome</th><th class=\"phone\">Int.</th>
			<th class=\"phone\">Est.</th><th class=\"mobile\">Cell.</th>
			<th class=\"mail\">E-mail</th><th class=\"initials\">Iniziali</th>
			<th class=\"department\">Ufficio</th><th class=\"location\">Sede</th>
		</tr>
	</thead>
	</table>
</div>
</body>
</html>
