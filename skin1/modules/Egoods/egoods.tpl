{* $Id: egoods.tpl,v 1.12.2.2 2006/07/11 08:39:29 svowl Exp $ *}
<tr> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[distribution]" /></td>{/if}
	<td class="FormButton">{$lng.lbl_esd_distribution}:</td>
	<td>
{include file=main/popup_files_js.tpl}
    <input type="hidden" name="distribution_filename" />
    <input type="text" name="distribution" size="24" value="{$product.distribution}" />
    <input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: popup_files('modifyform.distribution_filename', 'modifyform.distribution');" />
	</td>
</tr>
