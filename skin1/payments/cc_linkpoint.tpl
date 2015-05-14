{* $Id: cc_linkpoint.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>LinkPoint (CardService International)</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_linkpoint_desc}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_linkpoint_storename}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_linkpoint_hostport}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" />:<input type="text" name="param07" size="4" value="{$module_data.param07|escape}" /><br />
{$lng.lbl_cc_linkpoint_hostport_note}</td>
</tr>

<tr>
<td>{$lng.lbl_cc_linkpoint_certpath}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="A" {if $module_data.testmode eq "A"}selected{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="D" {if $module_data.testmode eq "D"}selected{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="N" {if $module_data.testmode eq "N"}selected{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_linkpoint_cvm}:</td>
<td>
<select name="param04">
<option value="not_provided" {if $module_data.param04 eq "not_provided"}selected{/if}>{$lng.lbl_cc_linkpoint_cvm_not_provided}</option>
<option value="provided" {if $module_data.param04 eq "provided"}selected{/if}>{$lng.lbl_cc_linkpoint_cvm_provided}</option>
<option value="illegible" {if $module_data.param04 eq "illegible"}selected{/if}>{$lng.lbl_cc_linkpoint_cvm_illegible}</option>
<option value="not_present" {if $module_data.param04 eq "not_present"}selected{/if}>{$lng.lbl_cc_linkpoint_cvm_not_present}</option>
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
