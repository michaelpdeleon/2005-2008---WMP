{* $Id: ch_wtsbank.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>WTS bank</h3>
{$lng.txt_ch_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_ch_wtsbank_parentid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_ch_wtsbank_subid}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param03">
<option value="US"{if $module_data.param03 eq "US"} selected="selected"{/if}>US Dollar
<option value="CN"{if $module_data.param03 eq "CN"} selected="selected"{/if}>Canadian Dollar
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_ch_wtsbank_action}:</td>
<td><select name="param04">
<option value="P"{if $module_data.param04 eq "P"} selected="selected"{/if}>Process
<option value="V"{if $module_data.param04 eq "V"} selected="selected"{/if}>Validate only
</select>
</td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_ch_settings content=$smarty.capture.dialog extra='width="100%"'}
