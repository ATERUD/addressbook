<?php


// Apre un bind ldap.
// Per rilasciare il bind utilizzare ldap_unbind()
function ater_get_ldap_connection()
{
        $ldapHost = getenv("HTTP_ATERUD_LDAP_HOST");
        $ldapPort = intval(getenv("HTTP_ATERUD_LDAP_PORT"));
	$ldapUser = getenv("HTTP_ATERUD_LDAP_USER");
	$ldapPass = getenv("HTTP_ATERUD_LDAP_PASS");

	$ldapConnection = ldap_connect($ldapHost, $ldapPort) or die("Could not connect to $ldapHost");

	// binding to ldap server
	ldap_bind($ldapConnection, $ldapUser, $ldapPass) or die(ldap_error($bind));

        return $ldapConnection;
}


// Richiede un bind ldap
function ater_get_ldap_users($bind, $filter, $fields)
{
        $basedn = getenv("HTTP_ATERUD_LDAP_BASEDN");
	$sr = ldap_search($bind, $basedn, $filter, $fields);
        $info = ldap_get_entries($bind, $sr);

	// Converti le stringhe in UTF8
        array_walk_recursive($info,
            function (&$entry) {
                $entry = iconv('Windows-1252', 'UTF-8', $entry);
            }
        );

        return $info;
}


function ater_sort_ldap_array(&$list, $sortKey)
{
        // metti le intestazioni di colonna in un array, per usarle nella
	// chiamata array_multisort
	foreach ($list as $key => $row) {
		//if ($row != '') {
		$name[$key] = $row[$sortKey];
		//}
        }
        if (!array_multisort($name, SORT_REGULAR, $list)) {
                echo "Cannot sort array!";
        };
}

?>

