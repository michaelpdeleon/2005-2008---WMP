// $Id: check_zipcode.js,v 1.3 2006/03/09 11:24:01 max Exp $

// check_zip_code_rules is defined in check_zipcode_js.tpl

function check_zip_code_field(cnt, zip) {
	var c_code;
	var zip_error = false;

	if (!zip || zip.value == "")
		return true;

	c_code = cnt ? cnt.options[cnt.selectedIndex].value : config_default_country;

	if (check_zip_code_rules[c_code] != undefined) {
		var rules = check_zip_code_rules[c_code];

		if (rules.lens != undefined	&& rules.lens[zip.value.length] == undefined)
			zip_error = true;

		if (rules.re != undefined && zip.value.search(rules.re) != -1)
			zip_error = true;

		if (zip_error) {
			if (rules.error && rules.error.length > 0)
				alert(rules.error);
			zip.focus();
			return false;
		}
	}

	return !zip_error;
}

function check_zip_code() {
	return check_zip_code_field(document.forms["registerform"].b_country, document.forms["registerform"].b_zipcode) && check_zip_code_field(document.forms["registerform"].s_country, document.forms["registerform"].s_zipcode); 
}

