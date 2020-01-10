function add_mailto(mailString) {
	var result = "";
	if (mailString)
		result = "<a href='mailto:" + mailString + "'>" + mailString + "</a>";
	return result;
}

function ater_get_internal_number(number) {
	if (!number)
			return number;

        return number.substring(number.length - 3, number.length);
}
	
function ater_number_is_mobile(number) {
        var result = false;
        if (number.length == 10 && number[0] != '0')
                result = true;

        return result;
}

function ater_number_is_green(number) {
        var result = false;
        if (number.length == 9 && number.substring(0, 3) == '800')
                result = true;
        return result;
}

function ater_format_telephone_number(number) {
       if (!number)
            return '';

        // Normalizza la stringa che rappresenta il numero di telefono
        var result = number.replace(/[^\d]/g, '');

        if (ater_number_is_mobile(result) || ater_number_is_green(result))
                result = result.substring(0, 3) + '-' + result.substring(3, 6) + '-' + result.substring(6, result.length);
        else if (result.length > 3)
                result = result.substring(0, 4) + '-' + result.substring(4, result.length);
        return result;
}

