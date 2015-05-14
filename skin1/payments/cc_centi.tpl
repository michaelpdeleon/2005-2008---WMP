{* $Id: cc_centi.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>CentiPaid</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_centi_login}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_centi_language}:</td>
<td>
<select name="param02">
<option value="ar"{if $module_data.param02 eq "ar"} selected="selected"{/if}>Arabic
<option value="en"{if $module_data.param02 eq "en"} selected="selected"{/if}>English
<option value="es"{if $module_data.param02 eq "es"} selected="selected"{/if}>Spanish
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
