/* $Id: change_states.js,v 1.7.2.1 2006/04/20 05:25:02 max Exp $ */

/*
	Initialization procedure
*/
function init_js_states(obj, state_name, county_name, state_value, county_value, force_run) {
	if (!obj || !obj.form.elements[state_name])
		return false;

	/* Get child objects	*/
	obj.states = obj.form.elements[state_name];
	obj.counties = obj.form.elements[county_name];

	obj.lastCode = false;
	obj.lastStateCode = false;
	obj.defaultStates = [];
	if (obj.counties)
		obj.defaultCounties = [];

	/* Get default values	*/
	var code = obj.options[obj.selectedIndex].value;
	if (countries[code] && countries[code].states && countries[code].states.length > 0) {
		for (var i in countries[code].states) {
			if (countries[code].states[i].code == state_value) {

				/* Save default state	*/
				obj.defaultStates[code] = i;
				if (obj.counties && countries[code].states[i].counties && countries[code].states[i].counties.length > 0) {
					for (var x in countries[code].states[i].counties) {
						if (x == county_value) {

							/* Save default county	*/
							obj.defaultCounties[i] = x;
							break;
						}
					}
				}
				break;
			}
		}
	}

	/* Save state and/or county full name	*/
	if (!obj.defaultStates[code]) {
		obj.defaultStateFull = state_value;
		obj.defaultCountyFull = county_value;
	} else if (obj.counties && !obj.defaultCounties[obj.defaultStates[code]]) {
		obj.defaultCountyFull = county_value;
	}

	obj.states.countries = obj;
	obj.statesName = state_name;
	if (obj.counties) {
		obj.counties.countries = obj;
		obj.countiesName = county_name;
	}

	/* Define handler for onchange events	*/
	if (obj.onchange)	
		obj.oldOnchange = obj.onchange;
	obj.onchange = change_states;

	if (obj.states.onchange)	
		obj.oldStatesOnchange = obj.states.onchange;
	obj.states.onchange = change_counties;

	if (obj.counties && obj.counties.onchange) {
		obj.oldCountiesOnchange = obj.counties.onchange;
	}

	/* Object settings	*/
	obj.statesInputSize = 32;
	obj.statesInputMaxLength = false;
	obj.statesSpanClass = "SmallText";
	obj.statesSpanStyle = false;
	obj.statesSelectClass = false;
	obj.statesSelectStyle = false;
	obj.statesInputClass = false;
	obj.statesInputStyle = false;
	obj.statesNoStates = window.txt_no_states ? txt_no_states : false;
	obj.countiesNoCounties = window.txt_no_counties ? txt_no_counties : false;

	if (force_run) {
		start_js_states(document.getElementById(obj.id));

	} else if (window.addEventListener) {
		window.addEventListener("load", new Function('', "start_js_states(document.getElementById('"+obj.id+"'))"), false);

	} else if (window.attachEvent) {
		window.attachEvent("onload", new Function('', "start_js_states(document.getElementById('"+obj.id+"'))"));

	} else {
		window.oldOnload = window.onload;
		window.onload = new Function('', "if (this.oldOnload) this.oldOnload(); start_js_states(document.getElementById('"+obj.id+"'))");
	}

	check_countries(obj);

	return obj;
}

/*
	Initial object run
*/
function start_js_states(obj) {
	if (obj && obj.onchange)
		obj.onchange();
}

