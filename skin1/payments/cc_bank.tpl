{* $Id: cc_bank.tpl,v 1.7.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Bank of America</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_boa_note}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_boa_storeid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_boa_cvv}:</td>
<td>
<select name="param02">
<option value="1"{if $module_data.param02 eq "1"} selected="selected"{/if}>{$lng.lbl_cc_boa_cvv_present}</option>
<option value="2"{if $module_data.param02 eq "2"} selected="selected"{/if}>{$lng.lbl_cc_boa_cvv_unreadable}</option>
<option value="9"{if $module_data.param02 eq "9"} selected="selected"{/if}>{$lng.lbl_cc_boa_cvv_notpresent}</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_boa_key}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
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
