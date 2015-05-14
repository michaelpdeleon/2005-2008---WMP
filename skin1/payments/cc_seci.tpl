{* $Id: cc_seci.tpl,v 1.4.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>Secure-I</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_seci_id}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_seci_key}:</td>
<td><input type="password" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
{*
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param03">
<option value="EUR"{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euro (Europe)
<option value="GBP"{if $module_data.param03 eq "GBP"} selected="selected"{/if}>Pound Sterling (United Kingdom)
<option value="USD"{if $module_data.param03 eq "USD"} selected="selected"{/if}>US Dollar (United States)
</select>
</td>
</tr>
*}
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
