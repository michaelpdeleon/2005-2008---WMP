<h3>iDEAL Payment System</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">

<tr>
	<td>{$lng.lbl_ideal_basic_mid}:</td>
	<td><input type="text" name="param04" size="24" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
	<td>{$lng.lbl_ideal_basic_skey}:</td>
	<td><input type="text" name="param03" size="48" value="{$module_data.param03|escape}" /></td>
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
	<td><input type="text" name="param05" size="24" value="{$module_data.param05|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra="width=100%"}
