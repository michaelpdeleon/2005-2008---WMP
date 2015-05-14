{* $Id: cc_hsbc.tpl,v 1.9.2.3 2006/07/17 11:15:52 max Exp $ *}
{include file="location.tpl" last_location="Payment gateway settings"}
<h3>HSBC Secure E-Payment Service</h3>
{$lng.txt_cc_configure_top_text}
<p />
<font color="red">{$lng.lbl_warning}:</font><br />
{$lng.txt_hsbc_note}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_hsbc_id}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_hsbc_key}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
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
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param04">
<option value="978" {if $module_data.param04 eq "978"}selected{/if}>Euro</option>
<option value="344" {if $module_data.param04 eq "344"}selected{/if}>Hong Kong Dollar</option>
<option value="392" {if $module_data.param04 eq "392"}selected{/if}>Japanese Yen</option>
<option value="826" {if $module_data.param04 eq "826"}selected{/if}>Pound Sterling</option>
<option value="840" {if $module_data.param04 eq "840"}selected{/if}>US Dollar</option>
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
