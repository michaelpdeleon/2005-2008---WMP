{* $Id: cc_paybox.tpl,v 1.7.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>PayBox Service</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_paybox_sitenum}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_paybox_rank}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param03" size="24" value="{$module_data.param03|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="250"{if $module_data.param04 eq "250"} selected="selected"{/if}>Franc</option>
<option value="978"{if $module_data.param04 eq "978"} selected="selected"{/if}>Euro</option>
</select></td>
</tr>
<tr>
<td>{$lng.lbl_cc_paybox_language}:</td>
<td><select name="param05">
<option value="FRA"{if $module_data.param05 eq "FRA"} selected="selected"{/if}>FRA</option>
<option value="GBR"{if $module_data.param05 eq "GBR"} selected="selected"{/if}>GBR</option>
<option value="DEU"{if $module_data.param05 eq "DEU"} selected="selected"{/if}>DEU</option>
</select></td>
</tr>
<tr>
<td colspan="2">
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</td></tr>
</table>
</form>
</center>

<br />

{$lng.txt_cc_paybox_note}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}

