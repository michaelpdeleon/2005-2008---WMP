{* $Id: cc_mbookers.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Money Bookers</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_mbookers_pay_to_email}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option {if $module_data.param02 eq "AUD"}selected{/if} value="AUD">Australian Dollar</option>
<option {if $module_data.param02 eq "BGN"}selected{/if} value="BGN">Bulgarian Lev</option>
<option {if $module_data.param02 eq "CAD"}selected{/if} value="CAD">Canadian Dollar</option>
<option {if $module_data.param02 eq "CHF"}selected{/if} value="CHF">Swiss Franc</option>
<option {if $module_data.param02 eq "CZK"}selected{/if} value="CZK">Czech Koruna</option>
<option {if $module_data.param02 eq "DKK"}selected{/if} value="DKK">Danish Krone</option>
<option {if $module_data.param02 eq "EEK"}selected{/if} value="EEK">Estonian Koruna</option>
<option {if $module_data.param02 eq "EUR"}selected{/if} value="EUR">Euro</option>
<option {if $module_data.param02 eq "GBP"}selected{/if} value="GBP">Pound Sterling</option>
<option {if $module_data.param02 eq "HKD"}selected{/if} value="HKD">Hong Kong Dollar</option>
<option {if $module_data.param02 eq "HUF"}selected{/if} value="HUF">Forint</option>
<option {if $module_data.param02 eq "ILS"}selected{/if} value="ILS">Shekel</option>
<option {if $module_data.param02 eq "ISK"}selected{/if} value="ISK">Iceland Krona</option>
<option {if $module_data.param02 eq "JPY"}selected{/if} value="JPY">Yen</option>
<option {if $module_data.param02 eq "KRW"}selected{/if} value="KRW">South-Korean Won</option>
<option {if $module_data.param02 eq "LVL"}selected{/if} value="LVL">Latvian Lat</option>
<option {if $module_data.param02 eq "MYR"}selected{/if} value="MYR">Malaysian Ringgit</option>
<option {if $module_data.param02 eq "NOK"}selected{/if} value="NOK">Norwegian Krone</option>
<option {if $module_data.param02 eq "NZD"}selected{/if} value="NZD">New Zealand Dollar</option>
<option {if $module_data.param02 eq "PLN"}selected{/if} value="PLN">Zloty</option>
<option {if $module_data.param02 eq "SEK"}selected{/if} value="SEK">Swedish Krona</option>
<option {if $module_data.param02 eq "SGD"}selected{/if} value="SGD">Singapore Dollar</option>
<option {if $module_data.param02 eq "SKK"}selected{/if} value="SKK">Slovak Koruna</option>
<option {if $module_data.param02 eq "THB"}selected{/if} value="THB">Baht</option>
<option {if $module_data.param02 eq "TWD"}selected{/if} value="TWD">New Taiwan Dollar</option>
<option {if $module_data.param02 eq "USD"}selected{/if} value="USD">US Dollar</option>
<option {if $module_data.param02 eq "ZAR"}selected{/if} value="ZAR">South-African Rand</option>
<option {if $module_data.param02 eq "AFA"}selected{/if} value="AFA">Afghani</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_mbookers_language}:</td>
<td>
<select name="param03">
<option value="EN" {if $module_data.param03 eq "EN"}selected{/if}>EN</option>
<option value="DE" {if $module_data.param03 eq "DE"}selected{/if}>DE</option>
<option value="ES" {if $module_data.param03 eq "ES"}selected{/if}>ES</option>
<option value="FR" {if $module_data.param03 eq "FR"}selected{/if}>FR</option>
</select>
</td>
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
