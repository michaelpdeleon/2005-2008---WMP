{* $Id: cc_paygate.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>PayGate</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_paygate_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option value="USD"{if $module_data.param02 eq "USD"} selected="selected"{/if}>US Dollar
<option value="GBP"{if $module_data.param02 eq "GBP"} selected="selected"{/if}>Britain Pound
<option value="EUR"{if $module_data.param02 eq "EUR"} selected="selected"{/if}>Euro
<option value="ATS"{if $module_data.param02 eq "ATS"} selected="selected"{/if}>Schilling
<option value="DKK"{if $module_data.param02 eq "DKK"} selected="selected"{/if}>Danish Krone
<option value="HUF"{if $module_data.param02 eq "HUF"} selected="selected"{/if}>Forint
<option value="JPY"{if $module_data.param02 eq "JPY"} selected="selected"{/if}>Yen
<option value="LUF"{if $module_data.param02 eq "LUF"} selected="selected"{/if}>Luxembourg Franc
<option value="NLG"{if $module_data.param02 eq "NLG"} selected="selected"{/if}>Netherlands Guilden
<option value="NOK"{if $module_data.param02 eq "NOK"} selected="selected"{/if}>Norwegian Krone
<option value="SEK"{if $module_data.param02 eq "SEK"} selected="selected"{/if}>Swedish Krona
<option value="CHF"{if $module_data.param02 eq "CHF"} selected="selected"{/if}>Swiss Franc
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_paygate_blowfishpass}:</td>
<td><input type="password" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
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
