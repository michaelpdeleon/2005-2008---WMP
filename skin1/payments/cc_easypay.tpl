{* $Id: cc_easypay.tpl,v 1.7.2.3 2006/07/17 11:15:52 max Exp $ *}
<h3>EasyPay</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_easypay_mid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option {if $module_data.param02 eq "SGD"}selected{/if} value="SGD">Singapore Dollar</option>
<option {if $module_data.param02 eq "MYR"}selected{/if} value="MYR">Malaysian Ringitt</option>
<option {if $module_data.param02 eq "USD"}selected{/if} value="USD">US Dollar</option>
<option {if $module_data.param02 eq "AUD"}selected{/if} value="AUD">Australian Dollar</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="Y" {if $module_data.testmode eq "Y"}selected{/if}>{$lng.lbl_cc_testlive_test}
<option value="N" {if $module_data.testmode eq "N"}selected{/if}>{$lng.lbl_cc_testlive_live}
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
