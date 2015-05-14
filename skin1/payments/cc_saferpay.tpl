{* $Id: cc_saferpay.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>Saferpay</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_saferpay_accountid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param02">
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
<td>{$lng.lbl_cc_saferpay_language}:</td>
<td><select name="param05">
<option value="2055"{if $module_data.param05 eq "2055"} selected="selected"{/if}>deutsch</option>
<option value="1033"{if $module_data.param05 eq "1033"} selected="selected"{/if}>english</option>
<option value="4108"{if $module_data.param05 eq "4108"} selected="selected"{/if}>francais</option>
<option value="1040"{if $module_data.param05 eq "1040"} selected="selected"{/if}>italiano</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_saferpay_binpath}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /><br />
{$lng.lbl_cc_saferpay_binpath_note}
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_saferpay_cfgpath}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /><br />
{$lng.lbl_cc_saferpay_cfgpath_note}
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
