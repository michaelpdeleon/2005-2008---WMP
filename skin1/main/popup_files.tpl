{* $Id: popup_files.tpl,v 1.14.2.4 2006/07/11 08:39:27 svowl Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html>
<head>
	<title>{$lng.lbl_select_file}</title>
	<link rel="stylesheet" href="{$SkinDir}/skin1_admin.css" />
<script type="text/javascript" language="JavaScript 1.2">
<!--
var err_choose_file_first = "{$lng.err_choose_file_first|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var err_choose_directory_first = "{$lng.err_choose_directory_first|strip_tags|replace:"\n":" "|replace:"\r":" "}";
{literal}
function setFile (filename, path) {
	if (window.opener) {
{/literal}
		if (window.opener.document.{$smarty.get.field_filename})
			window.opener.document.{$smarty.get.field_filename}.value = filename;
		if (window.opener.document.{$smarty.get.field_path})
			window.opener.document.{$smarty.get.field_path}.value = path;
{literal}
	}
	window.close();
}

function setFileInfo () {
	if (document.files_form.path.value != "") {
		setFile(document.files_form.path.options[document.files_form.path.selectedIndex].text, document.files_form.path.value);
	} else {
		alert(err_choose_file_first);
	}
}

function checkDirectory () {
	if (document.dir_form.dir.selectedIndex == -1) {
		alert(err_choose_directory_first);
		return false;
	}
	return true;
}

{/literal}
-->
</script>
</head>
<body>
<br />
{capture name=dialog}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="50%" valign="top">

<form method="get" onsubmit="javascript: return checkDirectory ()" name="dir_form">
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape}" />
{if $product_provider}
<input type="hidden" name="product_provider" value="{$product_provider|escape}" />
{/if}

<b>{$lng.lbl_directories}:</b><br />
<select name="dir" size="20" style="width: 100%" ondblclick="javascript: if (checkDirectory()) document.dir_form.submit();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype eq "dir"}
	<option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}/</option>
{/if}
{/section}
</select>
<br /><br />
<center><input type="submit" value="{$lng.lbl_change_directory|strip_tags:false|escape}" /></center>
</form>

	</td>
	<td width="50%" valign="top">

<form method="get" name="files_form">
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape:"html"}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape:"html"}" />
<b>{$lng.lbl_files}:</b>
<select name="path" size="20" style="width: 100%" ondblclick="javascript: setFileInfo();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype ne "dir"}
	<option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}</option>
{/if}
{/section}
</select>
<br /><br />
<center>
<input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setFileInfo();" /></center>

</form>

	</td>
</table>
{/capture}

<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_file extra="width=90%"}
</div>

</body>
</html>
