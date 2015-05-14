/* $Id: editor.js,v 1.18.2.1 2006/06/01 11:00:14 max Exp $ */

function lmo(id) {
	/* find template div or span */
	var templates = grabTemplateNames(window.event.srcElement);
	setStatus(templates, id);
	lastID = id;
	window.event.srcElement.focus();
	var aElement = findAElement(window.event.srcElement);
	if (aElement != null) {
		/* check if there is Label dialog open */
		if (labelWindow == null || labelWindow.closed) {
			/* focus the link */
			aElement.onkeypress = lkp;
			aElement.onmouseover = lamo;
			aElement.focus();
		}
	}
	window.event.returnValue = true;
}

function grabTemplateNames(elem) {
	var templates = "";
	while (elem != null && elem.tagName.toUpperCase() != 'BODY') {
		if (elem.tagName.toUpperCase() == 'DIV' || elem.tagName.toUpperCase() == 'SPAN' || elem.tagName.toUpperCase() == 'TBODY') {
			if (elem.id != null && elem.id.indexOf('.tpl') > 0) {
				templates = elem.id.replace(/(\w)0(\w)/g, "$1/$2").replace(/\.tpl\d+/, ".tpl") + " > " + templates ;
			}
		}	
		elem = elem.parentElement;
	}
	return templates;
}

function findAElement(elem) {
	while ((elem.parentElement.tagName.toUpperCase()!='A' || elem.parentElement.href=='') && elem.parentElement.tagName.toUpperCase()!='BODY') {
		elem = elem.parentElement;
	}
	if (elem.parentElement.tagName.toUpperCase()!='A') {
		return null;
	}
	return elem.parentElement;
}

function lamo() {
	setStatus (grabTemplateNames(window.event.srcElement), lastID);
}

function lmu(id) {
	lastID = window.status = '';
	window.event.returnValue = true;
}
function lkp() {
	if (window.event.keyCode == 69 || window.event.keyCode == 101) { /* 'E', 'e' */
		if (lastID!='')
			showLabelForm(lastID);
		window.event.returnValue = true;

	} else if (window.event.keyCode == 67 || window.event.keyCode == 99 && window.clipboardData) { /* 'C', 'c' */
		clipboardData.setData('Text', lastID);
	}

}
function lmc(id) {
	if (findAElement(window.event.srcElement) != null) {
		return; /* inside <a> */
	}
	showLabelForm(id);
	window.event.returnValue = true;
}
function setLabel() {
	/* find all spans having onMouseOver=lom('labelWindow.lf.val.value'); */
	allElem = document.body.getElementsByTagName("SPAN");
	for (a = 0; a < allElem.length; a++) {
		if (allElem[a].onmouseover != null && allElem[a].id == labelID) {
			allElem[a].innerHTML = labelWindow.lf.val.value;
			lng_labels[labelID] = xescape(labelWindow.lf.val.value);
		}
	}	
}

function showLabelForm(id) {
	if (labelWindow && !labelWindow.closed && labelWindow.close)
		labelWindow.close();
	if (defaultLabelWindowX == 0) {
		defaultLabelWindowX = window.screenLeft+100;
		defaultLabelWindowY = window.screenTop+100;
	}
	openLabelForm(id);
}

function copyName() {
	if (window.clipboardData)
		clipboardData.setData("Text", labelID);
	window.close();
}

function dmo() {
	if (window.status == '') {
		window.status = grabTemplateNames(window.event.srcElement);
	}
}

/*
	functions to run from debug console
*/

/*
	make a border around the template
*/
function markTemplate(tmplt, remove) {
	/* find all spans and divs having name=tmplt */
	var tags = new Array("SPAN","DIV","TBODY");
	var reg = new RegExp("^"+tmplt.replace(/\./, "\."), "");
	var borderStyle = remove ? "0pt none black" : "1pt solid black";
	
	for (var i=0; i < tags.length; i++) {
		var allelem = document.body.getElementsByTagName(tags[i]);
		for (var a = 0; a < allelem.length; a++) {
			if (allelem[a].id == null || allelem[a].id.search(reg) == -1)
				continue;

			if (tags[i] == 'TBODY') {
				var firstRow = allelem[a].children[0];
				for (var col = 0; col < firstRow.children.length; col++)
					firstRow.children[col].style.borderTop = borderStyle;
				var lastRow = allelem[a].children[allelem[a].children.length-1];
				for (var col = 0; col < lastRow.children.length; col++)
					lastRow.children[col].style.borderBottom = borderStyle;

				lastRow.borderBottom = borderStyle;
				for (var j = 0; j < allelem[a].children.length; j++) {
					var child = allelem[a].children[j];
					child.children[0].style.borderLeft = borderStyle;
					child.children[child.children.length-1].style.borderRight = borderStyle;
				}
			} else {	
				markElement(allelem[a], remove);
			}
		}
	}
}

function tmo(tmplt) {
	markTemplate(tmplt, 0)
}
function tmu(tmplt) {
	markTemplate(tmplt, 1)
}

