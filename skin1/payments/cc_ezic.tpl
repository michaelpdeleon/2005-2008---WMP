{* $Id: cc_ezic.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Ezic. Direct mode V3.x</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_ezic_note|substitute:"SERVER_ADDR":$smarty.env.SERVER_ADDR}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_ezic_accountid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_ezic_siteid}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
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
