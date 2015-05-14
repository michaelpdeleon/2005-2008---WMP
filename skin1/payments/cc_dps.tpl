{* $Id: cc_dps.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Direct Payment Solution: PX POST</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_dps_username}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_dps_password}:</td>
<td><input type="password" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr><td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param04">
<option value="AUD"{if $module_data.param04 eq "AUD"} selected="selected"{/if}>Australian Dollar</option>
<option value="CAD"{if $module_data.param04 eq "CAD"} selected="selected"{/if}>Canadian Dollar</option>
<option value="CHF"{if $module_data.param04 eq "CHF"} selected="selected"{/if}>Swiss Franc</option>
<option value="DEM"{if $module_data.param04 eq "DEM"} selected="selected"{/if}>Deutsche Mark</option>
<option value="FRF"{if $module_data.param04 eq "FRF"} selected="selected"{/if}>French Franc</option>
<option value="HKD"{if $module_data.param04 eq "HKD"} selected="selected"{/if}>Hong Kong Dollar</option>
<option value="JPY"{if $module_data.param04 eq "JPY"} selected="selected"{/if}>Yen</option>
<option value="NZD"{if $module_data.param04 eq "NZD"} selected="selected"{/if}>New Zealand Dollar</option>
<option value="SGD"{if $module_data.param04 eq "SGD"} selected="selected"{/if}>Singapore Dollar</option>
<option value="USD"{if $module_data.param04 eq "USD"} selected="selected"{/if}>US Dollar</option>
<option value="ZAR"{if $module_data.param04 eq "ZAR"} selected="selected"{/if}>Rand</option>
</select>
</td></tr>
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
