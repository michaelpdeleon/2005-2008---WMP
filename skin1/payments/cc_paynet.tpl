{* $Id: cc_paynet.tpl,v 1.7.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>PayNet</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_paynet_payid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param02">
<option value="NOK"{if $module_data.param02 eq "NOK"} selected="selected"{/if}>Norwegian kroners
<option value="SEK"{if $module_data.param02 eq "SEK"} selected="selected"{/if}>Swedish kroners
<option value="DKK"{if $module_data.param02 eq "DKK"} selected="selected"{/if}>Danish kroners
<option value="USD"{if $module_data.param02 eq "USD"} selected="selected"{/if}>US dollars
<option value="EUR"{if $module_data.param02 eq "EUR"} selected="selected"{/if}>Euros
<option value="GBP"{if $module_data.param02 eq "GBP"} selected="selected"{/if}>British pounds
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_paynet_cert_path}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>


<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>


</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
