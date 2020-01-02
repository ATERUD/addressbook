<?php

function ater_number_is_mobile($number)
{
	$result = false;
	if (strlen($number) == 10 && $number[0] != '0')
		$result = true;
	
	return $result;
}

function ater_number_is_green($number)
{
	$result = false;
	if (strlen($number) == 9 && substr($number, 0, 3) == '800')
		$result = true;
	return $result;
}


function ater_get_internal_number($number)
{
	if ($number == '')
		return $number;

	return substr($number,-3, 3);
}


function ater_format_telephone_number($number)
{
	if ($number == '')
		return $number;

	// Normalizza la stringa che rappresenta il numero di telefono
	$number= str_replace(array(' ','-'), '', $number);

	if (ater_number_is_mobile($number) || ater_number_is_green($number))
		$number = substr($number, 0, 3)."-".substr($number, 3, 3)."-".substr($number, 6);
	else if (strlen($number) > 3)
		$number = substr($number, 0, 4)."-".substr($number, 4);
	return $number;
}

?>
