{* $Id: import.tpl,v 1.14.2.4 2006/08/04 05:23:29 svowl Exp $ *}

{capture name=dialog}

{if $show_error ne ""}

{$lng.txt_import_error}

<br /><br />
{if $import_log_url}
<div align="right">{include file="buttons/button.tpl" href="$import_log_url" button_title=$lng.lbl_view_entire_import_log}</div>

<br />

<table cellspacing="0" cellpadding="1" width="100%">
<tr>
	<td bgcolor="#CCCCCC">

<table cellspacing="0" cellpadding="10" width="100%">
<tr>
	<td class="SectionBox">{$import_log_content}</td>
</tr>
</table>

	</td>
</tr>
</table>
{else}
{$lng.txt_log_file_error|substitute:"file":$import_log_file}
{/if}

<br /><br />

{include file="buttons/button.tpl" href="import.php" button_title=$lng.lbl_back_to_import_page}

<br /><br />

{else}

{$lng.txt_import_data_note}

<br /><br />

{include file="buttons/button.tpl" button_title=$lng.lbl_sample_import_file href="javascript:window.open('popup_info.php?action=IMPORT','IMPORT_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"}

<br /><br />

{if $need_select_provider}

{if $data_provider ne ''}
{assign var="display_none_open" value="display: none; "}
{assign var="idp_visible" value=true}
{else}
{assign var="display_none_close" value="display: none; "}
{assign var="idp_visible" value=false}
{/if}

<div align="right">
{include file="main/visiblebox_link.tpl" mark="4" title=$lng.lbl_import_data_provider visible=$idp_visible}
</div>

<br /><br />

<table cellpadding="0" cellspacing="0" width="100%" style="{$display_none_close}" id="box4"><tr><td>

{include file="main/subheader.tpl" title=$lng.lbl_import_data_provider}

{$lng.txt_data_provider_login}

<form action="import.php" method="post" name="changeproviderform">
<input type="hidden" name="action" value="change_provider" />

<table cellpadding="0" cellspacing="3">

<tr>
	<td><b>{$lng.lbl_data_provider_login}:</b></td>
	<td><input type="text" size="35" name="data_provider" value="{$data_provider}" /></td>
	<td>{include file="buttons/button.tpl" href="javascript: document.changeproviderform.submit();" type="input"}</td>
</tr>

</table>
</form>

<br /><br /><br />

	</td>
</tr>
</table>
{/if}


{include file="main/subheader.tpl" title=$lng.lbl_import_data}

{$lng.txt_import_data_note2}

<script type="text/javascript">
<!--

var drop_alert = '{$lng.txt_import_data_types_js_warning|escape:javascript}';

{literal}
function checkDrops(f) {
	for (var x = 0; x < f.elements.length; x++) {
		if (f.elements[x].name.search(/^drop\[/) != -1) {
			if (f.elements[x].checked)
				return confirm(drop_alert);
		}
	}
	return true;
}
{/literal}
-->
</script>
<form action="import.php" method="post" enctype="multipart/form-data" name="importdata_form" onsubmit="javascript: return checkDrops(this);">
<input type="hidden" name="mode" value="import" />

<table cellpadding="5" cellspacing="1" width="100%">

<tr>
	<td valign="top" width="50%">
	<b>{$lng.lbl_csv_delimiter}:</b><br />{ include file="provider/main/ie_delimiter.tpl" saved_delimiter=$import_data.delimiter}
	</td>
</tr>

<tr>
	<td colspan="2">
<script type="text/javascript">
<!--
{if $import_data eq '' || $import_data.source eq 'server'}
filesrc='1';
{elseif $import_data.source eq 'upload'}
filesrc='2';
{else}
filesrc='3';
{/if}
-->
</script>
<br />
<b>{$lng.txt_source_import_file}:</b>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="20">&nbsp;</td>
	<td><input type="radio" id="source_server" name="source" value="server"{if $import_data eq '' || $import_data.source eq 'server'} checked="checked"{/if} onclick="javascript: if (filesrc=='1') return true; visibleBox(filesrc, 1); filesrc='1'; visibleBox(filesrc, 1);" /></td>
	<td><label for="source_server">{$lng.lbl_server}</label></td>
</tr>
<tr>
	<td width="20">&nbsp;</td>
	<td><input type="radio" id="source_upload" name="source" value="upload"{if $import_data.source eq 'upload'} checked="checked"{/if} onclick="javascript: if (filesrc=='2') return true; visibleBox(filesrc, 1); filesrc='2'; visibleBox(filesrc, 1);" /></td>
	<td><label for="source_upload">{$lng.lbl_home_computer}</label></td>
</tr>
<tr>
	<td width="20">&nbsp;</td>
	<td><input type="radio" id="source_url" name="source" value="url"{if $import_data.source eq 'url'} checked="checked"{/if} onclick="javascript: if (filesrc=='3') return true; visibleBox(filesrc, 1); filesrc='3'; visibleBox(filesrc, 1);" /></td>
	<td><label for="source_url">{$lng.lbl_url}</label></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td colspan="2"><br />
<div id="box1" {if $import_data ne '' && $import_data.source ne 'server'} style="display: none;"{/if}>
<b>{$lng.txt_csv_file_is_located_on_the_server}:</b>
<br />
<input type="text" size="70" name="localfile" value="{$import_data.localfile|default:"`$my_files_location`/import.csv"}" />
<br />
{$lng.txt_csv_file_is_located_on_the_server_expl|substitute:"my_files_location":$my_files_location}
</div>

<div id="box2"{if $import_data eq '' || $import_data.source ne 'upload'} style="display: none;"{/if}>
<b>{$lng.lbl_csv_file_for_upload}:</b><br /><input type="file" size="70" name="userfile" />

{if $upload_max_filesize}
<br /><font class="Star">{$lng.lbl_warning}!</font> {$lng.txt_max_file_size_that_can_be_uploaded}: {$upload_max_filesize}b.
{/if}
</div>

<div id="box3"{if $import_data eq '' || $import_data.source ne 'url'} style="display: none;"{/if}>
<b>{$lng.txt_csv_file_is_located_on_the_remote}:</b>
<br />
<input type="text" size="70" name="urlfile" value="{$import_data.urlfile}" />
<br />&nbsp;
</div>

	</td>
</tr>
</table>

<br /><br />

<div align="right">
{include file="main/visiblebox_link.tpl" mark="5" title=$lng.lbl_import_options}
</div>
{include file="main/import_options.tpl"}

<br /><br />

{include file="buttons/rarrow.tpl"} <input type="submit" value="{$lng.lbl_import|strip_tags:false|escape}" />

</form>

{include file="main/include_js.tpl" src="reset.js"}
<script type="text/javascript">
<!--
var importdata_form_def = new Array();
importdata_form_def[0] = new Array('options[category_sep]', '/');
importdata_form_def[1] = new Array('options[categoryid]', '0');
importdata_form_def[2] = new Array('options[images_directory]', '');
importdata_form_def[3] = new Array('options[crypt_order_details]', 'Y');
importdata_form_def[4] = new Array('options[crypt_password]', 'Y');
-->
</script>
{if $import_log_url}
<div align="right">{include file="buttons/button.tpl" href="$import_log_url" button_title=$lng.lbl_view_import_log}</div>
{/if}

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_import_data content=$smarty.capture.dialog extra='width="100%"'}
