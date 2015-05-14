{* $Id: cc_fuse.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Clear Commerce (PayFuse)</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_fuse_name}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_fuse_password}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_fuse_clientid}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_fuse_testserver}:</td>
<td><input type="text" name="param07" size="32" value="{$module_data.param07|escape}" /><br />
{$lng.lbl_cc_fuse_testserver_note}
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_fuse_liveserver}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /><br />
{$lng.lbl_cc_fuse_liveserver_note}
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param08">
<option value="978" {if $module_data.param08 eq "978"}selected{/if}>Euro</option>
<option value="344" {if $module_data.param08 eq "344"}selected{/if}>Hong Kong Dollar</option>
<option value="392" {if $module_data.param08 eq "392"}selected{/if}>Japanese Yen</option>
<option value="826" {if $module_data.param08 eq "826"}selected{/if}>Pound Sterling</option>
<option value="840" {if $module_data.param08 eq "840"}selected{/if}>US Dollar</option>
</select>
</td>
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
