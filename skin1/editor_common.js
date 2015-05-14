/* $Id: editor_common.js,v 1.4 2006/02/16 13:45:23 max Exp $ */

var lastID;
var defaultLabelWindowX = 0, defaultLabelWindowY = 0;
var labelWindow;
var labelID;
var initialValue;

function xescape(str) {
	str = str.replace(/&/g, "&amp;");
	str = str.replace(/"/g, "&quot;")
	str = str.replace(/'/g, "&#039;")
	str = str.replace(/</g, "&lt;");
	str = str.replace(/>/g, "&gt;");

	return str;
}

function xunescape(str) {
	str = str.replace(/&amp;/g, "&");
	str = str.replace(/&quot;/g, '"')
	str = str.replace(/&#039;/g, "'")
	str = str.replace(/&lt;/g, "<");
	str = str.replace(/&gt;/g, ">");

	return str;
}

function setStatus(templates, id) {
	window.status = templates+id ;
}

function dmu(event) {
	window.status = '';
}

function markElement(elem, remove) {
	if (remove) {
		elem.style.border = 'none';
		elem.style.borderWidth = "0pt";
	} else {
		elem.style.border = 'solid';
		elem.style.borderWidth = "1pt";
		elem.style.borderColor = 'black';
	}
}

function openLabelForm(id) {
	labelID = id;
	var labelText = lng_labels[labelID];
	initialValue = xunescape(labelText);
	var as_tarea = (labelText.length > 40 || labelText.match(/[\n\r]/)) ? "&tarea=Y" : "";
	labelWindow = window.open(xcart_web_dir+"/popup_edit_label.php?current_area="+current_area+as_tarea, "labelWnd", "width=450, height=60, resizable=yes, left="+defaultLabelWindowX+", top="+defaultLabelWindowY);
}
