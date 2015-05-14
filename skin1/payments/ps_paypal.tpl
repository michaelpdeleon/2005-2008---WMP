{* $Id: ps_paypal.tpl,v 1.6.2.1 2006/06/16 10:47:51 max Exp $ *}

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_paypal_acc}:</td>
<td><input type="text" name="{$conf_prefix}[param01]" size="24" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_paypal_for}:</td>
<td><input type="text" name="{$conf_prefix}[param02]" size="24" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="{$conf_prefix}[param03]">
<option value="USD"{if $module_data.param03 eq "USD"} selected="selected"{/if}>U.S. Dollars (USD)
<option value="CAD"{if $module_data.param03 eq "CAD"} selected="selected"{/if}>Canadian Dollars (CAD)
<option value="EUR"{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euros (EUR)
<option value="GBP"{if $module_data.param03 eq "GBP"} selected="selected"{/if}>Pounds Sterling (GBP)
<option value="JPY"{if $module_data.param03 eq "JPY"} selected="selected"{/if}>Yen (JPY)
<option value="AUD"{if $module_data.param03 eq "AUD"} selected="selected"{/if}>Australian Dollars (AUD)
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="{$conf_prefix}[param04]" size="36" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="{$conf_prefix}[testmode]">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
<br /><font class="SmallText">
{$lng.txt_paypal_sandbox_note}
</font>
</td>
</tr>

</table>
