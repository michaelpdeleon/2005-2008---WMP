// $Id: zone_edit.js,v 1.2 2006/01/05 11:36:59 svowl Exp $
function moveSelect(left, right, type) {
	if (type != 'R') {
		var tmp = left;
		left = right;
		right = tmp;
	}
	if (!left || !right)
		return false;

	while (right.selectedIndex != -1) {
		left.options[left.options.length] = new Option(right.options[right.selectedIndex].text, right.options[right.selectedIndex].value);
		right.options[right.selectedIndex] = null;
	}

	return true;
}

function saveSelects(objects) {
	if (!objects)
		return false;

	if (document.zoneform.zone_name.value == '') {
		alert(msg_err_zone_rename);
		return false;
	}
	
	for (var sel = 0; sel < objects.length; sel++) {
		if (document.getElementById(objects[sel]))
			if (document.getElementById(objects[sel]+"_store").value == '')
				for (var x = 0; x < document.getElementById(objects[sel]).options.length; x++)
					document.getElementById(objects[sel]+"_store").value += document.getElementById(objects[sel]).options[x].value+";";
	}
	return true;
}

function checkZone(zone, name) {
var codes;

	var obj = document.getElementById(name);
	if (zone == 'ALL') {
		for (var x = 0; x < obj.options.length; x++)
			obj.options[x].selected = true;
		return true;
	}

	eval('codes = zones.'+zone);
	if (codes)
		for(var x = 0; x < obj.options.length; x++)
			eval('obj.options[x].selected = codes.'+obj.options[x].value);
}
