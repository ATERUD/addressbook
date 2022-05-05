<?php

namespace ATERUD;

class User {
	public $phone;
	public $givenname;
	public $surname;
	public $initials;
	public $mobile;
	public $department;
	public $otherphone;
	public $mail;
	public $internalnumber;
	public $pager;
	public function __construct() {
	
	}

	public function InternalNumber() {
	
	}
}


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

	public function GetUsersExt($filter, $fields, $sortKey = "") {
		$rawList = $this->GetUsers($filter, $fields, $sortKey);
		$usersList = Array();
		for ($i = 0; $i < $rawList["count"]; $i++) {
			$user = new User;
			$user->phone=$rawList[$i]["telephonenumber"][0];
			if (!empty($rawList[$i]["givenname"][0]))
				$user->givenname=$rawList[$i]["givenname"][0];
			if (!empty($rawList[$i]["sn"][0]))
				$user->surname=$rawList[$i]["sn"][0];
			if (!empty($rawList[$i]["pager"][0]))
				$user->pager=$rawList[$i]["pager"][0];
			if (!empty($rawList[$i]["initials"][0]))
				$user->initials=$rawList[$i]["initials"][0];
			if (!empty($rawList[$i]["mobile"]))
				$user->mobile=$rawList[$i]["mobile"][0];
			$user->mobile=ater_format_telephone_number($user->mobile);
			if (!empty($rawList[$i]["othertelephone"]))
				$user->otherphone=$rawList[$i]["othertelephone"][0];
			$user->internalnumber=ater_get_internal_number($user->phone);
			if ($user->otherphone != "")
				$user->internalnumber = $user->internalnumber . "/" . ater_get_internal_number($user->otherphone);
			if (!empty($rawList[$i]["mail"]))
				$user->mail=add_mailto($rawList[$i]["mail"][0]);
			if (!empty($rawList[$i]["department"])) {
				$user->department=$rawList[$i]["department"][0];
			}
			array_push($usersList, $user);
		}
		return $usersList;
	}

	private function _SortArray(&$list, $sortKey) {
	        // metti le intestazioni di colonna in un array, per usarle nella
		// chiamata array_multisort
		foreach ($list as $key => $row) {
			if (is_array($row) && array_key_exists($sortKey, $row))
				$name[$key] = $row[$sortKey];
			else
				$name[$key] = '';
	        }
	        if (!array_multisort($name, SORT_REGULAR, $list)) {
	                echo "Cannot sort array!";
	        }
	}
}


?>

