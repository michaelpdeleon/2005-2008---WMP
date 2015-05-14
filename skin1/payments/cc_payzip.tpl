{* $Id: cc_payzip.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>PayZip.Net: Web2Web</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_payzip_note|substitute:"http_location":$http_location}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_payzip_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_payzip_password}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="USD"{if $module_data.param04 eq "USD"} selected="selected"{/if}>US dollar
<option value="EUR"{if $module_data.param04 eq "EUR"} selected="selected"{/if}>Euro
<option value="GBP"{if $module_data.param04 eq "GBP"} selected="selected"{/if}>British pound
<option value="THB"{if $module_data.param04 eq "THB"} selected="selected"{/if}>Thailand Baht
<option value="SGD"{if $module_data.param04 eq "SGD"} selected="selected"{/if}>Singapore Dollars
<option value="HKD"{if $module_data.param04 eq "HKD"} selected="selected"{/if}>Hong Kong Dollars
<option value="JPY"{if $module_data.param04 eq "JPY"} selected="selected"{/if}>Japan Yen
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
