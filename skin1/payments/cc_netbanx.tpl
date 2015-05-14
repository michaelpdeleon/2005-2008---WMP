{* $Id: cc_netbanx.tpl,v 1.7.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>NetBanx</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<img src="{$ImagesDir}/netbanxlogo.gif" border="0" width="125" height="63" />
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_netbanx_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_netbanx_actionurl}:</td>
<td><input type="password" name="param02" size="32" value="{$module_data.param02|escape}" /><br />
{$lng.lbl_cc_netbanx_actionurl_note}
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param04">
<option value="ATS"{if $module_data.param04 eq "ATS"} selected="selected"{/if}>Austrian Shilling
<option value="AUD"{if $module_data.param04 eq "AUD"} selected="selected"{/if}>Australian Dollar
<option value="BEF"{if $module_data.param04 eq "BEF"} selected="selected"{/if}>Belgian franc
<option value="CAD"{if $module_data.param04 eq "CAD"} selected="selected"{/if}>Canadian Dollar
<option value="CHF"{if $module_data.param04 eq "CHF"} selected="selected"{/if}>Swiss Franc
<option value="CZK"{if $module_data.param04 eq "CZK"} selected="selected"{/if}>Czech Koruna
<option value="DEM"{if $module_data.param04 eq "DEM"} selected="selected"{/if}>German mark
<option value="DKK"{if $module_data.param04 eq "DKK"} selected="selected"{/if}>Danish Kroner
<option value="ESP"{if $module_data.param04 eq "ESP"} selected="selected"{/if}>Spanish Peseta
<option value="EUR"{if $module_data.param04 eq "EUR"} selected="selected"{/if}>EURO
<option value="FIM"{if $module_data.param04 eq "FIM"} selected="selected"{/if}>Finnish Markka
<option value="FRF"{if $module_data.param04 eq "FRF"} selected="selected"{/if}>French franc
<option value="GBP"{if $module_data.param04 eq "GBP"} selected="selected"{/if}>British pound
<option value="HKD"{if $module_data.param04 eq "HKD"} selected="selected"{/if}>Hong Kong Dollar
<option value="HUF"{if $module_data.param04 eq "HUF"} selected="selected"{/if}>Hungarian Forint
<option value="IEP"{if $module_data.param04 eq "IEP"} selected="selected"{/if}>Irish Punt
<option value="ILS"{if $module_data.param04 eq "ILS"} selected="selected"{/if}>New Shekel
<option value="ITL"{if $module_data.param04 eq "ITL"} selected="selected"{/if}>Italian Lira
<option value="JPY"{if $module_data.param04 eq "JPY"} selected="selected"{/if}>Japanese Yen
<option value="LTL"{if $module_data.param04 eq "LTL"} selected="selected"{/if}>Litas
<option value="LUF"{if $module_data.param04 eq "LUF"} selected="selected"{/if}>Luxembourg franc
<option value="LVL"{if $module_data.param04 eq "LVL"} selected="selected"{/if}>Lats Letton
<option value="MXN"{if $module_data.param04 eq "MXN"} selected="selected"{/if}>Peso
<option value="NLG"{if $module_data.param04 eq "NLG"} selected="selected"{/if}>Dutch Guilders
<option value="NOK"{if $module_data.param04 eq "NOK"} selected="selected"{/if}>Norwegian Kroner
<option value="NZD"{if $module_data.param04 eq "NZD"} selected="selected"{/if}>New Zealand Dollar
<option value="PLN"{if $module_data.param04 eq "PLN"} selected="selected"{/if}>Polish Zloty
<option value="PTE"{if $module_data.param04 eq "PTE"} selected="selected"{/if}>Portuguese Escudo
<option value="RUR"{if $module_data.param04 eq "RUR"} selected="selected"{/if}>Rouble
<option value="SEK"{if $module_data.param04 eq "SEK"} selected="selected"{/if}>Swedish Krone
<option value="SGD"{if $module_data.param04 eq "SGD"} selected="selected"{/if}>Singapore Dollar
<option value="SKK"{if $module_data.param04 eq "SKK"} selected="selected"{/if}>Couronne Slovaque
<option value="THB"{if $module_data.param04 eq "THB"} selected="selected"{/if}>Thai Bath
<option value="TRL"{if $module_data.param04 eq "TRL"} selected="selected"{/if}>Lire Turque
<option value="USD"{if $module_data.param04 eq "USD"} selected="selected"{/if}>US Dollar
<option value="ZAR"{if $module_data.param04 eq "ZAR"} selected="selected"{/if}>South African Rand   
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
