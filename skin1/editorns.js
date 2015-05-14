/* $Id: editorns.js,v 1.16 2006/04/12 08:05:26 max Exp $ */

function lmo(id, event) {
	/* find template div or span */
	var templates = grabTemplateNames(event.currentTarget);
	setTimeout("setStatus(\""+templates+"\", \""+id+"\")", 20);
	lastID = id;
	var aElement = findAElement(event.currentTarget);
	if (aElement != null) {
		/* check if there is Label dialog open */
		if (labelWindow == null || labelWindow.closed) {
			aElement.addEventListener("keypress", lkp, true);
			/* focus the link */
			aElement.focus();
		}
	}
}

function grabTemplateNames(elem) {
	var templates = "", tst=""
	while (elem != null) {
		if (elem.tagName != null && (elem.tagName.toUpperCase() == 'DIV' || elem.tagName.toUpperCase() == 'SPAN' || elem.tagName.toUpperCase() == 'TBODY')) {
			var attr = elem.attributes.getNamedItem("id");
			if (attr != null && attr.name != null && attr.value.indexOf('.tpl') > 0)
				templates = attr.value.replace(/(\w)0(\w)/g, "$1/$2").replace(/\.tpl\d+/, ".tpl") + " > " + templates;
		}	
		elem = elem.parentNode;
	}
	return templates;
}

function findAElement(elem) {
	while ((elem.parentNode.tagName.toUpperCase() != 'A' || elem.parentNode.href == '') && elem.parentNode.tagName.toUpperCase() != 'BODY')
		elem = elem.parentNode;
	if (elem.parentNode.tagName.toUpperCase() != 'A')
		return null;
	return elem.parentNode;
}

function lamo(event) {
	setStatus (grabTemplateNames(event.currentTarget), lastID);
}

function lmu(id, event) {
	lastID = window.status = '';
}

function lkp(event){
	if (event.charCode == 69 || event.charCode == 101) { /* 'E', 'e' */
		if (lastID!='')
			showLabelForm(lastID);
	}
}

function lmc(id, event) {
	if (findAElement(event.currentTarget) != null)
		return;
	showLabelForm(id);
}

function setLabel() {
	/* find all spans having onMouseOver=lom('labelWindow.lf.val.value') */;
	allElem = document.body.getElementsByTagName("SPAN");
	for (var a = 0; a < allElem.length; a++) {
		var attr = allElem[a].attributes.getNamedItem("id");
		if (attr != null && attr.value == labelID) {
			allElem[a].innerHTML = labelWindow.document.lf.val.value;
			lng_labels[labelID] = xescape(labelWindow.document.lf.val.value);
		}
	}	
}

function showLabelForm(id) {
	if (labelWindow != null && !labelWindow.closed)
		labelWindow.close();
	if (defaultLabelWindowX == 0) {
		defaultLabelWindowX = window.screenX+100;
		defaultLabelWindowY = window.screenY+100;
	}
	openLabelForm(id);
}

function dmo(event) {
	if (window.status == '')
		window.status = grabTemplateNames(event.currentTarget);
}

/*
	functions to run from debug console
*/

/*
	make a border around the template
*/
function markTemplate(tmplt, remove) {
	/* find all spans and divs having name=tmplt */
	var tags = new Array('SPAN',"DIV","TBODY");
	var reg = new RegExp("^"+tmplt.replace(/\./, "\."), "");
	var borderStyle = remove ? "0pt none black" : "1pt solid black";

	for (var i = 0; i < tags.length; i++) {
		var allelem = document.body.getElementsByTagName(tags[i]);
		for (var a = 0; a < allelem.length; a++) {
			var attr = allelem[a].attributes.getNamedItem("id");
			if (attr == null || attr.value.search(reg) == -1)
				continue;

			if (tags[i] == 'TBODY') {
				for (var j = 0; j < allelem[a].childNodes.length; j++) {
					var row = allelem[a].childNodes[j];
					for (var k = 0; k < row.childNodes.length; k++) {
						if (row.childNodes[k].tagName == 'TD')
							row.childNodes[k].style.border = borderStyle;
					}	
				}
			} else {
				markElement(allelem[a], remove);
			}
		}
	}
}

function tmo(tmplt, event) {
	markTemplate(tmplt, 0);
}
function tmu(tmplt, event) {
	markTemplate(tmplt, 1);
}

