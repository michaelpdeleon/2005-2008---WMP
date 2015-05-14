{* $Id: cc_censeo.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>{$module_data.module_name}</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_censeo_company}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /><br />
{if $module_data.module_name eq "Censeo (dialog)"}{$lng.lbl_cc_censeo_company_note}{else}{$lng.lbl_cc_censeo_nodialog_company_note}{/if}
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_censeo_secret}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_censeo_project}:</td>
<td><input type="text" name="param03" size="24" value="{$module_data.param03|escape}" /><br />
{$lng.lbl_cc_censeo_project_note}
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param05">
<option value="978"{if $module_data.param05 eq "978"} selected="selected"{/if}>Euro (Europe)
<option value="752"{if $module_data.param05 eq "752"} selected="selected"{/if}>Swedish Krona (Sweden)
<option value="840"{if $module_data.param05 eq "840"} selected="selected"{/if}>US Dollar (United States)
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="36" value="{$module_data.param04|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
