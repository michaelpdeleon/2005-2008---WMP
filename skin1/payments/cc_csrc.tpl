{* $Id: cc_csrc.tpl,v 1.7.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>CyberSource</h3>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_cybersource_note}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_csrc_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_csrc_icspath}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_csrc_server}</td>
<td><input type="text" name="param03" size="24" value="{$module_data.param03|escape}" />:<input type="text" name="param04" size="4" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param05">
<option value="USD"{if $module_data.param05 eq "USD"} selected="selected"{/if}>US Dollars</option>
<option value="GBP"{if $module_data.param05 eq "GBP"} selected="selected"{/if}>Sterling</option>
<option value="EUR"{if $module_data.param05 eq "EUR"} selected="selected"{/if}>Euro</option>
</select></td></tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
