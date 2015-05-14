{* $Id: popup.tpl,v 1.1.2.2 2006/07/11 08:39:31 svowl Exp $ *}
{include file="modules/HTML_Editor/editor.tpl"}
{include file="modules/HTML_Editor/editor.tpl"}
<textarea id="TArea" name="TArea" cols="65" rows="12"></textarea>
<script type="text/javascript">
<!--
var id = "{$id}";

{literal}

if (isHTML_Editor) {
	var Editor = new InnovaEditor('Editor');
	Editor.mode = 'XHTMLBody';
	Editor.width = 550;
	Editor.height = 250;
	Editor.REPLACE("TArea");
}

function save() {
	if (window.opener && window.opener.document.getElementById(id))
		window.opener.document.getElementById(id).value = Editor.getXHTMLBody();
	window.close();
}

function init() {
	if (window.opener && window.opener.document.getElementById(id))
		Editor.putHTML(window.opener.document.getElementById(id).value.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&'));
}

if (window.addEventListener) {
	window.addEventListener("load", init, false);
} else if (window.attachEvent) {
	window.attachEvent("onload", init);
}

{/literal}
-->
</script>
<br />
<div align="center">
<input type="button" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick="javascript: save();";>
</div>
