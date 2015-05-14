{* $Id: product_modify.tpl,v 1.12 2006/04/07 06:00:35 max Exp $ *}
{foreach from=$extra_fields item=ef}
<tr> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[efields][{$ef.fieldid}]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$ef.field}</td>
	<td><input type="text" name="efields[{$ef.fieldid}]" size="24" value="{if $ef.is_value eq 'Y'}{$ef.field_value|escape:"html"}{else}{$ef.value|escape:"html"}{/if}" /></td>
</tr>
{/foreach}