/*
	Change states list
*/
function change_states() {
	var code = this.options[this.selectedIndex].value;

	/* Detect input box type and get default value	*/
	if (this.states.tagName.toUpperCase() == 'SPAN') {
		var type = false;

	} else if (this.states.tagName.toUpperCase() == 'SELECT') {
		var type = 'S';
		if (this.lastCode && countries[this.lastCode] && countries[this.lastCode].states && countries[this.lastCode].states.length > 0) {
			for (var i in countries[this.lastCode].states) {
				if (countries[this.lastCode].states[i].code == this.states.options[this.states.selectedIndex].value) {
					this.defaultStates[this.lastCode] = i;
					break;
				}
			}
		}

	} else if (this.states.tagName.toUpperCase() == 'INPUT') {
		var type = 'I';
		this.defaultStateFull = this.states.value;

	} else {
		return true;
	}

	if (countries[code].states === false) {
		/* If current country hasn't any states	*/
		if (type !== false) {
			this.states = tag_replace(this.states, "SPAN");
			if (this.statesNoStates)
				this.states.innerHTML = this.statesNoStates;
			if (this.statesSpanClass)
				this.states.className = this.statesSpanClass;
			this.states.countries = this;
			this.states.onchange = change_counties;
		}

	} else if (countries[code].states.length == 0) {
		/* If current country has empty states list	*/
		if (type != "I") {
			this.states = tag_replace(this.states, "INPUT", this.statesName);
			if (this.statesInputSize)
				this.states.size = this.statesInputSize;
			if (this.statesInputMaxLength)
				this.states.maxLength = this.statesInputMaxLength;
			if (this.statesInputClass)
				this.states.className = this.statesInputClass;
			this.states.countries = this;
			this.states.onchange = change_counties;
			this.states.oldOnchange = this.oldStatesOnchange;
		}
		if (this.defaultStateFull)
			this.states.value = this.defaultStateFull;

	} else if (countries[code].states.length > 0) {
		/* If current country has states list	*/
		if (type != "S") {
			this.states = tag_replace(this.states, "SELECT", this.statesName);
			if (this.statesSelectClass)
				this.states.className = this.statesSelectClass;
			this.states.countries = this;
			this.states.onchange = change_counties;
			this.states.oldOnchange = this.oldStatesOnchange;

		}

		/* States list cleaning	*/
		if (this.lastCode != code && type == 'S') {
			while (this.states.options.length > 0)
				this.states.options[0] = null;
		}

		if (type != 'S' || this.lastCode != code) {
			/* Fill states list	*/
			if (states_sort_override) {

				/* Sort	*/
				var tmp = [];
				for (var i in countries[code].states) {
					tmp[i] = countries[code].states[i];
					tmp[i].idx = i;
				}
				tmp.sort(sort_states);

				/* Fill list	*/
				for (var i in tmp) {
					if (!tmp[i])
						continue;
					this.states.options[this.states.options.length] = new Option(tmp[i].name, tmp[i].code);
					this.states.options[this.states.options.length-1].selected = (this.defaultStates[code] == tmp[i].idx);
					if (this.defaultStates[code] == tmp[i].idx)
						this.states.selectedIndex = this.states.options.length-1;
				}

				/* Set default state	*/
			} else {
				for (var i in countries[code].states) {
					this.states.options[this.states.options.length] = new Option(countries[code].states[i].name, countries[code].states[i].code);
					if (this.defaultStates[code] == i)
						this.states.options[this.states.options.length-1].selected = true;
				}
			}
			if (!this.defaultStates[code])
				this.states.options[0].selected = true;

		} else if (this.defaultStates[code] && countries[code].states[this.defaultStates[code]]) {
			/* Set default state	*/
			for (var i = 0; i < this.states.options.length; i++) {
				if (this.states.options[i].value == countries[code].states[this.defaultStates[code]].code) {
					this.states.options[i].selected = true;
					this.states.selectedIndex = i;
					break;
				}
			}		

		} else {
			this.states.options[0].selected = true;
			this.states.selectedIndex = 0;
		}
	}

	/* Call old onchange event handler	*/
	if (this.oldOnchange)
		this.oldOnchange();

	/* Call counties rebuild procedure	*/
	if (this.states.onchange)
		this.states.onchange();

	this.oldSelectedIndex = this.selectedIndex;

	this.lastCode = code;

}

