{* $Id: cc_ideb.tpl,v 1.7.2.3 2006/07/17 11:15:52 max Exp $ *}
<h3>iDeb</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_ideb_client}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_ideb_ini}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_ideb_prj}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="752"{if $module_data.param04 eq "752"} selected="selected"{/if}>Swedish Krona</option>
<option value="840"{if $module_data.param04 eq "840"} selected="selected"{/if}>US Dollars</option>
<option value="978"{if $module_data.param04 eq "978"} selected="selected"{/if}>Euro</option>
</select></td></tr>

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
