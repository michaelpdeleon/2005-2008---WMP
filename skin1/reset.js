// $Id: reset.js,v 1.2 2005/12/01 14:28:19 max Exp $

function reset_form(formname, localDef) {
	var x, y, z, obj, localDef, selectedItems, hash_radio;
	var form = document.forms.namedItem(formname);
	if (!form)
		return false;

	var hash_radio = new Array();

	for (x = 0; x < form.elements.length; x++) {
		obj = form.elements[x];
		if ((obj.tagName == 'INPUT' || obj.tagName == 'SELECT' || obj.tagName == 'TEXTAREA') && obj.name != '' && obj.type != 'hidden') {
			var reset_value = '';
			var found = false;
			for (y = 0; y < localDef.length; y++) {
				if (obj.name == localDef[y][0] || obj.id == localDef[y][0]) {
					reset_value = localDef[y][1];
					found = true;
					break;
				}
			}

			if (!found)
				continue;

			if (obj.tagName == 'SELECT') {
				obj.selectedIndex = obj.multiple ? -1 : 0;
				reset_value = reset_value.valueOf();
				selectedItems = new Array();
				if (reset_value.length > 0)
					selectedItems = reset_value.split(',');
				for (z = 0; z < obj.options.length; z++) {
					for (y = 0; y < selectedItems.length; y++) {
						if (obj.options[z].value == selectedItems[y] || obj.options[z].text == selectedItems[y]) {
							obj.options[z].selected = true;
						}
					}
				}
			} else if (obj.tagName == 'INPUT' && obj.type == 'radio') {
				var is_found = false;
				for (z = 0; z < hash_radio.length; z++) {
					if (hash_radio[z][0] == obj.name) {
						is_found = hash_radio[z][1];
						break;
					}
				}
				if (is_found == 'F')
					continue;
				obj.checked = (obj.value == reset_value || is_found === false);
				hash_radio[hash_radio.length] = new Array(obj.name, (obj.value == reset_value) ? "F" : "");
			} else if (obj.tagName == 'INPUT' && obj.type == 'checkbox') {
				obj.checked = reset_value;
			} else {
				obj.value = reset_value;
			}

			if (!obj.disabled) {
				if(obj.onclick)
					obj.onclick.apply(obj);
				if (obj.onchange)
					obj.onchange.apply(obj);
			}
		}
	}
}
