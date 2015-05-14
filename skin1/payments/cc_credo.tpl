{* $Id: cc_credo.tpl,v 1.4.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Credomatic</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_credo_path}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_credo_cvv}:</td>
<td><select name="param03"><option value="">{$lng.lbl_no}<option value="Y"{if $module_data.param03 eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</select></td>
</tr>

<tr>
<td>{$lng.lbl_cc_credo_avs}:</td>
<td><select name="param04"><option value="">{$lng.lbl_no}<option value="Y"{if $module_data.param04 eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</select></td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
