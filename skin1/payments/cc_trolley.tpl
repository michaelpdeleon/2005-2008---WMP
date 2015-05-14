{* $Id: cc_trolley.tpl,v 1.7.2.3 2006/07/17 11:15:52 max Exp $ *}
<h3>Trolley Gateway</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_trol_id}:</td>
<td><input type="text" name="param04" size="36" value="{$module_data.param04|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_trol_pass}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" maxlength=30 /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param05">
<option value="EUR"{if $module_data.param05 eq "EUR"} selected="selected"{/if}>Euro (Europe)
<option value="GBP"{if $module_data.param05 eq "GBP"} selected="selected"{/if}>Pound Sterling (United Kingdom)
<option value="USD"{if $module_data.param05 eq "USD"} selected="selected"{/if}>US Dollar (United States)
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param09" size="24" value="{$module_data.param09|escape}" maxlength=30 /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
