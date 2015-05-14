{* $Id: ch_pbcxml.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>PayByCheck (XML)</h3>
{$lng.txt_ch_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_ch_paybycheck_procid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_ch_paybycheck_password}:</td>
<td><input type="password" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_paybycheck_mid}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param04">
<option value="USD"{if $module_data.param04 eq "USD"} selected="selected"{/if}>US Dollars
<!--<option value="CDN"{if $module_data.param04 eq "CDN"} selected="selected"{/if}>Canadian Dollars-->
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>


<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param09" size="32" value="{$module_data.param09|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_ch_settings content=$smarty.capture.dialog extra='width="100%"'}
