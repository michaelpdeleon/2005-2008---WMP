{* $Id: cc_triple.tpl,v 1.8.2.3 2006/07/17 11:15:52 max Exp $ *}
<h3>Triple Deal</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_td_id}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_td_pm}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_td_day}:</td>
<td><input type="text" name="param07" size="32" value="{$module_data.param07|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option value="ATS"{if $module_data.param02 eq "ATS"} selected="selected"{/if}>Austria, Schilling</option>
<option value="CHF"{if $module_data.param02 eq "CHF"} selected="selected"{/if}>Switzerland, Franc</option>
<option value="DEM"{if $module_data.param02 eq "DEM"} selected="selected"{/if}>Germany, Deutsche Mark</option>
<option value="EUR"{if $module_data.param02 eq "EUR"} selected="selected"{/if}>European Economic and Monetary Union, Euro</option>
<option value="GBP"{if $module_data.param02 eq "GBP"} selected="selected"{/if}>Great Britain, Pound Sterling</option>
<option value="ITL"{if $module_data.param02 eq "ITL"} selected="selected"{/if}>Italy, Lira</option>
<option value="USD"{if $module_data.param02 eq "USD"} selected="selected"{/if}>United States of America, US Dollar</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_td_language}:</td>
<td>
<select name="param03">
<option value="nl"{if $module_data.param03 eq "nl"} selected="selected"{/if}>NL
<option value="en"{if $module_data.param03 eq "en"} selected="selected"{/if}>EN
<option value="de"{if $module_data.param03 eq "de"} selected="selected"{/if}>DE
<option value="fr"{if $module_data.param03 eq "fr"} selected="selected"{/if}>FR
<option value="es"{if $module_data.param03 eq "es"} selected="selected"{/if}>ES
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}
</select>
</td>
</tr>


<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
