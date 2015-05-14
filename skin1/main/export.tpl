{* $Id: export.tpl,v 1.19.2.4 2006/07/11 08:39:27 svowl Exp $ *}
{capture name=dialog}

{$lng.txt_export_note}<br />
<br />

{if $need_select_provider}

{if $data_provider ne ''}
{assign var="display_none_open" value="display: none; "}
{else}
{assign var="display_none_close" value="display: none; "}
{/if}

<div align="right">
{include file="main/visiblebox_link.tpl" mark="4" title=$lng.lbl_import_data_provider}
</div>

<br /><br />

<table cellpadding="0" cellspacing="0" width="100%" style="{$display_none_close}" id="box4"><tr><td>

{include file="main/subheader.tpl" title=$lng.lbl_import_data_provider}

{$lng.txt_data_provider_login_export}

<form action="import.php" method="post" name="changeproviderform">
<input type="hidden" name="mode" value="export" />
<input type="hidden" name="action" value="change_provider" />

<table cellpadding="0" cellspacing="3">

<tr>
	<td><b>{$lng.lbl_data_provider_login}:</b></td>
	<td><input type="text" size="35" name="data_provider" value="{$export_data.provider}" /></td>
	<td>{include file="buttons/button.tpl" href="javascript: document.changeproviderform.submit();" type="input"}</td>
</tr>

</table>
</form>

<br /><br /><br />

	</td>
</tr>
</table>
{/if}

{include file="main/subheader.tpl" title=$lng.lbl_export_data}

<form action="import.php" method="post" name="exportdata_form">
<input type="hidden" name="mode" value="export" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td valign="top">
	<b>{$lng.lbl_csv_delimiter}:</b><br />
	{ include file="provider/main/ie_delimiter.tpl" field_name="data[delimiter]" saved_delimiter=$export_data.delimiter}
	</td>
</tr>

<tr><td>&nbsp;</td></tr>

<tr>
	<td valign="top">
	<b>{$lng.lbl_data_rows_per_file}:</b><br />
<input type="text" name="data[rows_per_file]" value="{$export_data.rows_per_file|default:""}" /><br />
{$lng.lbl_data_rows_per_file_expl}
	</td>
</tr>

{if $export_options ne ''}
{foreach from=$export_options item=v}
<tr><td>&nbsp;</td></tr>

<tr>
	<td>{include file=$v}</td>
</tr>
{/foreach}
{/if}

<tr><td>&nbsp;</td></tr>

<tr><td valign="top">
{include file="main/check_all_row.tpl" style="line-height: 170%;" form="exportdata_form" prefix="check"}

<table cellspacing="1" cellpadding="0" width="100%">
<tr class="TableHead">
	<td width="15">&nbsp;</td>
	<td>{$lng.lbl_data_type}</td>
	<td colspan="2">{$lng.lbl_data_range}</td>
</tr>
{include file="main/export_specs.tpl" export_spec=$export_spec level=0}
</table>

	</td>
</tr>

<tr>
	<td>
	<table cellpadding="1" cellspacing="1" width="100%">
	<tr>
		<td class="SubmitBox">
		<input type="submit" value="{$lng.lbl_export|strip_tags:false|escape}" />
		</td>
		<td class="SubmitBox" align="right">
		<input type="button" value="{$lng.lbl_reset|strip_tags:false|escape}" onclick="javascript: reset_form('exportdata_form', exportdata_form_def); change_all(false);" />
		</td>
	</tr>
	</table>
	</td>
</tr>

</table>
</form>

{include file="main/include_js.tpl" src="reset.js"}
<script type="text/javascript">
<!--
var exportdata_form_def = [
	['data[delimiter]', ';'],
	['data[rows_per_file]', ''],
	['options[category_sep]', '/'],
	['options[export_images]', 'Y'],
	['options[images_directory]', '{$export_images_dir}']
];
-->
</script>

