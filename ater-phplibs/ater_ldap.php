<?php

namespace ATERUD;

class LDAP {
	
	private $connection;

	// Apre un bind ldap.
	// Per rilasciare il bind utilizzare ldap_unbind()
	public function __construct() {
	        $ldapHost = getenv("HTTP_ATERUD_LDAP_HOST");
	        $ldapPort = intval(getenv("HTTP_ATERUD_LDAP_PORT"));
		$ldapUser = getenv("HTTP_ATERUD_LDAP_USER");
		$ldapPass = getenv("HTTP_ATERUD_LDAP_PASS");

		$this->connection = ldap_connect($ldapHost, $ldapPort) or die("Could not connect to $ldapHost");

		// binding to ldap server
		ldap_bind($this->connection, $ldapUser, $ldapPass) or die(ldap_error($this->connection));
	}
	

	public function __destruct() {
		ldap_unbind($this->connection);	
	}

	// Richiede un bind ldap
	public function GetUsers($filter, $fields, $sortKey = "") {
	        $basedn = getenv("HTTP_ATERUD_LDAP_BASEDN");
		$sr = ldap_search($this->connection, $basedn, $filter, $fields);
	        $info = ldap_get_entries($this->connection, $sr);

		// Converti le stringhe in UTF8
	        array_walk_recursive($info,
	            function (&$entry) {
	                $entry = iconv('Windows-1252', 'UTF-8', $entry);
	            }
	        );

		if ($sortKey != "")
		    $this->_SortArray($info, $sortKey);
		
	        return $info;
	}


	private function _SortArray(&$list, $sortKey) {
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
}


?>

