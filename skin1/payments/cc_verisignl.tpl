{* $Id: cc_verisignl.tpl,v 1.7.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>VeriSign Payflow Link</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_verisignl_note|substitute:"http_location":$http_location}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_verisignl_login}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_verisignl_partner}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_verisign_server_being_used}:</td>
<td><select name="param06">
<option value="AU"{if $module_data.param06 eq 'AU'}selected{/if}>{$lng.country_AU}</option>
<option value="US"{if $module_data.param06 eq 'US'}selected{/if}>{$lng.country_US}</option>
</select></td>
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
