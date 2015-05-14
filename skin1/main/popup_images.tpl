{* $Id: popup_images.tpl,v 1.20.2.3 2006/07/11 08:39:27 svowl Exp $ *}
{config_load file="$skin_config"}
<html>
<head>
<title>{$lng.lbl_select_file|strip_tags}</title>
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
<script type="text/javascript" language="JavaScript 1.2">
<!--
var err_choose_file_first = "{$lng.err_choose_file_first|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var err_choose_directory_first = "{$lng.err_choose_directory_first|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var field_filename = "{$smarty.get.field_filename}";
var field_path = "{$smarty.get.field_path}";

{literal}
function setFile(filename, path) {
	if (window.opener) {

		if (window.opener.document[field_filename])
			window.opener.document[field_filename].value = filename;
		else if (window.opener.document.getElementById(field_filename))
			window.opener.document.getElementById(field_filename).value = filename;

		if (window.opener.document[field_path])
			window.opener.document[field_path].value = path;
		else if (window.opener.document.getElementById(field_path))
			window.opener.document.getElementById(field_path).value = path;

	}
	window.close ();
}

function setFileInfo() {
	if (document.files_form.path.value != "") {
		setFile(document.files_form.path.options[document.files_form.path.selectedIndex].text, document.files_form.path.value);
	} else {
		alert(err_choose_file_first);
	}
}

function setFilePreview() {
	if (document.files_form.path.value != "") {
		document.files_form.file_preview.value = document.files_form.path.value;
		document.files_form.submit();
	} else {
		alert(err_choose_file_first);
	}
}


function checkDirectory() {
	if (document.dir_form.dir.selectedIndex == -1) {
		alert(err_choose_directory_first);
		return false;
	}

	return true;
}

function setImagePreview() {
	if (document.files_form.enable_preview.checked)
		document.preview.src = 'getfile.php?file='+document.files_form.path.value.replace(/&/, "%26");
}

{/literal}
-->
</script>
</head>
<body class="background"{$reading_direction_tag}>
<table cellpadding="10" cellspacing="0" width="100%"><tr><td>

{assign var="width" value="width=33%"}
<br />
{capture name=dialog}

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td {$width} valign="top">
<form method="get" onsubmit="javascript: return checkDirectory ()" name="dir_form">
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path}" />
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="tp" value="images" />

<b>{$lng.lbl_directories}:</b><br />
<select name="dir" size="20" style="width: 100%" ondblclick="javascript: if(checkDirectory()) document.dir_form.submit();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype eq "dir"}
<option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}/</option>
{/if}
{/section}
</select><br /><br />
<center>
<input type="submit" value="{$lng.lbl_change_directory|strip_tags:false|escape}" /></center></form>
</td>

<form method="get" name="files_form" action="popup_files.php">

<td {$width} valign="top">
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape:"html"}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape:"html"}" />
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="file_preview" value="" />
<input type="hidden" name="tp" value="images" />

<b>{$lng.lbl_files}:</b>
<select name="path" size="20" style="width: 100%" onchange="setImagePreview()" ondblclick="javascript: setFileInfo();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype ne "dir"}
<option value="{$dir_entries[idx].href}" {if $dir_entries[idx].href eq $file_preview}selected{/if}>{$dir_entries[idx].file|truncate:35}</option>
{/if}
{/section}
</select><br /><br />
<center>
<input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setFileInfo ();" />
</center>
</td>

<td {$width} valign="top">
<b>&nbsp;</b>

<center>
{if $file_preview}
<img src="getfile.php?file={$file_preview}" name="preview" width="100" height="100" alt="{$lng.lbl_preview_image|escape}" /><br />
{else}
<img src="{$ImagesDir}/null.gif" name="preview" width="100" height="100" border="1" alt="{$lng.lbl_preview_image|escape}" /><br />
{/if}
<br />
<input type="checkbox" name="enable_preview" value="Y" checked="checked" /> Active
<table cellpadding="0" cellspacing="2" width="100%"><tr>
<td width="4"><img src="{$ImagesDir}/null.gif" width="4" height="1" alt="" /><br /></td><td><div align="justify">{$lng.txt_preview_images_note}</div></td>
</tr></table>
</center>
</td>
</form>
</tr>
</table>
{/capture}
<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_file extra='width="100%"'}
</div>

<p align="right"><a href="javascript:window.close();"><b>{$lng.lbl_close_window}</b></a></p>
</td></tr></table>

</body>
</html>
