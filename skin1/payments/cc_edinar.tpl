{* $Id: cc_edinar.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>E-Dinar</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_edinar_account}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param02">
<option value="1"{if $module_data.param02 eq "1"} selected="selected"{/if}>US Dollar (USD)
<option value="2"{if $module_data.param02 eq "2"} selected="selected"{/if}>Canadian Dollar (CAD)
<option value="41"{if $module_data.param02 eq "41"} selected="selected"{/if}>Swiss Francs (CHF)
<option value="44"{if $module_data.param02 eq "44"} selected="selected"{/if}>Gt. Britain Pound (GPB)
<option value="61"{if $module_data.param02 eq "61"} selected="selected"{/if}>Australian Dollar (AUD)
<option value="81"{if $module_data.param02 eq "81"} selected="selected"{/if}>Japanese Yen (JPY)
<option value="85"{if $module_data.param02 eq "85"} selected="selected"{/if}>Euro (EUR)
<option value="7777"{if $module_data.param02 eq "97"} selected="selected"{/if}>E-dinar (DIN)
</select>
</td>
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
