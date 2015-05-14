{* $Id: cc_netbilling.tpl,v 1.10.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>Netbilling</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_netbilling_account}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_netbilling_sitetag}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
<br /><center>{$lng.txt_vbv_admin_note|substitute:"ImagesDir":$ImagesDir}</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
