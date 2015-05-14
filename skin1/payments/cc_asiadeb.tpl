{* $Id: cc_asiadeb.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>AsiaDebit</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_asiadeb_note|substitute:"http_location":$http_location}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_asiadeb_shopid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param02">
<option value="SEK"{if $module_data.param02 eq "SEK"} selected="selected"{/if}>Swedish kroner
<option value="DKK"{if $module_data.param02 eq "DKK"} selected="selected"{/if}>Danish kroner
<option value="USD"{if $module_data.param02 eq "USD"} selected="selected"{/if}>US dollar
<option value="EUR"{if $module_data.param02 eq "EUR"} selected="selected"{/if}>Euro
<option value="GBP"{if $module_data.param02 eq "GBP"} selected="selected"{/if}>British pound
<option value="ATS"{if $module_data.param02 eq "ATS"} selected="selected"{/if}>Austria Schilling
<option value="BEF"{if $module_data.param02 eq "BEF"} selected="selected"{/if}>Belgium Franc
<option value="FIM"{if $module_data.param02 eq "FIM"} selected="selected"{/if}>Finland Markka
<option value="IEP"{if $module_data.param02 eq "IEP"} selected="selected"{/if}>Ireland Punt
<option value="ITL"{if $module_data.param02 eq "ITL"} selected="selected"{/if}>Italy Lira
<option value="LUF"{if $module_data.param02 eq "LUF"} selected="selected"{/if}>Luxembourg Francs
<option value="NLG"{if $module_data.param02 eq "NLG"} selected="selected"{/if}>Netherlands (Dutch) Guilders
<option value="NOK"{if $module_data.param02 eq "NOK"} selected="selected"{/if}>Norway Kroner
<option value="PTE"{if $module_data.param02 eq "PTE"} selected="selected"{/if}>Portugal Escudo
<option value="ESP"{if $module_data.param02 eq "ESP"} selected="selected"{/if}>Spain Pesetas
<option value="CHF"{if $module_data.param02 eq "CHF"} selected="selected"{/if}>Switzerland Francs
<option value="THB"{if $module_data.param02 eq "THB"} selected="selected"{/if}>Thailand Baht
<option value="SGD"{if $module_data.param02 eq "SGD"} selected="selected"{/if}>Singapore Dollars
<option value="HKD"{if $module_data.param02 eq "HKD"} selected="selected"{/if}>Hong Kong Dollars
<option value="MYR"{if $module_data.param02 eq "MYR"} selected="selected"{/if}>Malaysia Ringgit
<option value="JPY"{if $module_data.param02 eq "JPY"} selected="selected"{/if}>Japan Yen
<option value="CAD"{if $module_data.param02 eq "CAD"} selected="selected"{/if}>Canada Dollars
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