/*
	Change counties list
*/
function change_counties() {
	if (!this.countries || !this.countries.counties)
		return true;

	var counties = this.countries.counties;
	var code = this.countries.options[this.countries.selectedIndex].value;
	var scode = false;
	if (this.options && countries[code].states && countries[code].states.length > 0) {
		for (var i in countries[code].states) {
			if (countries[code].states[i].code == this.options[this.selectedIndex].value) {
				scode = i;
				break;
			}
		}
	} else if (this.tagName.toUpperCase() == "INPUT") {
		scode = 0;
	}

	/* Detect input box type and get default value	*/
	if (counties.tagName.toUpperCase() == 'SPAN') {
		var type = false;

	} else if (counties.tagName.toUpperCase() == 'SELECT') {
		var type = 'S';
		if (this.countries.lastCode && this.countries.lastStateCode && countries[this.countries.lastCode] && countries[this.countries.lastCode].states && countries[this.countries.lastCode].states[this.countries.lastStateCode] && countries[this.countries.lastCode].states[this.countries.lastStateCode].counties && countries[this.countries.lastCode].states[this.countries.lastStateCode].counties.length > 0) {
			for (var i in countries[this.countries.lastCode].states[this.countries.lastStateCode].counties) {
				if (i == counties.options[counties.selectedIndex].value) {
					this.countries.defaultCounties[this.countries.lastStateCode] = i;
					break;
				}
			}
		}

	} else if (counties.tagName.toUpperCase() == 'INPUT') {
		var type = 'I';
		this.countries.defaultCountyFull = counties.value;

	} else {
		return true;
	}

	if (scode === false) {
		/* If current country hasn't any states	and counties	*/
		if (type !== false) {
			this.countries.counties = counties = tag_replace(counties, "SPAN");
			if (this.countries.countiesNoCounties)
				counties.innerHTML = this.countries.countiesNoCounties;
			if (this.countries.statesSpanClass)
				counties.className = this.countries.statesSpanClass;
		}

	} else if (!countries[code].states[scode] || countries[code].states[scode].counties.length == 0) {
		/* If current country hasn't states	or current state hasn;t counties list */
		if (type != "I") {
			this.countries.counties = counties = tag_replace(counties, "INPUT", this.countries.countiesName);
			if (this.countries.statesInputSize)
				counties.size = this.countries.statesInputSize;
			if (this.countries.statesInputMaxLength)
				counties.maxLength = this.countries.statesInputMaxLength;
			if (this.countries.statesInputClass)
				counties.className = this.countries.statesInputClass;
			if (this.countries.oldCountiesOnchange)
				counties.onchange = this.countries.oldCountiesOnchange;
		}
		if (this.countries.defaultCountyFull)
			counties.value = this.countries.defaultCountyFull;

	} else if (countries[code].states[scode].counties.length > 0) {
		/* If current state has counties list	*/
		if (type != "S") {
			this.countries.counties = counties = tag_replace(counties, "SELECT", this.countries.countiesName);
			if (this.countries.statesSelectClass)
				counties.className = this.countries.statesSelectClass;
			if (this.countries.oldCountiesOnchange)
				counties.onchange = this.countries.oldCountiesOnchange;
		}

		/* Clear old counties list	*/
		if (this.countries.lastStateCode != scode && type == 'S') {
			while (counties.options.length > 0)
				counties.options[0] = null;
		}

		/* Fill counties list	*/
		if (states_sort_override) {

			/* Sort	*/
			var tmp = [];
			for (var i in countries[code].states[scode].counties) {
				tmp[i] = countries[code].states[scode].counties[i];
				tmp[i].idx = i;
			}
			tmp.sort(sort_states);

			/* Fill list	*/
			for (var i in tmp) {
				if (!tmp[i])
					continue;
				counties.options[counties.options.length] = new Option(tmp[i].name, tmp[i].idx);
				if (this.countries.defaultCounties[scode] == tmp[i].idx) {
					counties.options[counties.options.length-1].selected = true;
					counties.selectedIndex = counties.options.length-1;
				}
			}

		} else {
			for (var i in countries[code].states[scode].counties) {
				counties.options[counties.options.length] = new Option(countries[code].states[scode].counties[i].name, i);
				if (this.countries.defaultCounties[scode] == i)
					counties.options[counties.options.length-1].selected = true;
			}
		}
		if (!this.countries.defaultCounties[scode])
			counties.options[0].selected = true;
	}

	this.countries.lastStateCode = scode;

	if (this.oldOnchange)
		this.oldOnchange();	

	if (counties.onchange)
		counties.onchange();
}

/*
	Replace tag by tag
*/
function tag_replace(obj, tag, tname) {
	var tmp = obj.parentNode.insertBefore(document.createElement(tag), obj);
	obj.parentNode.removeChild(obj);
	if (obj.name)
		obj.name = '';
	delete obj;
	if (tname)
		tmp.id = tmp.name = tname;
	return tmp;
}

/*
	Sort states and counties (Opera fix)
*/
function sort_states(a, b) {
	if (!a || !b || !a.order || !b.order || a.order == b.order)
		return 0;
	return a.order > b.order ? 1 : -1;
}

/*
	Check countries list (for Google Toolbar Autofill functionality)
*/
function check_countries(obj) {
	if (!obj || !obj.id)
		return;

	if (obj.oldSelectedIndex != undefined && obj.oldSelectedIndex != obj.selectedIndex && obj.onchange)
		obj.onchange();

	obj.oldSelectedIndex = obj.selectedIndex;
	obj.changeStatesTO = setTimeout("check_countries(document.getElementById('"+obj.id+"'));", 1000);
}