{if $export_log_url}
<br />
<div align="right">{include file="buttons/button.tpl" href=$export_log_url button_title=$lng.lbl_view_export_log}</div>
{/if}

{if $export_packs ne ''}
<br />
<a name="packs" />
<div align="right">{include file="main/visiblebox_link.tpl" mark="epacks" title=$lng.lbl_export_packs visible=$smarty.get.status}</div>

<br /><br />

<form action="import.php" method="post" name="exportpacks_form">
<input type="hidden" name="mode" value="export" />
<input type="hidden" id="exportpacks_action" name="action" value="" />

<table cellpadding="0" cellspacing="0" width="100%" style="{if $smarty.get.status ne 'success'}display: none;{/if}" id="boxepacks"><tr><td>

{include file="main/subheader.tpl" title=$lng.lbl_export_packs}

<script type="text/javascript" language="JavaScript 1.2"><!--
var ep_checkboxes = new Array({foreach from=$export_packs item=v key=k}'packs[{$k}]',{/foreach}'');
 
--></script>

<div style="line-height:170%"><a href="javascript:change_all(true, 'exportpacks_form', ep_checkboxes);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false, 'exportpacks_form', ep_checkboxes);">{$lng.lbl_uncheck_all}</a></div>

<table cellpadding="2" cellspacing="1">
<tr class="TableHead">
	<td width="15">&nbsp;</td>
	<td>{$lng.lbl_date}</td>
	<td>{$lng.lbl_files}</td>
	<td>{$lng.lbl_language}</td>
	<td>{$lng.lbl_data_types}</td>
</tr>
{foreach from=$export_packs item=pack key=kpack}
<tr{cycle name="ep" values=" , class='TableSubHead'" advance=false}>
	<td valign="top" rowspan="{$pack.count}" height="20"><input type="checkbox" name="packs[{$kpack}]" value="{$kpack}" /></td>
	<td valign="top" rowspan="{$pack.count}" height="20">
		<table cellspacing="0" cellpadding="0" height="20"><tr><td>{$pack.date|date_format:$config.Appearance.date_format} {$pack.date|date_format:$config.Appearance.time_format}</td></tr></table>
	</td>
{assign var="is_first" value="Y"}
{foreach from=$pack.files item=f key=fn}
{if $is_first eq 'Y'}
{assign var="is_first" value="F"}
{else}
<tr{cycle name="ep" values=" , class='TableSubHead'" advance=false}>
{/if}
	<td valign="top" rowspan="{$f.sections_count|default:1}"><a href="get_export.php?file={$fn}">{$fn}</a></td>
	<td valign="top" rowspan="{$f.sections_count|default:1}" align="center">{if $f.code eq ''}-{else}{$f.code_name|default:$f.code}{/if}</td>
{if $f.sections_count > 0}
{assign var="is_first2" value="Y"}
{foreach from=$f.sections item=sec}
{if $is_first2 eq 'Y'}{assign var="is_first2" value="F"}{else}<tr{cycle name="ep" values=" , class='TableSubHead'" advance=false}>{/if}
	<td nowrap="nowrap">{$sec|replace:"_":" "}</td>
{if $is_first2 eq 'F' && $is_first eq 'F'}
	{assign var="is_first" value=""}
	{assign var="is_first2" value=""}
{/if}
</tr>
{/foreach}
{/if}
</tr>
{/foreach}
</tr>
{if $pack.dir_exists}
<tr{cycle name="ep" values=" , class='TableSubHead'" advance=false}>
	<td colspan="3" style="padding-top: 10px;">{$lng.lbl_temporary_directory_for_images}: {$pack.dir_exists}</td>
</tr>
{/if}
{cycle name="ep" values=" , class='TableSubHead'" print=false}
{/foreach}
</table>

<br />

<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: document.getElementById('exportpacks_action').value='delete_pack'; document.exportpacks_form.submit();" />

</td></tr>
</table>

</form>
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_export_data content=$smarty.capture.dialog extra='width="100%"'}
