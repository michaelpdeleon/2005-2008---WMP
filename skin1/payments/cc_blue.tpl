{* $Id: cc_blue.tpl,v 1.7.2.3 2006/07/11 08:39:36 svowl Exp $ *}
<h3>BluePay</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
	<td>{$lng.lbl_cc_bluepay_merchant}:</td>
	<td>
<input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /><br />
{$lng.txt_cc_bluepay_merchant}
	</td>
</tr>
<tr>
	<td>{$lng.lbl_cc_bluepay_skey}:</td>
	<td>
<input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /><br/>
{$lng.txt_cc_bluepay_skey}
	</td>
</tr>
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
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
