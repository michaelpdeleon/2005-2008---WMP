// $Id: product_variants.js,v 1.6 2006/02/13 14:13:48 max Exp $
var old_cross = new Array();

function rebuildWP() {
var tbl = document.getElementById('wp_table');
var x, y, z, objTr, objTd, objInput, found;
var cross = new Array();
var vidsWP = new Array();
var cnt = -1;
var prefix = "wprices";
var prefix_rg = new RegExp(prefix+'_', 'g');

	if (!tbl)
		return false;

	for (x in vwprices) {
		if (isNaN(x))
			continue;
		if (vids[x] && vids[x][0].checked)
			vidsWP[vidsWP.length] = x;
	}

	if (vidsWP.length > 0) {
		cross = vwprices[vidsWP[0]];
	}

	if (vidsWP.length > 1) {
		for (x = 1; x < vidsWP.length; x++) {
			if (!vwprices[vidsWP[x]]) {
				cross = new Array();
				break;
			}
			for (y = 0; y < cross.length; y++) {
				found = false;
				for (z = 0; z < vwprices[vidsWP[x]].length; z++) {
					if (cross[y][0] == vwprices[vidsWP[x]][z][0] && cross[y][1] == vwprices[vidsWP[x]][z][1])
						found = true;
				}
				if (!found)
					cross.splice(y, 1);
			}
		}
	}

	if (cross.length == 0)
		old_cross = new Array();

	if (old_cross.length == cross.length && old_cross.length > 0) {
		for (x = 0; x < cross.length; x++) {
			found = false;
			for (y = 0; y < old_cross.length; y++) {
				if (cross[x][0] == old_cross[y][0] && cross[x][1] == old_cross[y][1])
					found = true;
			}
			if (!found) {
				old_cross = new Array();
				break;
			}
		}
	} else {
		old_cross = new Array();
	}

	if (old_cross.length > 0)
		return true;

	for (x = tbl.rows.length-1; x > 0; x--) {
		if (tbl.rows[x].id.search(prefix_rg) == 0)
			tbl.deleteRow(tbl.rows[x].rowIndex);
	}

	var firstRow = tbl.rows[0].rowIndex ? tbl.rows[0].rowIndex+1 : 1;
	for (x = 0; x < cross.length; x++) {
		objTr = tbl.insertRow(firstRow);
		objTr.id = prefix+"_"+x;
		objTd = objTr.insertCell(0);
		objTd.innerHTML = cross[x][0];

		objTd = objTr.insertCell(1);

		objInput = document.createElement("INPUT");
		objInput.type = "text";
		objTd.appendChild(objInput);
		objInput.size = 7;
		objInput.name = "wprices["+x+"][price]";
		objInput.value = cross[x][2];

		objInput = document.createElement("INPUT");
		objInput.type = "hidden";
		objTd.appendChild(objInput);
		objInput.name = "wprices["+x+"][quantity]";
		objInput.value = cross[x][0];

		objInput = document.createElement("INPUT");
		objInput.type = "hidden";
		objTd.appendChild(objInput);
		objInput.name = "wprices["+x+"][membershipid]";
		objInput.value = cross[x][1];

		objTd = objTr.insertCell(2);
		if (cross[x][1] == 0) {
			objTd.innerHTML = lbl_all;
		} else if (memberships[cross[x][1]]) {
			objTd.innerHTML = memberships[cross[x][1]];
		} else {
			objTd.innerHTML = cross[x][1];
		}

		objTd = objTr.insertCell(3);
		objInput = document.createElement("INPUT");
		objInput.type = "button";
		objTd.appendChild(objInput);
		objInput.value = lbl_delete;
		objInput.id = x;
		objInput.onclick = new Function('', 'document.productvariantsform.mode.value = "delete_wprice"; document.productvariantsform.delete_wprice_quantity.value = old_cross[this.id][0]; document.productvariantsform.delete_wprice_membershipid.value = old_cross[this.id][1]; document.productvariantsform.submit();');

		firstRow = objTr.rowIndex+1;
	}

	old_cross = cross;	
}

function vidsChecked() {
	for (var v in vids) {
		if (vids[v][0].checked)
			return true;
	}

	return false;
}

function addWImage() {
	if (pwindow && !pwindow.closed) {
		setTimeout("addWImage()", 200);
		return;

	} else if (document.getElementById('imageW_onunload').value != 'Y') {
		imgTStmap = oldTStmap;
		return;
	}

	document.productvariantsform.tstamp.value = imgTStmap;
	document.getElementById('imageW_onunload').value = '';

	for (var v in vids) {
		displayImage(vids[v][0], v);
	}
}

function resetWImage() {
	for (var v in vids) {
		vids[v][1].src = current_location+"/image.php?type=W&id="+v;
	}

	obj = document.getElementById('skip_image_W');
	if (obj)
		obj.value = "Y";

	oldTStmap = imgTStmap = null;
	document.getElementById('imageW_reset').style.display = 'npne';
}

function displayImage(obj, id) {
	var d = new Date();

	if (!obj.checked) {
		vids[id][1].src = current_location+"/image.php?type=W&id="+id+"&tmp="+d.getTime();
	} else if (imgTStmap && pwindow && pwindow.closed) {
		vids[id][1].src = current_location+"/image.php?type=W&id="+imgTStmap+"&tmp="+d.getTime();
	}
}
