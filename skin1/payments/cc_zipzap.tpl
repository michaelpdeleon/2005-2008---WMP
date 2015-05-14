{* $Id: cc_zipzap.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>ZipZap</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_zipzap_opsid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param03">
<option value="554"{if $module_data.param03 eq "554"} selected="selected"{/if}>NZD - New Zealand Dollar
<option value="036"{if $module_data.param03 eq "036"} selected="selected"{/if}>AUD - Australian Dollar
<option value="756"{if $module_data.param03 eq "756"} selected="selected"{/if}>CHF - Swiss Franc
<option value="826"{if $module_data.param03 eq "826"} selected="selected"{/if}>GBP - Pound Sterling
<option value="124"{if $module_data.param03 eq "124"} selected="selected"{/if}>CAD - Canadian Dollar
<option value="344"{if $module_data.param03 eq "344"} selected="selected"{/if}>HKD - Hong Kong Dollar
<option value="392"{if $module_data.param03 eq "392"} selected="selected"{/if}>JPY - Yen
<option value="702"{if $module_data.param03 eq "702"} selected="selected"{/if}>SGD - Singapore Dollar
<option value="710"{if $module_data.param03 eq "710"} selected="selected"{/if}>ZAR - Rand
<option value="840"{if $module_data.param03 eq "840"} selected="selected"{/if}>USD - US Dollar
<option value="978"{if $module_data.param03 eq "978"} selected="selected"{/if}>EUR - European Currency Unit
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
