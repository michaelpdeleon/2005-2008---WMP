{* $Id: cc_intellipaycom.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>IntelliPay: ExpertLink</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_intellipaycom_login}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_intellipaycom_password}:</td>
<td><input type="password" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_intellipaycom_reject}:</td>
<td>
<select name="param04">
<option value=N{if $module_data.param04 eq "N"} selected="selected"{/if}>{$lng.lbl_cc_intellipaycom_reject_no}</option>
<option value=Y{if $module_data.param04 eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_intellipaycom_reject_yes}</option>
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
