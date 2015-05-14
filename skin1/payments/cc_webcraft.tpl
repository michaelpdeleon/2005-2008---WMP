{* $Id: cc_webcraft.tpl,v 1.7.2.3 2006/07/17 11:15:52 max Exp $ *}
<h3>The Webcraft Card Payment Gateway</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_wcraft_user}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_wcraft_pass}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>

<tr valign="top">
<td>{$lng.lbl_cc_wcraft_ver}:</td>
<td>
<select name="param04">
<option value="1.1.2"{if $module_data.param04 eq "1.1.2"} selected="selected"{/if}>1.1.2
<option value="1.1.3"{if $module_data.param04 eq "1.1.3"} selected="selected"{/if}>1.1.3
</select>
<br />{$lng.lbl_cc_wcraft_note}
</td>
</tr>

<tr valign="top">
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="A" {if $module_data.testmode eq "A"}selected{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="D" {if $module_data.testmode eq "D"}selected{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="N" {if $module_data.testmode eq "N"}selected{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>

<tr valign="top">
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param09" size="40" maxlength=255 value="{$module_data.param09|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
