{* $Id: cc_vp.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>Velocity Pay</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_vp_merchantid}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_vp_merchantpass}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_vp_formurl}:</td>
<td><input type="text" name="param03" size="24" value="{$module_data.param03|escape}" /></td>
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
