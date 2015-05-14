{* $Id: cc_securetrading.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>SECURETRADING.net</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_securetrading_desc}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_securetrading_login}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param03">
<option value=GBP{if $module_data.param03 eq "GBP"} selected="selected"{/if}>Britain Pound
<option value=EUR{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euro
<option value=USD{if $module_data.param03 eq "USD"} selected="selected"{/if}>US Dollar
</select>
</td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
