{* $Id: cc_vaultx.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>VaultX</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_vaultx_clientid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_vaultx_gateways}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_vaultx_gatewaysport}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_vaultx_clientcert}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_vaultx_certpass}:</td>
<td><input type="password" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_vaultx_binary}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param07" size="32" value="{$module_data.param07|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
