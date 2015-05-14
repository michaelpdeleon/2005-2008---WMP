{* $Id: cc_lynksystems.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Lynk Systems</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_lynksystems_merchantid}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_lynksystems_storeid}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_lynksystems_terminalid}:</td>
<td><input type="text" name="param03" size="24" value="{$module_data.param03|escape}" /></td>
</tr>
<tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td><select name="testmode">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="24" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td colspan="2">
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</td></tr>
</table>
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
